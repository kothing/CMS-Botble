<?php

namespace Botble\Blog\Repositories\Interfaces;

use Botble\Blog\Models\Post;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PostInterface extends RepositoryInterface
{
    public function getFeatured(int $limit = 5, array $with = []): Collection;

    public function getListPostNonInList(array $selected = [], int $limit = 7, array $with = []): Collection;

    public function getRelated(int|string $id, int $limit = 3): Collection;

    public function getRelatedCategoryIds(Post|int|string $model): array;

    public function getByCategory(array|int|string $categoryId, int $paginate = 12, int $limit = 0): Collection|LengthAwarePaginator;

    public function getByUserId(int|string $authorId, int $paginate = 6): Collection|LengthAwarePaginator;

    public function getDataSiteMap(): Collection|LengthAwarePaginator;

    public function getByTag(int|string $tag, int $paginate = 12): Collection|LengthAwarePaginator;

    public function getRecentPosts(int $limit = 5, int|string $categoryId = 0): Collection;

    public function getSearch(string|null $keyword, int $limit = 10, int $paginate = 10): Collection|LengthAwarePaginator;

    public function getAllPosts(int $perPage = 12, bool $active = true, array $with = ['slugable']): Collection|LengthAwarePaginator;

    public function getPopularPosts(int $limit, array $args = []): Collection;

    public function getFilters(array $filters): Collection|LengthAwarePaginator;
}
