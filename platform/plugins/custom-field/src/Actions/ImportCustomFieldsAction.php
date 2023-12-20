<?php

namespace Botble\CustomField\Actions;

use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Botble\CustomField\Repositories\Interfaces\FieldItemInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportCustomFieldsAction extends AbstractAction
{
    public function __construct(
        protected FieldGroupInterface $fieldGroupRepository,
        protected FieldItemInterface $fieldItemRepository
    ) {
    }

    public function run(array $fieldGroupsData): array
    {
        DB::beginTransaction();

        foreach ($fieldGroupsData as $fieldGroup) {
            if (! is_array($fieldGroup)) {
                continue;
            }

            $validator = Validator::make($fieldGroup, [
                'order' => 'integer|min:0|required',
                'rules' => 'json|required',
                'title' => 'required|max:255',
            ]);

            if (isset($fieldGroup['status']) && is_array($fieldGroup['status'])) {
                $fieldGroup['status'] = $fieldGroup['status']['value'];
            }

            if ($validator->fails()) {
                DB::rollBack();

                return $this->error($validator->messages()->first());
            }

            $fieldGroup['created_by'] = Auth::id();
            $item = $this->fieldGroupRepository->create($fieldGroup);
            if (! $item) {
                DB::rollBack();

                return $this->error();
            }
            $createItems = $this->createFieldItem(Arr::get($fieldGroup, 'items', []), $item->id);
            if ($createItems['error']) {
                DB::rollBack();

                return $this->error($createItems['message']);
            }
        }

        DB::commit();

        return $this->success();
    }

    protected function createFieldItem(array $items, int|string $fieldGroupId, int|string|null $parentId = null): array
    {
        foreach ($items as $item) {
            $validator = Validator::make($item, [
                'order' => 'integer|min:0|required',
                'title' => 'required|max:255',
                'slug' => 'required|max:255',
                'type' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                return [
                    'error' => true,
                    'message' => $validator->messages()->first(),

                ];
            }

            $item['field_group_id'] = $fieldGroupId;
            $item['parent_id'] = $parentId;
            $item['created_by'] = Auth::id();
            $field = $this->fieldItemRepository->create($item);

            if (! $field) {
                return [
                    'error' => true,
                    'message' => null,
                ];
            }

            $createChildren = $this->createFieldItem(Arr::get($item, 'children', []), $fieldGroupId, $field->id);

            if (! $createChildren) {
                return [
                    'error' => true,
                    'message' => null,
                ];
            }
        }

        return [
            'error' => false,
        ];
    }
}
