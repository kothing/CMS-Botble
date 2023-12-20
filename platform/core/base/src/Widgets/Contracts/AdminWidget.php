<?php

namespace Botble\Base\Widgets\Contracts;

use Illuminate\Contracts\View\View;

interface AdminWidget
{
    public function register(array $widgets, string|null $namespace): static;

    public function remove(string $id, string|null $namespace): static;

    public function getColumns(string|null $namespace): int;

    public function render(string $namespace): View;
}
