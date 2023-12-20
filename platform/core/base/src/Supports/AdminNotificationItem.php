<?php

namespace Botble\Base\Supports;

use Closure;

class AdminNotificationItem
{
    protected string $title = '';

    protected string $description = '';

    protected string $label = '';

    protected Closure|string|null $route = null;

    protected array $action = [];

    protected string $permission = '';

    public static function make(): self
    {
        return new self();
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function action(string $label, Closure|string|null $route): self
    {
        $this->route = $route;
        $this->label = $label;

        return $this;
    }

    public function permission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): string
    {
        return $this->permission;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRoute(): Closure|string|null
    {
        return $this->route;
    }
}
