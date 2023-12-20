<?php

namespace Botble\Gallery\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Gallery\Repositories\Interfaces\GalleryInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Collection;

class GalleryRepository extends RepositoriesAbstract implements GalleryInterface
{
    public function getAll(array $with = ['slugable', 'user'], int $limit = 0): Collection
    {
        $data = $this->model
            ->with($with)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $data->limit($limit);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getDataSiteMap(): Collection
    {
        $data = $this->model
            ->with('slugable')
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('order')
            ->select(['id', 'name', 'updated_at'])
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    public function getFeaturedGalleries(int $limit, array $with = ['slugable', 'user']): Collection
    {
        $data = $this->model
            ->with($with)
            ->where([
                'status' => BaseStatusEnum::PUBLISHED,
                'is_featured' => 1,
            ])
            ->select([
                'id',
                'name',
                'user_id',
                'image',
                'created_at',
            ])
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
