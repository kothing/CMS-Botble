<?php

namespace Botble\CustomField\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;

interface FieldGroupInterface extends RepositoryInterface
{
    public function getFieldGroups(array $condition = []): Collection;

    public function createFieldGroup(array $data);

    public function createOrUpdateFieldGroup(int|string|null $id, array $data);

    public function updateFieldGroup(int|string|null $id, array $data);

    public function getFieldGroupItems(
        $groupId,
        $parentId = null,
        $withValue = false,
        $morphClass = null,
        $morphId = null
    );
}
