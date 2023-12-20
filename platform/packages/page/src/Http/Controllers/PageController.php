<?php

namespace Botble\Page\Http\Controllers;

use Botble\Base\Events\BeforeUpdateContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Page\Forms\PageForm;
use Botble\Page\Http\Requests\PageRequest;
use Botble\Page\Models\Page;
use Botble\Page\Tables\PageTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function index(PageTable $dataTable)
    {
        PageTitle::setTitle(trans('packages/page::pages.menu_name'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('packages/page::pages.create'));

        return $formBuilder->create(PageForm::class)->renderForm();
    }

    public function store(PageRequest $request, BaseHttpResponse $response)
    {
        $page = Page::query()->create(array_merge($request->input(), [
            'user_id' => Auth::id(),
        ]));

        event(new CreatedContentEvent(PAGE_MODULE_SCREEN_NAME, $request, $page));

        return $response->setPreviousUrl(route('pages.index'))
            ->setNextUrl(route('pages.edit', $page->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Page $page, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $page->name]));

        return $formBuilder->create(PageForm::class, ['model' => $page])->renderForm();
    }

    public function update(Page $page, PageRequest $request, BaseHttpResponse $response)
    {
        event(new BeforeUpdateContentEvent($request, $page));

        $page->fill($request->input());
        $page->save();

        event(new UpdatedContentEvent(PAGE_MODULE_SCREEN_NAME, $request, $page));

        return $response
            ->setPreviousUrl(route('pages.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Page $page, Request $request, BaseHttpResponse $response)
    {
        try {
            $page->delete();

            event(new DeletedContentEvent(PAGE_MODULE_SCREEN_NAME, $request, $page));

            return $response->setMessage(trans('packages/page::pages.deleted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, new Page(), PAGE_MODULE_SCREEN_NAME);
    }
}
