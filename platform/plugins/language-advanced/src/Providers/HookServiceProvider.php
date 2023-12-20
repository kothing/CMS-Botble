<?php

namespace Botble\LanguageAdvanced\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Supports\ServiceProvider;
use Botble\Language\Facades\Language;
use Botble\Language\Models\Language as LanguageModel;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Slug\Facades\SlugHelper;
use Botble\Table\CollectionDataTable;
use Botble\Table\EloquentDataTable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addLanguageBox'], 1134, 2);
        add_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, [$this, 'addCurrentLanguageEditingAlert'], 1134, 3);
        add_action(BASE_ACTION_BEFORE_EDIT_CONTENT, [$this, 'getCurrentAdminLanguage'], 1134, 2);
        add_action(BASE_ACTION_META_BOXES, [$this, 'customizeMetaBoxes'], 10, 2);

        add_filter(BASE_FILTER_TABLE_HEADINGS, [$this, 'addLanguageTableHeading'], 1134, 2);
        add_filter(BASE_FILTER_GET_LIST_DATA, [$this, 'addLanguageColumn'], 1134, 2);
        add_filter(BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM, [$this, 'checkItemLanguageBeforeShow'], 1134, 2);
        add_filter(BASE_FILTER_BEFORE_RENDER_FORM, [$this, 'changeFormDataBeforeRendering'], 1134, 2);
        add_filter(BASE_FILTER_SLUG_AREA, [$this, 'changeSlugField'], 25, 2);
        add_filter(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, [$this, 'checkItemLanguageBeforeGetAdminListItem'], 50, 2);
        add_filter('stored_meta_box_key', [$this, 'storeMetaBoxKey'], 1134, 2);
    }

    public function addLanguageBox(string $priority, Model|string|null $object): void
    {
        if (
            $priority == 'top' &&
            ! empty($object) &&
            $object->getKey() &&
            LanguageAdvancedManager::isSupported($object) &&
            Language::getActiveLanguage([
                'lang_code',
                'lang_flag',
                'lang_name',
            ])->count() > 1
        ) {
            MetaBox::addMetaBox(
                'language_advanced_wrap',
                trans('plugins/language::language.name'),
                [$this, 'languageMetaField'],
                get_class($object),
                'top'
            );
        }
    }

    public function languageMetaField(): string|null
    {
        $languages = Language::getActiveLanguage([
            'lang_code',
            'lang_flag',
            'lang_name',
        ]);

        if ($languages->count() < 2) {
            return null;
        }

        $args = func_get_args();

        $currentLanguage = self::checkCurrentLanguage($languages);

        if (! $currentLanguage) {
            $currentLanguage = Language::getDefaultLanguage([
                'lang_flag',
                'lang_name',
                'lang_code',
            ]);
        }

        $route = $this->getRoutes();

        return view(
            'plugins/language-advanced::language-box',
            compact(
                'args',
                'languages',
                'currentLanguage',
                'route'
            )
        )->render();
    }

    public function checkCurrentLanguage(array|Collection $languages)
    {
        $referenceLanguage = Language::getRefLang();

        foreach ($languages as $language) {
            if (($referenceLanguage && $language->lang_code == $referenceLanguage) ||
                $language->lang_is_default
            ) {
                return $language;
            }
        }

        return null;
    }

    protected function getRoutes(): array
    {
        $currentRoute = implode('.', explode('.', Route::currentRouteName(), -1));

        return apply_filters(LANGUAGE_FILTER_ROUTE_ACTION, [
            'create' => $currentRoute . '.create',
            'edit' => $currentRoute . '.edit',
        ]);
    }

    public function addCurrentLanguageEditingAlert(Request $request, Model|string|null $data = null): void
    {
        $model = $data;
        if (is_object($data)) {
            $model = get_class($data);
        }

        if ($data && LanguageAdvancedManager::isSupported($model) && Language::getActiveLanguage()->count() > 1) {
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
    }

    public function getCurrentAdminLanguage(Request $request, Model|string|null $data = null): string|null
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

    public function languageSwitcher(array $options = []): string
    {
        return view('plugins/language::partials.switcher', compact('options'))->render();
    }

    public function addLanguageColumn(
        EloquentDataTable|CollectionDataTable $data,
        Model|string|null $model
    ): EloquentDataTable|CollectionDataTable {
        if ($model && LanguageAdvancedManager::isSupported($model)) {
            $route = $this->getRoutes();

            if (is_in_admin() && Auth::check() && ! Auth::user()->hasAnyPermission($route)) {
                return $data;
            }

            return $data->addColumn('language', function ($item) use ($route) {
                $languages = Language::getActiveLanguage();

                return view('plugins/language-advanced::language-column', compact('item', 'route', 'languages'))
                    ->render();
            });
        }

        return $data;
    }

    public function addLanguageTableHeading(array $headings, Model|string|null $model): array
    {
        if (! LanguageAdvancedManager::isSupported($model)) {
            return $headings;
        }

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

    public function checkItemLanguageBeforeShow($query, Model|string|null $model): Builder|EloquentBuilder|Model
    {
        $currentLocale = Language::getCurrentLocaleCode();

        if ($currentLocale == Language::getDefaultLocaleCode()) {
            return $query;
        }

        return $this->getDataByCurrentLanguageCode($query, $model, Language::getCurrentLocaleCode());
    }

    public function checkItemLanguageBeforeGetAdminListItem(EloquentBuilder|Model $query, Model|string|null $model): EloquentBuilder|Model
    {
        return $this->getDataByCurrentLanguageCode($query, $model, Language::getCurrentAdminLocaleCode());
    }

    protected function getDataByCurrentLanguageCode($query, Model|string|null $model, string $currentLocale): Builder|EloquentBuilder|Model
    {
        if ($query instanceof Builder || $query instanceof EloquentBuilder) {
            $model = $query->getModel();
        }

        if (! LanguageAdvancedManager::isSupported($model)) {
            return $query;
        }

        return $query->with([
            'translations' => function ($query) use ($model, $currentLocale) {
                $query->where($model->getTable() . '_translations' . '.lang_code', $currentLocale);
            },
        ]);
    }

    public function changeFormDataBeforeRendering(FormAbstract $form, Model|string|null $data): FormAbstract
    {
        if (
            is_in_admin() &&
            Language::getRefLang() &&
            Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
            $data &&
            $data->getKey() &&
            LanguageAdvancedManager::isSupported($data)
        ) {
            foreach ($form->getMetaBoxes() as $key => $metaBox) {
                if (LanguageAdvancedManager::isTranslatableMetaBox($key)) {
                    continue;
                }

                $form->removeMetaBox($key);
            }

            $columns = LanguageAdvancedManager::getTranslatableColumns($data);
            foreach ($form->getFields() as $key => $field) {
                if (! in_array($key, $columns)) {
                    $form->remove($key);
                }
            }

            $refLang = null;

            if (Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode()) {
                $refLang = '?ref_lang=' . Language::getCurrentAdminLocaleCode();
            }

            $form
                ->setFormOption('url', route('language-advanced.save', $data->getKey()) . $refLang)
                ->add('model', 'hidden', ['value' => get_class($data)]);
        }

        return $form;
    }

    public function customizeMetaBoxes(string $context, Model|string|null $object): void
    {
        if (
            is_in_admin() &&
            Language::getRefLang() &&
            Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
            LanguageAdvancedManager::isSupported($object)
        ) {
            foreach (MetaBox::getMetaBoxes() as $reference => $metaBox) {
                foreach ($metaBox as $context => $position) {
                    foreach ($position as $item) {
                        foreach (array_keys($item) as $key) {
                            if (LanguageAdvancedManager::isTranslatableMetaBox($key)) {
                                continue;
                            }

                            MetaBox::removeMetaBox($key, $reference, $context);
                        }
                    }
                }
            }
        }
    }

    public function changeSlugField(string|null $html = null, Model|string|null $object = null): string|null
    {
        if (
            is_in_admin() &&
            Language::getRefLang() &&
            Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
            LanguageAdvancedManager::isSupported($object) &&
            SlugHelper::isSupportedModel(get_class($object))
        ) {
            Assets::addStylesDirectly('vendor/core/packages/slug/css/slug.css');

            $prefix = SlugHelper::getPrefix(get_class($object));

            return view('plugins/language-advanced::slug', compact('object', 'prefix'))->render();
        }

        return $html;
    }

    public function storeMetaBoxKey(string $key, Model|string|null $object): string
    {
        $locale = is_in_admin() ? Language::getCurrentAdminLocaleCode() : Language::getCurrentLocaleCode();

        $translatableColumns = LanguageAdvancedManager::getTranslatableColumns($object);

        $translatableColumns[] = 'seo_meta';

        if (is_plugin_active('language') && $locale != Language::getDefaultLocaleCode() && in_array(
            $key,
            $translatableColumns
        )) {
            $key = $locale . '_' . $key;
        }

        return $key;
    }
}
