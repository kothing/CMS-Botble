<?php

namespace Botble\Language\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Language;
use Botble\Language\Facades\Language as LanguageFacade;
use Botble\Language\Http\Requests\LanguageRequest;
use Botble\Language\LanguageManager;
use Botble\Language\Models\Language as LanguageModel;
use Botble\Language\Models\LanguageMeta;
use Botble\Language\Repositories\Interfaces\LanguageInterface;
use Botble\Language\Repositories\Interfaces\LanguageMetaInterface;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Botble\Translation\Manager;
use Botble\Widget\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Throwable;

class LanguageController extends BaseController
{
    public function __construct(
        protected LanguageInterface $languageRepository,
        protected LanguageMetaInterface $languageMetaRepository
    ) {
    }

    public function index()
    {
        PageTitle::setTitle(trans('plugins/language::language.name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/language/js/language.js']);

        $languages = Language::getListLanguages();
        $flags = Language::getListLanguageFlags();
        $activeLanguages = $this->languageRepository->all();

        return view('plugins/language::index', compact('languages', 'flags', 'activeLanguages'));
    }

    public function store(LanguageRequest $request, BaseHttpResponse $response, LanguageManager $languageManager)
    {
        try {
            $language = $this->languageRepository->getFirstBy([
                'lang_code' => $request->input('lang_code'),
            ]);

            if ($language) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/language::language.added_already'));
            }

            if (! LanguageModel::query()->exists()) {
                $request->merge(['lang_is_default' => 1]);
            }

            File::ensureDirectoryExists(lang_path('vendor'));

            if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
                return $response
                    ->setError()
                    ->setMessage(
                        trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()])
                    );
            }

            $locale = $request->input('lang_locale');

            if (! File::isDirectory(lang_path($locale))) {
                $importedLocale = false;

                if (is_plugin_active('translation')) {
                    $result = app(Manager::class)->downloadRemoteLocale($locale);

                    $importedLocale = ! $result['error'];
                }

                if (! $importedLocale) {
                    $defaultLocale = lang_path('en');
                    if (File::exists($defaultLocale)) {
                        File::copyDirectory($defaultLocale, lang_path($locale));
                    }

                    $this->createLocaleInPath(lang_path('vendor/core'), $locale);
                    $this->createLocaleInPath(lang_path('vendor/packages'), $locale);
                    $this->createLocaleInPath(lang_path('vendor/plugins'), $locale);

                    $themeLocale = Arr::first(BaseHelper::scanFolder(theme_path(Theme::getThemeName() . '/lang')));

                    if ($themeLocale) {
                        File::copy(
                            theme_path(Theme::getThemeName() . '/lang/' . $themeLocale),
                            lang_path($locale . '.json')
                        );
                    }
                }
            }

            $language = $this->languageRepository->createOrUpdate($request->except('lang_id'));

            $this->clearRoutesCache();

            event(new CreatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $language));

            try {
                $models = $languageManager->supportedModels();

                if ($this->languageRepository->count() == 1) {
                    foreach ($models as $model) {
                        if (! class_exists($model)) {
                            continue;
                        }

                        $ids = LanguageMeta::query()->where('reference_type', $model)
                            ->pluck('reference_id')
                            ->all();

                        $table = (new $model())->getTable();

                        $referenceIds = DB::table($table)
                            ->whereNotIn('id', $ids)
                            ->pluck('id')
                            ->all();

                        $data = [];
                        foreach ($referenceIds as $referenceId) {
                            $data[] = [
                                'reference_id' => $referenceId,
                                'reference_type' => $model,
                                'lang_meta_code' => $language->lang_code,
                                'lang_meta_origin' => md5($referenceId . $model . time()),
                            ];
                        }

                        LanguageMeta::query()->insert($data);
                    }
                }
            } catch (Throwable $exception) {
                return $response
                    ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                    ->setMessage($exception->getMessage());
            }

            return $response
                ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function update(Request $request, BaseHttpResponse $response)
    {
        try {
            $language = $this->languageRepository->getFirstBy(['lang_id' => $request->input('lang_id')]);
            if (empty($language)) {
                abort(404);
            }

            $language->fill($request->input());
            $language = $this->languageRepository->createOrUpdate($language);

            $this->clearRoutesCache();

            event(new UpdatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $language));

            return $response
                ->setData(view('plugins/language::partials.language-item', ['item' => $language])->render())
                ->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function postChangeItemLanguage(Request $request, BaseHttpResponse $response)
    {
        $referenceId = $request->input('reference_id') ?: $request->input('lang_meta_created_from');
        $currentLanguage = $this->languageMetaRepository->getFirstBy([
            'reference_id' => $referenceId,
            'reference_type' => $request->input('reference_type'),
        ]);

        $others = $this->languageMetaRepository->getModel();

        if ($currentLanguage) {
            $others = $others
                ->where('lang_meta_code', '!=', $request->input('lang_meta_current_language'))
                ->where('lang_meta_origin', $currentLanguage->origin);
        }

        $others = $others->select(['reference_id', 'lang_meta_code'])->get();

        $data = [];
        foreach ($others as $other) {
            $language = $this->languageRepository->getFirstBy(['lang_code' => $other->lang_code], [
                'lang_flag',
                'lang_name',
                'lang_code',
            ]);

            if (! empty($language) && ! empty($currentLanguage) && $language->lang_code != $currentLanguage->lang_meta_code) {
                $data[$language->lang_code]['lang_flag'] = $language->lang_flag;
                $data[$language->lang_code]['lang_name'] = $language->lang_name;
                $data[$language->lang_code]['reference_id'] = $other->reference_id;
            }
        }

        $languages = $this->languageRepository->all();
        foreach ($languages as $language) {
            if (! array_key_exists(
                $language->lang_code,
                $data
            ) && $language->lang_code != $request->input('lang_meta_current_language')) {
                $data[$language->lang_code]['lang_flag'] = $language->lang_flag;
                $data[$language->lang_code]['lang_name'] = $language->lang_name;
                $data[$language->lang_code]['reference_id'] = null;
            }
        }

        return $response->setData($data);
    }

    public function destroy(int|string $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $language = $this->languageRepository->getFirstBy(['lang_id' => $id]);
            $this->languageRepository->delete($language);
            $defaultLanguageId = false;

            if ($language->lang_is_default) {
                $defaultLanguage = $this->languageRepository->getFirstBy([
                    'lang_is_default' => 1,
                ]);

                if ($defaultLanguage) {
                    $defaultLanguageId = $defaultLanguage->lang_id;
                }
            }

            $this->clearRoutesCache();

            event(new DeletedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $language));

            return $response
                ->setData($defaultLanguageId)
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getSetDefault(Request $request, BaseHttpResponse $response)
    {
        $newLanguageId = $request->input('lang_id');

        $newLanguage = $this->languageRepository->getFirstBy(['lang_id' => $newLanguageId]);

        if (! $newLanguage) {
            abort(404);
        }

        $newLanguageCode = $newLanguage->lang_code;

        $themeName = Theme::getThemeName();

        $defaultLanguage = LanguageFacade::getDefaultLanguage(['lang_id', 'lang_code']);
        $currentLanguageId = $defaultLanguage->lang_id;
        $currentLanguageCode = $defaultLanguage->lang_code;

        try {
            if ($currentLanguageId != $newLanguageId) {
                if (! Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->exists()) {
                    $widgets = Widget::query()->where('theme', $themeName)->get();

                    foreach ($widgets as $widget) {
                        $replicated = $widget->replicate();

                        $widget->theme = Widget::getThemeName($currentLanguageCode);
                        $widget->save();

                        $replicated->save();
                    }
                } else {
                    $currentWidgets = Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->get();

                    Widget::query()->where('theme', Widget::getThemeName($newLanguageCode))->delete();

                    $widgets = Widget::query()->where('theme', $themeName)->get();

                    foreach ($widgets as $widget) {
                        $widget->theme = Widget::getThemeName($currentLanguageCode);
                        $widget->save();
                    }

                    foreach ($currentWidgets as $widget) {
                        $widget = $widget->replicate();
                        $widget->theme = $themeName;
                        $widget->save();
                    }
                }

                $themeOptionKey = ThemeOption::getOptionKey('', $currentLanguageCode);

                if (! Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%', $newLanguageCode))->exists()) {
                    $themeOptions = Setting::newQuery()->where('key', 'LIKE', $themeOptionKey . '%')->get();

                    foreach ($themeOptions as $themeOption) {
                        $replicated = $themeOption->replicate();

                        $themeOption->key = str_replace(
                            ThemeOption::getOption($themeOption->key, $defaultLanguage->lang_code),
                            ThemeOption::getOption($themeOption->key),
                            $themeOption->key
                        );
                        $themeOption->save();

                        $replicated->save();
                    }
                } else {
                    $currentThemeOptions = Setting::newQuery()->where(
                        'key',
                        'LIKE',
                        ThemeOption::getOptionKey('%', $newLanguageCode)
                    )->get();

                    Setting::newQuery()->where('key', 'LIKE', ThemeOption::getOptionKey('%', $newLanguageCode))->delete();

                    $themeOptions = Setting::newQuery()->where('key', 'LIKE', $themeOptionKey . '%')->get();

                    foreach ($themeOptions as $themeOption) {
                        $themeOption->key = str_replace(
                            $themeOptionKey,
                            ThemeOption::getOptionKey('', $currentLanguageCode),
                            $themeOption->key
                        );

                        $themeOption->save();
                    }

                    foreach ($currentThemeOptions as $themeOption) {
                        $themeOption = $themeOption->replicate();

                        $themeOption->key = str_replace(
                            ThemeOption::getOptionKey('', $newLanguageCode),
                            $themeOptionKey,
                            $themeOption->key
                        );

                        $themeOption->save();
                    }
                }
            }
        } catch (Throwable $exception) {
            info($exception->getMessage());
        }

        $this->languageRepository->update(['lang_is_default' => 1], ['lang_is_default' => 0]);

        $newLanguage->lang_is_default = 1;
        $newLanguage->save();

        $this->clearRoutesCache();

        event(new UpdatedContentEvent(LANGUAGE_MODULE_SCREEN_NAME, $request, $newLanguage));

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getLanguage(Request $request, BaseHttpResponse $response)
    {
        $language = $this->languageRepository->getFirstBy(['lang_id' => $request->input('lang_id')]);

        return $response->setData($language);
    }

    public function postEditSettings(Request $request, BaseHttpResponse $response)
    {
        Setting::set('language_hide_default', $request->input('language_hide_default', false))
            ->set('language_display', $request->input('language_display'))
            ->set('language_switcher_display', $request->input('language_switcher_display'))
            ->set('language_hide_languages', json_encode($request->input('language_hide_languages', [])))
            ->set('language_auto_detect_user_language', $request->input('language_auto_detect_user_language'))
            ->set(
                'language_show_default_item_if_current_version_not_existed',
                $request->input('language_show_default_item_if_current_version_not_existed')
            )
            ->save();

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getChangeDataLanguage($code, LanguageManager $language)
    {
        $previousUrl = strtok(URL::previous(), '?');

        $queryString = null;
        if ($code !== $language->getDefaultLocaleCode()) {
            $queryString = '?' . http_build_query([LanguageFacade::refLangKey() => $code]);
        }

        return redirect()->to($previousUrl . $queryString);
    }

    protected function createLocaleInPath(string $path, string $locale): int
    {
        $folders = File::directories($path);

        foreach ($folders as $module) {
            foreach (File::directories($module) as $item) {
                if (File::name($item) == 'en') {
                    File::copyDirectory($item, $module . '/' . $locale);
                }
            }
        }

        return count($folders);
    }

    public function clearRoutesCache(): bool
    {
        $path = app()->getCachedRoutesPath();

        foreach (LanguageFacade::getSupportedLanguagesKeys() as $locale) {
            if (! $locale) {
                $locale = LanguageFacade::getDefaultLocale();
            }

            $path = substr($path, 0, -4) . '_' . $locale . '.php';

            if (File::exists($path)) {
                File::delete($path);
            }
        }

        return true;
    }
}
