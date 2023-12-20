<?php

namespace Botble\CustomField\Actions;

use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Botble\CustomField\Repositories\Interfaces\FieldItemInterface;

class ExportCustomFieldsAction extends AbstractAction
{
    public function __construct(protected FieldGroupInterface $fieldGroupRepository, protected FieldItemInterface $fieldItemRepository)
    {
    }

    public function run(array $fieldGroupIds): array
    {
        if (! $fieldGroupIds) {
            $fieldGroups = $this->fieldGroupRepository
                ->allBy([], [], ['id', 'title', 'status', 'order', 'rules'])
                ->toArray();
        } else {
            $fieldGroups = $this->fieldGroupRepository
                ->allBy([
                    ['id', 'IN', $fieldGroupIds],
                ], [], ['id', 'title', 'status', 'order', 'rules'])
                ->toArray();
        }

        foreach ($fieldGroups as &$fieldGroup) {
            $fieldGroup['items'] = $this->getFieldItems($fieldGroup['id']);
        }

        return $this->success(null, $fieldGroups);
    }

    protected function getFieldItems(int|string $fieldGroupId, int|string|null $parentId = null): array
    {
        $fieldItems = $this->fieldItemRepository
            ->allBy([
                'field_group_id' => $fieldGroupId,
                'parent_id' => $parentId,
            ])
            ->toArray();

        foreach ($fieldItems as &$fieldItem) {
            $fieldItem['children'] = $this->getFieldItems($fieldGroupId, $fieldItem['id']);
        }

        return $fieldItems;
    }
}
