<?php

use Illuminate\Database\Eloquent\Model;

if (! function_exists('table_checkbox')) {
    /**
     * @deprecated
     */
    function table_checkbox(int|string $id): string
    {
        return view('core/table::partials.checkbox', compact('id'))->render();
    }
}

if (! function_exists('table_actions')) {
    /**
     * @deprecated
     */
    function table_actions(string|null $edit, string|null $delete, Model $item, string|null $extra = null): string
    {
        return view('core/table::partials.actions', compact('edit', 'delete', 'item', 'extra'))->render();
    }
}
