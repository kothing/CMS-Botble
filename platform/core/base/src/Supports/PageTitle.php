<?php

namespace Botble\Base\Supports;

class PageTitle
{
    protected string $title;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(bool $full = true): string|null
    {
        $baseTitle = setting('admin_title', config('core.base.general.base_name'));

        if (empty($this->title)) {
            return $baseTitle;
        }

        if (! $full) {
            return $this->title;
        }

        return $this->title . ' | ' . $baseTitle;
    }
}
