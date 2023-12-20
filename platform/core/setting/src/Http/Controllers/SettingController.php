<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Exceptions\LicenseIsAlreadyActivatedException;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Language;
use Botble\JsValidation\Facades\JsValidator;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Http\Requests\EmailSettingRequest;
use Botble\Setting\Http\Requests\EmailTemplateRequest;
use Botble\Setting\Http\Requests\LicenseSettingRequest;
use Botble\Setting\Http\Requests\MediaSettingRequest;
use Botble\Setting\Http\Requests\ResetEmailTemplateRequest;
use Botble\Setting\Http\Requests\SendTestEmailRequest;
use Botble\Setting\Http\Requests\SettingRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ProcessUtils;
use Throwable;

class SettingController extends BaseController
{
    public function getOptions()
    {
        PageTitle::setTitle(trans('core/setting::setting.title'));

        Assets::addScripts(['jquery-validation', 'form-validation'])
            ->addScriptsDirectly([
                'vendor/core/core/setting/js/setting.js',
                'vendor/core/core/setting/js/verify-license.js',
            ])
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css');

        Assets::usingVueJS();

        $jsValidation = JsValidator::formRequest(SettingRequest::class);

        return view('core/setting::index', compact('jsValidation'));
    }

    public function postEdit(SettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings(
            $request->except([
                '_token',
                'locale',
                'default_admin_theme',
                'admin_locale_direction',
            ])
        );

        $locale = $request->input('locale');
        if ($locale && array_key_exists($locale, Language::getAvailableLocales())) {
            session()->put('site-locale', $locale);
        }

        $isDemoModeEnabled = BaseHelper::hasDemoModeEnabled();

        if (! $isDemoModeEnabled) {
            setting()->set('locale', $locale);
        }

        $adminTheme = $request->input('default_admin_theme');
        if ($adminTheme != setting('default_admin_theme')) {
            session()->put('admin-theme', $adminTheme);
        }

        if (! $isDemoModeEnabled) {
            setting()->set('default_admin_theme', $adminTheme);
        }

        $adminLocalDirection = $request->input('admin_locale_direction');
        if ($adminLocalDirection != setting('admin_locale_direction')) {
            session()->put('admin_locale_direction', $adminLocalDirection);
        }

        if (! $isDemoModeEnabled) {
            setting()->set('admin_locale_direction', $adminLocalDirection);
            setting()->save();
        }

        return $response
            ->setPreviousUrl(route('settings.options'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    protected function saveSettings(array $data): void
    {
        foreach ($data as $settingKey => $settingValue) {
            if (is_array($settingValue)) {
                $settingValue = json_encode(array_filter($settingValue));
            }

            setting()->set($settingKey, (string)$settingValue);
        }

        setting()->save();
    }

    public function getEmailConfig()
    {
        PageTitle::setTitle(trans('core/base::layouts.setting_email'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js')
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css')
            ->addScripts(['jquery-validation', 'form-validation']);

        $jsValidation = JsValidator::formRequest(EmailSettingRequest::class);

        return view('core/setting::email', compact('jsValidation'));
    }

    public function postEditEmailConfig(EmailSettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings($request->except(['_token']));

        return $response
            ->setPreviousUrl(route('settings.email'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getEditEmailTemplate(string $type, string $module, string $template)
    {
        PageTitle::setTitle(trans(config($type . '.' . $module . '.email.templates.' . $template . '.title', '')));

        Assets::addStylesDirectly([
            'vendor/core/core/base/libraries/codemirror/lib/codemirror.css',
            'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.css',
            'vendor/core/core/setting/css/setting.css',
        ])
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/codemirror/lib/codemirror.js',
                'vendor/core/core/base/libraries/codemirror/lib/css.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/anyword-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
                'vendor/core/core/setting/js/setting.js',
            ]);

        $emailContent = get_setting_email_template_content($type, $module, $template);
        $emailSubject = get_setting_email_subject($type, $module, $template);
        $pluginData = [
            'type' => $type,
            'name' => $module,
            'template_file' => $template,
        ];

        return view('core/setting::email-template-edit', compact('emailContent', 'emailSubject', 'pluginData'));
    }

    public function postStoreEmailTemplate(EmailTemplateRequest $request, BaseHttpResponse $response)
    {
        if ($request->has('email_subject_key')) {
            setting()
                ->set($request->input('email_subject_key'), $request->input('email_subject'))
                ->save();
        }

        $templatePath = get_setting_email_template_path($request->input('module'), $request->input('template_file'));

        BaseHelper::saveFileData($templatePath, $request->input('email_content'), false);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postResetToDefault(ResetEmailTemplateRequest $request, BaseHttpResponse $response)
    {
        Setting::delete([$request->input('email_subject_key')]);

        $templatePath = get_setting_email_template_path($request->input('module'), $request->input('template_file'));

        if (File::exists($templatePath)) {
            File::delete($templatePath);
        }

        $shouldBeCleanedDirectories = [
            File::dirname($templatePath),
            storage_path('app/email-templates'),
        ];

        foreach ($shouldBeCleanedDirectories as $shouldBeCleanedDirectory) {
            if (File::isDirectory($shouldBeCleanedDirectory) && File::isEmptyDirectory($shouldBeCleanedDirectory)) {
                File::deleteDirectory($shouldBeCleanedDirectory);
            }
        }

        return $response->setMessage(trans('core/setting::setting.email.reset_success'));
    }

    public function postChangeEmailStatus(Request $request, BaseHttpResponse $response)
    {
        $request->validate(['key' => 'string', 'value' => 'in:0,1']);

        setting()
            ->set($request->input('key'), $request->boolean('value'))
            ->save();

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postSendTestEmail(BaseHttpResponse $response, SendTestEmailRequest $request)
    {
        try {
            EmailHandler::send(
                file_get_contents(core_path('setting/resources/email-templates/test.tpl')),
                'Test',
                $request->input('email'),
                [],
                true
            );

            return $response->setMessage(trans('core/setting::setting.test_email_send_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getMediaSetting(MediaFolderInterface $mediaFolderRepository)
    {
        PageTitle::setTitle(trans('core/setting::setting.media.title'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js')
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css')
            ->addScripts(['jquery-validation', 'form-validation']);

        $folderIds = json_decode((string)setting('media_folders_can_add_watermark'), true);

        $folders = $mediaFolderRepository->pluck('name', 'id', ['parent_id' => 0]);

        $jsValidation = JsValidator::formRequest(MediaSettingRequest::class);

        return view('core/setting::media', compact('folders', 'folderIds', 'jsValidation'));
    }

    public function postEditMediaSetting(MediaSettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings($request->except(['_token']));

        return $response
            ->setPreviousUrl(route('settings.media'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getVerifyLicense(Request $request, Core $core, BaseHttpResponse $response)
    {
        if ($request->expectsJson() && ! $core->checkConnection()) {
            return response()->json([
                'message' => __('Your server is not connected to the internet.'),
            ], 400);
        }

        $invalidMessage = 'Your license is invalid. Please activate your license!';

        $licenseFilePath = $core->getLicenseFilePath();

        if (! File::exists($licenseFilePath)) {
            return $response->setError()->setMessage($invalidMessage);
        }

        try {
            if (! $core->verifyLicense(true)) {
                return $response->setError()->setMessage($invalidMessage);
            }

            $activatedAt = Carbon::createFromTimestamp(filectime($core->getLicenseFilePath()));
            $message = 'Your license is activated.';
        } catch (Throwable $exception) {
            $activatedAt = Carbon::now();
            $message = $exception->getMessage();
        }

        $data = [
            'activated_at' => $activatedAt->format('M d Y'),
            'licensed_to' => setting('licensed_to'),
        ];

        return $response->setMessage($message)->setData($data);
    }

    public function activateLicense(LicenseSettingRequest $request, BaseHttpResponse $response, Core $core)
    {
        $buyer = $request->input('buyer');
        if (filter_var($buyer, FILTER_VALIDATE_URL)) {
            $buyer = explode('/', $buyer);
            $username = end($buyer);

            return $response
                ->setError()
                ->setMessage(sprintf('Envato username must not a URL. Please try with username "%s"!', $username));
        }

        $purchasedCode = $request->input('purchase_code');

        try {
            if (! $core->activateLicense($purchasedCode, $buyer)) {
                return $response->setError()->setMessage('Your license is invalid.');
            }

            $data = $this->saveActivatedLicense($core, $buyer);

            return $response->setMessage('Your license has been activated successfully!')->setData($data);
        } catch (LicenseIsAlreadyActivatedException) {
            try {
                $core->revokeLicense($purchasedCode, $buyer);

                if (! $core->activateLicense($purchasedCode, $buyer)) {
                    return $response->setError()->setMessage('Your license is invalid.');
                }

                $data = $this->saveActivatedLicense($core, $buyer);

                return $response
                    ->setMessage('Your license has been activated successfully and the license on the previous domain has been revoked!')
                    ->setData($data);
            } catch (LicenseIsAlreadyActivatedException) {
                return $response
                    ->setError()
                    ->setMessage('Exceeded maximum number of activations, please contact our support to reset your license.');
            }
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deactivateLicense(BaseHttpResponse $response, Core $core)
    {
        try {
            $core->deactivateLicense();

            Setting::delete(['licensed_to']);

            return $response->setMessage('Deactivated license successfully!');
        } catch (Throwable $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    public function resetLicense(LicenseSettingRequest $request, BaseHttpResponse $response, Core $core)
    {
        try {
            if (! $core->revokeLicense($request->input('purchase_code'), $request->input('buyer'))) {
                return $response->setError()->setMessage('Could not reset your license.');
            }

            Setting::delete(['licensed_to']);

            return $response->setMessage('Your license has been reset successfully.');
        } catch (Throwable $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    public function generateThumbnails(MediaFileInterface $fileRepository, BaseHttpResponse $response)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $files = $fileRepository->allBy([], [], ['url', 'mime_type', 'folder_id']);

        $errors = [];

        foreach ($files as $file) {
            try {
                RvMedia::generateThumbnails($file);
            } catch (Exception) {
                $errors[] = $file->url;
            }
        }

        $errors = array_unique($errors);

        $errors = array_map(function ($item) {
            return [$item];
        }, $errors);

        if ($errors) {
            return $response
                ->setError()
                ->setMessage(trans('core/setting::setting.generate_thumbnails_error', ['count' => count($errors)]));
        }

        return $response->setMessage(trans('core/setting::setting.generate_thumbnails_success', ['count' => count($files)]));
    }

    public function previewEmailTemplate(Request $request, string $type, string $module, string $template)
    {
        $emailHandler = EmailHandler::setModule($module)
            ->setType($type)
            ->setTemplate($template);

        $variables = $emailHandler->getVariables($type, $module, $template);

        $coreVariables = $emailHandler->getCoreVariables();

        Arr::forget($variables, array_keys($coreVariables));

        $inputData = $request->only(array_keys($variables));

        if (! empty($inputData)) {
            foreach ($inputData as $key => $value) {
                $inputData[BaseHelper::stringify($key)] = BaseHelper::clean(BaseHelper::stringify($value));
            }
        }

        $routeParams = [$type, $module, $template];

        $backUrl = route('setting.email.template.edit', $routeParams);

        $iframeUrl = route('setting.email.preview.iframe', $routeParams);

        return view(
            'core/setting::preview-email',
            compact('variables', 'inputData', 'backUrl', 'iframeUrl')
        );
    }

    public function previewEmailTemplateIframe(Request $request, string $type, string $module, string $template)
    {
        $emailHandler = EmailHandler::setModule($module)
            ->setType($type)
            ->setTemplate($template);

        $variables = $emailHandler->getVariables($type, $module, $template);

        $coreVariables = $emailHandler->getCoreVariables();

        Arr::forget($variables, array_keys($coreVariables));

        $inputData = $request->only(array_keys($variables));

        foreach ($variables as $key => $variable) {
            if (! isset($inputData[$key])) {
                $inputData[$key] = '{{ ' . $key . ' }}';
            } else {
                $inputData[$key] = BaseHelper::clean(BaseHelper::stringify($inputData[$key]));
            }
        }

        $emailHandler->setVariableValues($inputData);

        $content = get_setting_email_template_content($type, $module, $template);

        $content = $emailHandler->prepareData($content);

        return BaseHelper::clean($content);
    }

    public function cronjob(): View
    {
        PageTitle::setTitle(trans('core/setting::setting.cronjob.name'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js');

        $command = sprintf(
            '* * * * * cd %s && %s >> /dev/null 2>&1',
            BaseHelper::hasDemoModeEnabled() ? 'path-to-your-project' : ProcessUtils::escapeArgument(base_path()),
            Application::formatCommandString('schedule:run')
        );

        $lastRunAt = Setting::get('cronjob_last_run_at');

        if ($lastRunAt) {
            $lastRunAt = Carbon::parse($lastRunAt);
        }

        return view('core/setting::cronjob', compact('command', 'lastRunAt'));
    }

    protected function saveActivatedLicense(Core $core, string $buyer): array
    {
        setting()
            ->set(['licensed_to' => $buyer])
            ->save();

        $activatedAt = Carbon::createFromTimestamp(filectime($core->getLicenseFilePath()));

        return [
            'activated_at' => $activatedAt->format('M d Y'),
            'licensed_to' => $buyer,
        ];
    }
}
