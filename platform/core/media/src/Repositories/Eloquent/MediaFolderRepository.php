<?php

namespace Botble\Media\Repositories\Eloquent;

use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * @since 19/08/2015 07:45 AM
 */
class MediaFolderRepository extends RepositoriesAbstract implements MediaFolderInterface
{
    public function getFolderByParentId(int|string|null $folderId, array $params = [], bool $withTrash = false)
    {
        $params = array_merge([
            'condition' => [],
        ], $params);

        if (! $folderId) {
            $folderId = null;
        }

        $this->model = $this->model->where('parent_id', $folderId);

        if ($withTrash) {
            $this->model = $this->model->withTrashed();
        }

        return $this->advancedGet($params);
    }

    public function createSlug(string $name, int|string|null $parentId)
    {
        $slug = Str::slug($name, '-', ! RvMedia::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);
        $index = 1;
        $baseSlug = $slug;
        while ($this->checkIfExists('slug', $slug, $parentId)) {
            $slug = $baseSlug . '-' . $index++;
        }

        return $slug;
    }

    public function createName(string $name, int|string|null $parentId)
    {
        $newName = $name;
        $index = 1;
        $baseSlug = $newName;
        while ($this->checkIfExists('name', $newName, $parentId)) {
            $newName = $baseSlug . '-' . $index++;
        }

        return $newName;
    }

    protected function checkIfExists(string $key, string $value, int|string|null $parentId): bool
    {
        return $this->model->where($key, $value)->where('parent_id', $parentId)->withTrashed()->exists();
    }

    public function getBreadcrumbs(int|string|null $parentId, array $breadcrumbs = [])
    {
        if (! $parentId) {
            return $breadcrumbs;
        }

        $folder = $this->getFirstByWithTrash(['id' => $parentId]);

        if (empty($folder)) {
            return $breadcrumbs;
        }

        $child = $this->getBreadcrumbs($folder->parent_id, $breadcrumbs);

        return array_merge($child, [
            [
                'id' => $folder->id,
                'name' => $folder->name,
            ],
        ]);
    }

    public function getTrashed(int|string|null $parentId, array $params = [])
    {
        $params = array_merge([
            'where' => [],
        ], $params);
        $data = $this->model
            ->select('media_folders.*')
            ->where($params['where'])
            ->orderBy('media_folders.name')
            ->onlyTrashed();

        /**
         * @var Builder $data
         */
        if (! $parentId) {
            $data->leftJoin('media_folders as mf_parent', 'mf_parent.id', '=', 'media_folders.parent_id')
                ->where(function ($query) {
                    /**
                     * @var Builder $query
                     */
                    $query
                        ->orWhere('media_folders.parent_id', 0)
                        ->orWhere('mf_parent.deleted_at', null);
                })
                ->withTrashed();
        } else {
            $data->where('media_folders.parent_id', $parentId);
        }

        return $data->get();
    }

    public function deleteFolder(int|string|null $folderId, bool $force = false)
    {
        $child = $this->getFolderByParentId($folderId, [], $force);
        foreach ($child as $item) {
            $this->deleteFolder($item->id, $force);
        }

        if ($force) {
            $this->forceDelete(['id' => $folderId]);
        } else {
            $this->deleteBy(['id' => $folderId]);
        }
    }

    public function getAllChildFolders(int|string|null $parentId, array $child = [])
    {
        if (! $parentId) {
            return $child;
        }

        $folders = $this->allBy(['parent_id' => $parentId]);

        if (! empty($folders)) {
            foreach ($folders as $folder) {
                $child[$parentId][] = $folder;

                return $this->getAllChildFolders($folder->id, $child);
            }
        }

        return $child;
    }

    public function getFullPath(int|string|null $folderId, string|null $path = '')
    {
        if (! $folderId) {
            return $path;
        }

        $folder = $this->getFirstByWithTrash(['id' => $folderId]);

        if (empty($folder)) {
            return $path;
        }

        $parent = $this->getFullPath($folder->parent_id, $path);

        if (! $parent) {
            return $folder->slug;
        }

        return rtrim($parent, '/') . '/' . $folder->slug;
    }

    public function restoreFolder(int|string|null $folderId)
    {
        $child = $this->getFolderByParentId($folderId, [], true);
        foreach ($child as $item) {
            $this->restoreFolder($item->id);
        }

        $this->restoreBy(['id' => $folderId]);
    }

    public function emptyTrash(): bool
    {
        $folders = $this->model->onlyTrashed();

        $folders = $folders->get();
        foreach ($folders as $folder) {
            /**
             * @var MediaFolder $folder
             */
            $folder->forceDelete();
        }

        return true;
    }
}
