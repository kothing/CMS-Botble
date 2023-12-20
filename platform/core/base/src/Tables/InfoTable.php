<?php

namespace Botble\Base\Tables;

use Botble\Base\Supports\SystemManagement;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Http\JsonResponse;

class InfoTable extends TableAbstract
{
    protected $view = 'core/table::simple-table';

    protected $hasCheckbox = false;

    protected $hasOperations = false;

    public function ajax(): JsonResponse
    {
        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        return $this
            ->toJson($this->table->of(collect($packages))
            ->editColumn('name', function (array $item) {
                return view('core/base::system.partials.info-package-line', compact('item'))->render();
            })
            ->editColumn('dependencies', function (array $item) {
                return view('core/base::system.partials.info-dependencies-line', compact('item'))->render();
            }));
    }

    public function columns(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'title' => trans('core/base::system.package_name') . ' : ' . trans('core/base::system.version'),
                'class' => 'text-start',
            ],
            'dependencies' => [
                'name' => 'dependencies',
                'title' => trans('core/base::system.dependency_name') . ' : ' . trans('core/base::system.version'),
                'class' => 'text-start',
            ],
        ];
    }

    protected function getDom(): string|null
    {
        return $this->simpleDom();
    }
}
