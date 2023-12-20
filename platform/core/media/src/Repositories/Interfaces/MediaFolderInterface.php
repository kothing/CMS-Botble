<?php

namespace Botble\Media\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface MediaFolderInterface extends RepositoryInterface
{
    public function getFolderByParentId(int|string|null $folderId, array $params = [], bool $withTrash = false);

    public function createSlug(string $name, int|string|null $parentId);

    public function createName(string $name, int|string|null $parentId);

    public function getBreadcrumbs(int|string|null $parentId, array $breadcrumbs = []);

    public function getTrashed(int|string|null $parentId, array $params = []);

    public function deleteFolder(int|string|null $folderId, bool $force = false);

    public function getAllChildFolders(int|string|null $parentId, array $child = []);

    public function getFullPath(int|string|null $folderId, string|null $path = '');

    public function restoreFolder(int|string|null $folderId);

    public function emptyTrash(): bool;
}
