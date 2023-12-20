<?php

namespace Botble\CustomField\Repositories\Eloquent;

use Botble\Base\Models\BaseModel;
use Botble\CustomField\Models\FieldItem;
use Botble\CustomField\Repositories\Interfaces\CustomFieldInterface;
use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Botble\CustomField\Repositories\Interfaces\FieldItemInterface;
use Botble\Media\Facades\RvMedia;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FieldGroupRepository extends RepositoriesAbstract implements FieldGroupInterface
{
    protected FieldItemInterface $fieldItemRepository;

    protected CustomFieldInterface $customFieldRepository;

    public function __construct(BaseModel $model)
    {
        parent::__construct($model);

        $this->fieldItemRepository = app(FieldItemInterface::class);
        $this->customFieldRepository = app(CustomFieldInterface::class);
    }

    public function getFieldGroups(array $condition = []): Collection
    {
        return $this->model
            ->where($condition)
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function getFieldGroupItems(
        $groupId,
        $parentId = null,
        $withValue = false,
        $morphClass = null,
        $morphId = null
    ) {
        $result = [];

        $fieldItems = $this->fieldItemRepository->getGroupItems($groupId, $parentId);

        foreach ($fieldItems as $row) {
            $item = [
                'id' => $row->id,
                'title' => $row->title,
                'slug' => $row->slug,
                'instructions' => $row->instructions,
                'type' => $row->type,
                'options' => json_decode($row->options),
                'items' => $this->getFieldGroupItems($groupId, $row->id, $withValue, $morphClass, $morphId),
            ];
            if ($withValue === true) {
                if ($row->type === 'repeater') {
                    $item['value'] = $this->getRepeaterValue(
                        $item['items'],
                        $this->getFieldItemValue($row, $morphClass, $morphId)
                    );

                    foreach ($item['value'] as $ignored) {
                        $this->getRepeaterValues($item['value']);
                    }
                } else {
                    $item['value'] = $this->getFieldItemValue($row, $morphClass, $morphId);
                }

                if ($row->type == 'image' && ! empty($item['value'])) {
                    $item['thumb'] = RvMedia::getImageUrl($item['value'], 'thumb');
                }

                if ($row->type == 'file' && ! empty($item['value'])) {
                    $item['full_url'] = RvMedia::url($item['value']);
                }
            }

            $result[] = $item;
        }

        return $result;
    }

    protected function getRepeaterValues(array &$items): array
    {
        foreach ($items as &$child) {
            foreach ($child as &$item) {
                if ($item['type'] == 'repeater') {
                    $this->getRepeaterValues($item['value']);
                }

                if ($item['type'] == 'image') {
                    $item['thumb'] = RvMedia::getImageUrl($item['value'], 'thumb');
                }

                if ($item['type'] == 'file') {
                    $item['full_url'] = RvMedia::url($item['value']);
                }
            }
        }

        return $items;
    }

    protected function getRepeaterValue(array $items, array|string|null $data): array
    {
        if (! $items) {
            return [];
        }

        $data = $data ?: [];
        if ($data && ! is_array($data)) {
            $data = json_decode($data, true);
        }

        if (! $data) {
            return [];
        }

        $result = [];
        foreach ($data as $key => $row) {
            $cloned = $items;
            foreach ($cloned as $keyItem => $item) {
                foreach ($row as $currentData) {
                    if ((int)$item['id'] !== (int)$currentData['field_item_id']) {
                        continue;
                    }

                    if ($item['type'] === 'repeater') {
                        $item['value'] = $this->getRepeaterValue($item['items'], $currentData['value']);
                    } else {
                        $item['value'] = $currentData['value'];
                    }

                    $cloned[$keyItem] = $item;
                }
            }
            $result[$key] = $cloned;
        }

        return $result;
    }

    protected function getFieldItemValue(
        FieldItem $fieldItem,
        string|object|null $morphClass,
        string|null $morphId
    ): string|array|null {
        if (is_object($morphClass)) {
            $morphClass = get_class($morphClass);
        }

        $field = $this->customFieldRepository->getFirstBy([
            'use_for' => $morphClass,
            'use_for_id' => $morphId,
            'slug' => $fieldItem->slug,
            'field_item_id' => $fieldItem->getKey(),
        ]);

        return $field?->value;
    }

    public function createFieldGroup(array $data)
    {
        $result = $this->create($data);

        if ($result) {
            if (Arr::get($data, 'group_items')) {
                $this->editGroupItems(json_decode((string)$data['group_items'], true), $result->id);
            }
        }

        return $result;
    }

    protected function editGroupItems(array $items, int|string|null $groupId, int|string|null $parentId = null)
    {
        $position = 0;
        foreach ($items as $row) {
            $position++;

            $id = $row['id'];

            $data = [
                'field_group_id' => $groupId,
                'parent_id' => $parentId,
                'title' => $row['title'],
                'order' => $position,
                'type' => $row['type'],
                'options' => json_encode($row['options']),
                'instructions' => $row['instructions'],
                'slug' => Str::slug($row['slug'], '_') ?: Str::slug($row['title'], '_'),
                'position' => $position,
            ];

            $result = $this->fieldItemRepository->updateWithUniqueSlug($id, $data);

            if ($result) {
                $this->editGroupItems($row['items'], $groupId, $result->id);
            }
        }
    }

    public function createOrUpdateFieldGroup(int|string|null $id, array $data)
    {
        $result = $this->createOrUpdate($data, compact('id'));

        if ($result) {
            if (Arr::get($data, 'deleted_items')) {
                $this->fieldItemRepository->deleteFieldItem(json_decode((string)$data['deleted_items'], true));
            }

            if (Arr::get($data, 'group_items')) {
                $this->editGroupItems(json_decode((string)$data['group_items'], true), $result->id);
            }
        }

        return $result;
    }

    public function updateFieldGroup(int|string|null $id, array $data)
    {
        $result = $this->createOrUpdate($data, compact('id'));

        if ($result) {
            if (Arr::get($data, 'deleted_items')) {
                $this->fieldItemRepository->deleteFieldItem(json_decode((string)$data['deleted_items'], true));
            }

            if (Arr::get($data, 'group_items')) {
                $this->editGroupItems(json_decode((string)$data['group_items'], true), $result->id);
            }
        }

        return $result;
    }
}
