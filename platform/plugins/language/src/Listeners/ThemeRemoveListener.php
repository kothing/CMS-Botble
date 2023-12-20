<?php

namespace Botble\Language\Listeners;

use Botble\Language\Facades\Language;
use Botble\Setting\Models\Setting;
use Botble\Theme\Events\ThemeRemoveEvent;
use Botble\Theme\Facades\ThemeOption;
use Botble\Widget\Models\Widget;
use Exception;

class ThemeRemoveListener
{
    public function handle(ThemeRemoveEvent $event): void
    {
        try {
            $languages = Language::getActiveLanguage(['lang_code']);

            foreach ($languages as $language) {
                Widget::query()
                    ->where(['theme' => Widget::getThemeName($language->lang_code, theme: $event->theme)])
                    ->delete();

                Setting::query()
                    ->where(['key', 'LIKE', ThemeOption::getOptionKey('%', $language->lang_code)])
                    ->delete();
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
