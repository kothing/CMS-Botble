<?php

namespace Botble\Slug\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Menu\Facades\Menu;
use Botble\Setting\Supports\SettingStore;
use Botble\Slug\Http\Requests\SlugRequest;
use Botble\Slug\Http\Requests\SlugSettingsRequest;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Illuminate\Support\Str;

class SlugController extends BaseController
{
    public function store(SlugRequest $request, SlugService $slugService)
    {
        return $slugService->create(
            $request->input('value'),
            $request->input('slug_id'),
            $request->input('model')
        );
    }

    public function getSettings()
    {
        PageTitle::setTitle(trans('packages/slug::slug.settings.title'));

        return view('packages/slug::settings');
    }

    public function postSettings(
        SlugSettingsRequest $request,
        SlugInterface $slugRepository,
        BaseHttpResponse $response,
        SettingStore $settingStore
    ) {
        $hasChangedEndingUrl = false;

        foreach ($request->except(['_token']) as $settingKey => $settingValue) {
            if (Str::contains($settingKey, '-model-key')) {
                continue;
            }

            if ($settingKey == 'public_single_ending_url') {
                $settingValue = ltrim($settingValue, '.');

                if ($settingStore->get($settingKey) !== $settingValue) {
                    $hasChangedEndingUrl = true;
                }
            }

            if ($settingStore->get($settingKey) !== (string)$settingValue) {
                $slugRepository->update(
                    ['reference_type' => $request->input($settingKey . '-model-key')],
                    ['prefix' => (string)$settingValue]
                );

                Menu::clearCacheMenuItems();
            }

            $settingStore->set($settingKey, (string)$settingValue);
        }

        $settingStore->save();

        if ($hasChangedEndingUrl) {
            Menu::clearCacheMenuItems();
        }

        return $response
            ->setPreviousUrl(route('slug.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
