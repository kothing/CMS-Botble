<?php

namespace Botble\Base\Widgets;

use Botble\Table\TableBuilder;

abstract class Table extends Widget
{
    protected string $view = 'table';

    protected string $id;

    protected string $route;

    protected array $params = [];

    protected string $table;

    public function __construct(protected TableBuilder $builder)
    {
        parent::__construct();
    }

    public function route(string $route, array $params = []): static
    {
        $this->route = $route;
        $this->params = $params;

        return $this;
    }

    public function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'table' => $this->builder
                ->create($this->table)
                ->setAjaxUrl(route($this->route, $this->params))
                ->renderTable(),
        ]);
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }
}
