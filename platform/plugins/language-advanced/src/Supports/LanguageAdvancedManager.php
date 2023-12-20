<?php

namespace Botble\LanguageAdvanced\Supports;

use Botble\Language\Facades\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LanguageAdvancedManager
{
    public static function save(Model|null $object, Request $request): bool
    {
        if (! self::isSupported($object)) {
            return false;
        }

        $language = $request->input('language');

        if (! $language) {
            $language = Language::getCurrentAdminLocaleCode();
        }

        $condition = [
            'lang_code' => $language,
            $object->getTable() . '_id' => $object->getKey(),
        ];

        $table = $object->getTable() . '_translations';

        $data = [];
        foreach (DB::getSchemaBuilder()->getColumnListing($table) as $column) {
            if (! in_array($column, array_keys($condition))) {
                $data[$column] = $request->input($column);
            }
        }

        $data = array_merge($data, $condition);

        $translate = DB::table($table)->where($condition)->first();

        if ($translate) {
            DB::table($table)->where($condition)->update($data);
        } else {
            DB::table($table)->insert($data);
        }

        if ($language != Language::getDefaultLocaleCode()) {
            $defaultTranslation = DB::table($table)
                ->where([
                    'lang_code' => Language::getDefaultLocaleCode(),
                    $object->getTable() . '_id' => $object->getKey(),
                ])
                ->first();

            if ($defaultTranslation) {
                foreach (DB::getSchemaBuilder()->getColumnListing($table) as $column) {
                    if (! in_array($column, array_keys($condition))) {
                        $object->{$column} = $defaultTranslation->{$column};
                    }
                }

                $object->save();
            }
        }

        return true;
    }

    public static function isSupported(Model|string|null $model): bool
    {
        if (! $model) {
            return false;
        }

        if (is_object($model)) {
            $model = get_class($model);
        }

        return in_array($model, self::supportedModels());
    }

    public static function supportedModels(): array
    {
        return array_keys(self::getSupported());
    }

    public static function getSupported(): array
    {
        return config('plugins.language-advanced.general.supported', []);
    }

    public static function getConfigs(): array
    {
        return config('plugins.language-advanced.general', []);
    }

    public static function getTranslatableColumns(Model|string|null $model): array
    {
        if (! $model) {
            return [];
        }

        if (is_object($model)) {
            $model = get_class($model);
        }

        return Arr::get(LanguageAdvancedManager::getSupported(), $model, []);
    }

    public static function registerModule(string $model, array $columns): bool
    {
        config([
            'plugins.language-advanced.general.supported' => array_merge(self::getSupported(), [$model => $columns]),
        ]);

        return true;
    }

    public static function delete(Model|string|null $object): bool
    {
        if (! self::isSupported($object)) {
            return false;
        }

        $table = $object->getTable() . '_translations';

        DB::table($table)->where([$object->getTable() . '_id' => $object->getKey()])->delete();

        return true;
    }

    public static function isTranslatableMetaBox(string $metaBoxKey): bool
    {
        return in_array($metaBoxKey, Arr::get(self::getConfigs(), 'translatable_meta_boxes', []));
    }

    public static function addTranslatableMetaBox(string $metaBoxKey): bool
    {
        $metaBoxes = array_merge(Arr::get(self::getConfigs(), 'translatable_meta_boxes', []), [$metaBoxKey]);

        config(['plugins.language-advanced.general.translatable_meta_boxes' => $metaBoxes]);

        return true;
    }
}
