<?php

namespace Botble\Revision\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_REGISTER_CONTENT_TABS, [$this, 'addHistoryTab'], 55, 3);
        add_filter(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, [$this, 'addHistoryContent'], 55, 3);
    }

    public function addHistoryTab(string|null $tabs, string|Model|null $data = null): string
    {
        if (! empty($data) && $this->isSupported($data)) {
            Assets::addScriptsDirectly([
                '/vendor/core/packages/revision/js/html-diff.js',
                '/vendor/core/packages/revision/js/revision.js',
            ])
                ->addStylesDirectly('/vendor/core/packages/revision/css/revision.css');

            return $tabs . view('packages/revision::history-tab')->render();
        }

        return $tabs;
    }

    protected function isSupported(string|Model $model): bool
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        return in_array($model, config('packages.revision.general.supported', []));
    }

    public function addHistoryContent(string|null $tabs, string|Model|null $data = null): string
    {
        if (! empty($data) && $this->isSupported($data)) {
            return $tabs . view('packages/revision::history-content', ['model' => $data])->render();
        }

        return $tabs;
    }
}
