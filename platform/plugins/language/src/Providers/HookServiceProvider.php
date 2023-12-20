<?php

namespace Botble\Language\Providers;

use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\ServiceProvider;
use Botble\Language\Facades\Language;
use Botble\Language\Models\Language as LanguageModel;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu;
use Botble\Table\CollectionDataTable;
use Botble\Table\EloquentDataTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addLanguageBox'], 50, 2);
        add_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, [$this, 'addCurrentLanguageEditingAlert'], 55, 2);
        add_action(BASE_ACTION_BEFORE_EDIT_CONTENT, [$this, 'getCurrentAdminLanguage'], 55, 2);
        add_filter(FILTER_SLUG_PREFIX, [$this, 'setSlugPrefix'], 500);
        add_filter(BASE_FILTER_GET_LIST_DATA, [$this, 'addLanguageColumn'], 50, 2);
        add_filter(BASE_FILTER_TABLE_HEADINGS, [$this, 'addLanguageTableHeading'], 50, 2);
        add_filter(LANGUAGE_FILTER_SWITCHER, [$this, 'languageSwitcher'], 50, 2);
        add_filter(BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM, [$this, 'checkItemLanguageBeforeShow'], 50, 2);
        add_filter(BASE_FILTER_BEFORE_GET_SINGLE, [$this, 'getRelatedDataForOtherLanguage'], 50, 2);
        add_filter(BASE_FILTER_TABLE_BUTTONS, [$this, 'addLanguageSwitcherToTable'], 247, 2);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'getDataByCurrentLanguage'], 157);
        add_filter(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, [$this, 'checkItemLanguageBeforeGetAdminListItem'], 50);
        add_filter(BASE_FILTER_SITE_LANGUAGE_DIRECTION, fn () => Language::getCurrentLocaleRTL() ? 'rtl' : 'ltr', 1);
        add_filter(MENU_FILTER_NODE_URL, [$this, 'updateMenuNodeUrl'], 1);
        add_filter(BASE_FILTER_BEFORE_RENDER_FORM, [$this, 'changeDataBeforeRenderingForm'], 1134, 2);
        add_filter('theme-options-action-meta-boxes', [$this, 'addLanguageMetaBoxForThemeOptionsAndWidgets'], 55, 2);
        add_filter('widget-top-meta-boxes', [$this, 'addLanguageMetaBoxForThemeOptionsAndWidgets'], 55, 2);
        add_filter('setting_email_template_meta_boxes', [$this, 'settingEmailTemplateMetaBoxes'], 55, 2);
        add_filter('setting_email_template_path', [$this, 'settingEmailTemplatePath'], 55, 3);
        add_filter('setting_email_subject_key', [$this, 'settingEmailSubjectKey'], 55);
    }

    public function settingEmailTemplateMetaBoxes(string|null $data, array $params = []): string
    {
        $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);

        if ($languages->count() < 2) {
            return $data;
        }

        return $data . view('plugins/language::partials.admin-list-language-chooser', [
                'route' => 'setting.email.template.edit',
                'params' => $params,
                'languages' => $languages,
            ])->render();
    }

    public function settingEmailTemplatePath(string $path, string $module, string $templateKey): string
    {
        $currentLocale = is_in_admin(true) ? Language::getCurrentAdminLocale() : Language::getCurrentLocale();
        $locale = $currentLocale !== Language::getDefaultLocale() ? $currentLocale : null;

        if ($locale && in_array($locale, array_keys(Language::getSupportedLocales()))) {
            return "$module/$locale/$templateKey.tpl";
        }

        return $path;
    }

    public function settingEmailSubjectKey(string $key): string
    {
        $currentLocale = is_in_admin(true) ? Language::getCurrentAdminLocale() : Language::getCurrentLocale();
        $locale = $currentLocale !== Language::getDefaultLocale() ? $currentLocale : null;

        if ($locale && in_array($locale, array_keys(Language::getSupportedLocales()))) {
            return $key . '_' . $locale;
        }

        return $key;
    }

    public function addLanguageBox(string $priority, string|Model|null $object): void
    {
        if (! empty($object) && in_array(get_class($object), Language::supportedModels())) {
            MetaBox::addMetaBox(
                'language_wrap',
                trans('plugins/language::language.name'),
                [$this, 'languageMetaField'],
                get_class($object),
                'top'
            );
        }
    }

    public function addLanguageMetaBoxForThemeOptionsAndWidgets(string|null $data, string $screen): string|null
    {
        $route = null;
        switch ($screen) {
            case THEME_OPTIONS_MODULE_SCREEN_NAME:
                $route = 'theme.options';

                break;
            case WIDGET_MANAGER_MODULE_SCREEN_NAME:
                $route = 'widgets.index';

                break;
        }

        if (empty($route)) {
            return $data;
        }

        $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);

        if ($languages->count() < 2) {
            return $data;
        }

        return $data . view('plugins/language::partials.admin-list-language-chooser', compact('route', 'languages'))->render();
    }

    public function setSlugPrefix(string $prefix): string
    {
        if (is_in_admin()) {
            $currentLocale = Language::getCurrentAdminLocale();
        } else {
            $currentLocale = Language::getCurrentLocale();
        }

        if ($currentLocale && (! setting('language_hide_default') || $currentLocale != Language::getDefaultLocale())) {
            if (! $prefix) {
                return $currentLocale;
            }

            if ($prefix === $currentLocale || Str::contains($prefix, $currentLocale . '/')) {
                return $prefix;
            }

            return $currentLocale . '/' . $prefix;
        }

        return $prefix;
    }

    public function languageMetaField(): string|null
    {
        $languages = Language::getActiveLanguage([
            'lang_code',
            'lang_flag',
            'lang_name',
        ]);

        if ($languages->isEmpty()) {
            return null;
        }

        $related = [];
        $args = func_get_args();

        $referenceId = null;
        $langMetaOrigin = null;
        $langMetaCode = null;

        if (($reference = $args[0]) && $reference->id) {
            $referenceId = $reference->id;
            $langMetaCode = $reference->languageMeta?->lang_meta_code;
            $langMetaOrigin = $reference->languageMeta?->lang_meta_origin;
        } elseif ($refFrom = Language::getRefFrom()) {
            $langMetaOrigin = LanguageMeta::query()
                ->where([
                    'reference_id' => $refFrom,
                    'reference_type' => get_class($reference),
                ])
                ->value('lang_meta_origin');

            $referenceId = $refFrom;
            $langMetaCode = Language::getRefLang();
        }

        if ($referenceId && $langMetaOrigin) {
            $related = Language::getRelatedLanguageItem($referenceId, $langMetaOrigin);
        }

        $currentLanguage = self::checkCurrentLanguage($languages, $langMetaCode);

        if (! $currentLanguage) {
            $currentLanguage = Language::getDefaultLanguage([
                'lang_flag',
                'lang_name',
                'lang_code',
            ]);
        }

        $route = $this->getRoutes();

        $queryParams = request()->query();

        $queryParams[Language::refFromKey()] = ! empty($args[0]) && $args[0]->id ? $args[0]->id : 0;

        return view(
            'plugins/language::language-box',
            compact('args', 'languages', 'currentLanguage', 'related', 'route', 'queryParams')
        )->render();
    }

    public function checkCurrentLanguage(array|EloquentCollection $languages, string|null $value): ?LanguageModel
    {
        $refLang = Language::getRefLang();

        $currentLanguage = null;
        foreach ($languages as $language) {
            if ($value && $language->lang_code == $value) {
                $currentLanguage = $language;

                break;
            } elseif ($refLang && $language->lang_code === $refLang) {
                $currentLanguage = $language;

                break;
            } elseif ($language->lang_is_default) {
                $currentLanguage = $language;

                break;
            }
        }

        if (empty($currentLanguage)) {
            foreach ($languages as $language) {
                if ($language->lang_is_default) {
                    $currentLanguage = $language;

                    break;
                }
            }
        }

        return $currentLanguage;
    }

    protected function getRoutes(): array
    {
        $currentRoute = implode('.', explode('.', Route::currentRouteName(), -1));

        return apply_filters(LANGUAGE_FILTER_ROUTE_ACTION, [
            'create' => $currentRoute . '.create',
            'edit' => $currentRoute . '.edit',
        ]);
    }

    public function addCurrentLanguageEditingAlert(Request $request, $data = null): void
    {
        $model = $data;
        if (is_object($data)) {
            $model = get_class($data);
        }

        if ($data && in_array($model, Language::supportedModels()) && Language::getActiveLanguage()->count() > 1) {
            $code = Language::getCurrentAdminLocaleCode();
            if (empty($code)) {
                $code = $this->getCurrentAdminLanguage($request, $data);
            }

            $language = null;
            if (! empty($code) && is_string($code)) {
                Language::setCurrentAdminLocale($code);
                $language = LanguageModel::query()->where('lang_code', $code)->value('lang_name');
            }

            echo view('plugins/language::partials.notification', compact('language'))->render();
        }

        echo null;
    }

    public function getCurrentAdminLanguage(Request $request, ?Model $data = null): string|null
    {
        $code = null;
        if ($refLang = Language::getRefLang()) {
            $code = $refLang;
        } elseif (! empty($data) && $data->getKey()) {
            $code = $data->languageMeta?->lang_meta_code;
        }

        if (empty($code) || ! is_string($code)) {
            $code = Language::getDefaultLocaleCode();
        }

        Language::setCurrentAdminLocale($code);

        return $code;
    }

    public function addLanguageTableHeading(array $headings, string|Model $model): array
    {
        if (in_array(get_class($model), Language::supportedModels())) {
            if (is_in_admin() && Auth::check() && ! Auth::user()->hasAnyPermission($this->getRoutes())) {
                return $headings;
            }

            $languages = Language::getActiveLanguage(['lang_code', 'lang_name', 'lang_flag']);
            $heading = '';
            foreach ($languages as $language) {
                $heading .= language_flag($language->lang_flag, $language->lang_name);
            }

            return array_merge($headings, [
                'language' => [
                    'name' => 'language_meta.lang_meta_id',
                    'title' => $heading,
                    'class' => 'text-center language-header no-sort',
                    'width' => (count($languages) * 40) . 'px',
                    'orderable' => false,
                    'searchable' => false,
                ],
            ]);
        }

        return $headings;
    }

    public function addLanguageColumn(
        EloquentDataTable|CollectionDataTable $data,
        string|Model $model
    ): EloquentDataTable|CollectionDataTable {
        if ($model && in_array(get_class($model), Language::supportedModels())) {
            $route = $this->getRoutes();

            if (is_in_admin() && Auth::check() && ! Auth::user()->hasAnyPermission($route)) {
                return $data;
            }

            return $data->addColumn('language', function ($item) use ($route) {
                $relatedLanguages = [];

                $currentLanguage = $item->languageMeta?->lang_meta_code;

                if ($languageMetaOrigin = $item->languageMeta?->lang_meta_origin) {
                    $relatedLanguages = Language::getRelatedLanguageItem($item->id, $languageMetaOrigin);
                }

                $languages = Language::getActiveLanguage();
                $data = '';

                foreach ($languages as $language) {
                    if ($language->lang_code == $currentLanguage) {
                        $data .= view('plugins/language::partials.status.active', compact('route', 'item'))->render();
                    } else {
                        $added = false;
                        if (! empty($relatedLanguages)) {
                            foreach ($relatedLanguages as $key => $relatedLanguage) {
                                if ($key == $language->lang_code) {
                                    $data .= view(
                                        'plugins/language::partials.status.edit',
                                        compact('route', 'relatedLanguage')
                                    )->render();
                                    $added = true;
                                }
                            }
                        }

                        if (! $added) {
                            $data .= view(
                                'plugins/language::partials.status.plus',
                                compact('route', 'item', 'language')
                            )->render();
                        }
                    }
                }

                return view('plugins/language::partials.language-column', compact('data'))->render();
            });
        }

        return $data;
    }

    public function languageSwitcher(string $switcher, array $options = []): string
    {
        return view('plugins/language::partials.switcher', compact('options'))->render();
    }

    public function checkItemLanguageBeforeShow(Builder|Model $data): Builder|Model
    {
        return $this->getDataByCurrentLanguageCode($data, Language::getCurrentLocaleCode());
    }

    protected function getDataByCurrentLanguageCode(Builder|Model $data, string|null $languageCode): Builder|Model
    {
        $model = $data->getModel();

        if (
            in_array(get_class($model), Language::supportedModels()) &&
            ! empty($languageCode) &&
            ! $model instanceof LanguageModel &&
            ! $model instanceof LanguageMeta &&
            Language::getCurrentAdminLocaleCode() !== 'all'
        ) {
            Language::initModelRelations();

            return $data
                ->whereHas('languageMeta', function (Builder $query) use ($languageCode) {
                    $query->where('lang_meta_code', $languageCode);
                });
        }

        return $data;
    }

    public function checkItemLanguageBeforeGetAdminListItem(Builder|Model $data): Builder|Model
    {
        return $this->getDataByCurrentLanguageCode($data, Language::getCurrentAdminLocaleCode());
    }

    public function getRelatedDataForOtherLanguage(Collection|Builder $query, ?Model $model)
    {
        if (! setting('language_show_default_item_if_current_version_not_existed', true) || is_in_admin()) {
            return $query;
        }

        if ($query instanceof Builder) {
            $model = $query->getModel();
        }

        if (in_array(get_class($model), Language::supportedModels()) &&
            ! $model instanceof LanguageModel &&
            ! $model instanceof LanguageMeta
        ) {
            $data = $query->first();

            if (! empty($data)) {
                $current = $data->languageMeta;

                if ($current) {
                    Language::setCurrentAdminLocale($current->lang_meta_code);
                    if ($current->lang_meta_code != Language::getCurrentLocaleCode()) {
                        if (
                            ! setting('language_show_default_item_if_current_version_not_existed', 1) &&
                            get_class($model) != Menu::class
                        ) {
                            return $data;
                        }

                        $referenceId = LanguageMeta::query()
                            ->where('lang_meta_origin', $current->lang_meta_origin)
                            ->where('reference_id', '!=', $data->getKey())
                            ->where('reference_type', get_class($model))
                            ->where('lang_meta_code', Language::getCurrentLocaleCode())
                            ->value('reference_id');

                        if ($referenceId && $result = $model->where('id', $referenceId)) {
                            return $result;
                        }
                    }
                }
            }
        }

        return $query;
    }

    public function addLanguageSwitcherToTable(array $buttons, string $model): array
    {
        if (in_array($model, Language::supportedModels())) {
            $activeLanguages = Language::getActiveLanguage(['lang_code', 'lang_name', 'lang_flag']);
            $languageButtons = [];
            $currentLanguage = Language::getCurrentAdminLocaleCode();

            foreach ($activeLanguages as $item) {
                $languageButtons[] = [
                    'className' => 'change-data-language-item ' . ($item->lang_code == $currentLanguage ? 'active' : ''),
                    'text' => Html::tag(
                        'span',
                        $item->lang_name,
                        ['data-href' => route('languages.change.data.language', $item->lang_code)]
                    )->toHtml(),
                ];
            }

            $languageButtons[] = [
                'className' => 'change-data-language-item ' . ('all' == $currentLanguage ? 'active' : ''),
                'text' => Html::tag(
                    'span',
                    trans('plugins/language::language.show_all'),
                    ['data-href' => route('languages.change.data.language', 'all')]
                )->toHtml(),
            ];

            $flag = $activeLanguages->where('lang_code', $currentLanguage)->first();
            if (! empty($flag)) {
                $flag = language_flag($flag->lang_flag, $flag->lang_name);
            } else {
                $flag = Html::tag('i', '', ['class' => 'fa fa-flag'])->toHtml();
            }

            $language = [
                'language' => [
                    'extend' => 'collection',
                    'text' => $flag . Html::tag(
                        'span',
                        ' ' . trans('plugins/language::language.change_language') . ' ' . Html::tag(
                            'span',
                            '',
                            ['class' => 'caret']
                        )->toHtml()
                    )->toHtml(),
                    'buttons' => $languageButtons,
                ],
            ];

            $buttons = array_merge($buttons, $language);
        }

        return $buttons;
    }

    public function getDataByCurrentLanguage(Builder $query): Builder
    {
        $model = $query->getModel();

        if (
            in_array(get_class($model), Language::supportedModels()) &&
            ($languageCode = Language::getCurrentAdminLocaleCode()) &&
            $languageCode !== 'all'
        ) {
            Language::initModelRelations();

            return $query
                ->whereHas('languageMeta', function (Builder $query) use ($languageCode) {
                    $query->where('lang_meta_code', $languageCode);
                });
        }

        return $query;
    }

    public function changeDataBeforeRenderingForm(FormAbstract $form, BaseModel $data): FormAbstract
    {
        if (
            is_in_admin() &&
            Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
            in_array(get_class($data), Language::supportedModels())
        ) {
            $refLang = Language::getRefLang();
            $refFrom = Language::getRefFrom();

            if ($refLang && $refFrom) {
                $data = $data->getModel()->find($refFrom);

                if ($data) {
                    $form->setModel($data->replicate());
                }
            }
        }

        return $form;
    }

    public function updateMenuNodeUrl(string|null $value): string
    {
        if (is_in_admin() || in_array($value, ['#', 'javascript:void(0)'])) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_URL) ? $value : Language::localizeURL($value);
    }
}
