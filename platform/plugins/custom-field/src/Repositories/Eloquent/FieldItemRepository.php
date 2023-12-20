<?php

namespace Botble\CustomField\Repositories\Eloquent;

use Botble\CustomField\Models\FieldItem;
use Botble\CustomField\Repositories\Interfaces\FieldItemInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Collection;

class FieldItemRepository extends RepositoriesAbstract implements FieldItemInterface
{
    public function deleteFieldItem(array|int|string|null $id): int
    {
        return $this->model->whereIn('id', (array)$id)->delete();
    }

    public function getGroupItems(int|string|null $groupId, int|string|null $parentId = null): Collection
    {
        return $this->model
            ->where([
                'field_group_id' => $groupId,
                'parent_id' => $parentId,
            ])
            ->orderBy('order', 'ASC')
            ->get();
    }

    public function updateWithUniqueSlug(int|string|null $id, array $data): ?FieldItem
    {
        $data['slug'] = $this->makeUniqueSlug($id, $data['parent_id'], $data['slug'], $data['position']);

        return $this->createOrUpdate($data, compact('id'));
    }

    protected function makeUniqueSlug(
        int|string|null $id,
        int|string|null $parentId,
        string|null $slug,
        int $position
    ): string|null {
        $isExist = $this->getFirstBy([
            'slug' => $slug,
            'parent_id' => $parentId,
        ]);

        if ($isExist && (int)$id != (int)$isExist->id) {
            return $slug . '_' . time() . $position;
        }

        return $slug;
    }
}
