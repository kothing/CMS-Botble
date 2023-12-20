<?php

namespace Botble\Block\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Block\Forms\BlockForm;
use Botble\Block\Http\Requests\BlockRequest;
use Botble\Block\Models\Block;
use Botble\Block\Tables\BlockTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends BaseController
{
    use HasDeleteManyItemsTrait;

    public function index(BlockTable $dataTable)
    {
        PageTitle::setTitle(trans('plugins/block::block.menu'));

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('plugins/block::block.create'));

        return $formBuilder->create(BlockForm::class)->renderForm();
    }

    public function store(BlockRequest $request, BaseHttpResponse $response)
    {
        $block = new Block();
        $block->fill($request->input());
        $block->user_id = Auth::id();
        $block->save();

        event(new CreatedContentEvent(BLOCK_MODULE_SCREEN_NAME, $request, $block));

        return $response
            ->setPreviousUrl(route('block.index'))
            ->setNextUrl(route('block.edit', $block->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Block $block, FormBuilder $formBuilder)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $block->name]));

        return $formBuilder->create(BlockForm::class, ['model' => $block])->renderForm();
    }

    public function update(Block $block, BlockRequest $request, BaseHttpResponse $response)
    {
        $block->fill($request->input());
        $block->save();

        event(new UpdatedContentEvent(BLOCK_MODULE_SCREEN_NAME, $request, $block));

        return $response
            ->setPreviousUrl(route('block.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Block $block, Request $request, BaseHttpResponse $response)
    {
        try {
            $block->delete();
            event(new DeletedContentEvent(BLOCK_MODULE_SCREEN_NAME, $request, $block));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, new Block(), BLOCK_MODULE_SCREEN_NAME);
    }
}
