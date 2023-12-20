<?php

namespace Botble\Base\Supports;

use Exception;
use Illuminate\Support\Collection;

class BreadcrumbsGenerator
{
    protected Collection $breadcrumbs;

    protected array $callbacks = [];

    public function generate(array $callbacks, array $before, array $after, string $name, array $params): Collection
    {
        $this->breadcrumbs = new Collection();
        $this->callbacks = $callbacks;

        foreach ($before as $callback) {
            $callback($this);
        }

        $this->call($name, $params);

        foreach ($after as $callback) {
            $callback($this);
        }

        return $this->breadcrumbs;
    }

    protected function call(string $name, array $params): void
    {
        if (! isset($this->callbacks[$name])) {
            throw new Exception($name);
        }

        $this->callbacks[$name]($this, ...$params);
    }

    public function parent(string $name, ...$params): void
    {
        $this->call($name, $params);
    }

    public function push(string $title, string $url = null, array $data = []): void
    {
        $this->breadcrumbs->push((object)array_merge($data, compact('title', 'url')));
    }
}
