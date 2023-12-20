<?php

namespace Botble\Theme\Supports;

use Illuminate\Support\Facades\Auth;

class AdminBar
{
    protected array $groups = [];

    protected bool $isDisplay = true;

    protected array $noGroupLinks = [];

    public function __construct()
    {
        $this->groups = [
            'appearance' => [
                'link' => 'javascript:;',
                'title' => trans('packages/theme::theme.appearance'),
                'items' => [],
            ],
            'add-new' => [
                'link' => 'javascript:;',
                'title' => trans('packages/theme::theme.add_new'),
                'items' => [],
            ],
        ];
    }

    public function isDisplay(): bool
    {
        return $this->isDisplay;
    }

    public function setIsDisplay(bool $isDisplay = true): self
    {
        $this->isDisplay = $isDisplay;

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getLinksNoGroup(): array
    {
        return $this->noGroupLinks;
    }

    public function registerGroup(string $slug, string $title, string $link = 'javascript:;'): self
    {
        if (isset($this->groups[$slug])) {
            $this->groups[$slug]['items'][$title] = $link;

            return $this;
        }

        $this->groups[$slug] = [
            'title' => $title,
            'link' => $link,
            'items' => [],
        ];

        return $this;
    }

    public function registerLink(string $title, string $url, $group = null, string $permission = null): self
    {
        if ($group === null || ! isset($this->groups[$group])) {
            $this->noGroupLinks[] = [
                'link' => $url,
                'title' => $title,
                'permission' => $permission,
            ];
        } else {
            $this->groups[$group]['items'][$title] = [
                'link' => $url,
                'title' => $title,
                'permission' => $permission,
            ];
        }

        return $this;
    }

    public function render(): string
    {
        if (! Auth::check()) {
            return '';
        }

        $this->registerLink(trans('core/base::layouts.dashboard'), route('dashboard.index'), 'appearance', 'dashboard.index');
        $this->registerLink(trans('core/acl::users.users'), route('users.create'), 'add-new', 'users.create');
        $this->registerLink(trans('core/setting::setting.title'), route('settings.options'), 'appearance', 'settings.options');

        foreach ($this->groups as $key => $group) {
            if (! isset($group['items'])) {
                continue;
            }

            foreach ($group['items'] as $itemKey => $item) {
                if (! empty($item['permission']) && ! Auth::user()->hasPermission($item['permission'])) {
                    unset($this->groups[$key]['items'][$itemKey]);
                }
            }

            if (! count($group['items'])) {
                unset($this->groups[$key]);
            }
        }

        return view('packages/theme::admin-bar')->render();
    }
}
