<?php

namespace Botble\Menu\Repositories\Interfaces;

use Botble\Base\Models\BaseModel;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface MenuInterface extends RepositoryInterface
{
    public function findBySlug(string $slug, bool $active, array $select = [], array $with = []): BaseModel|null;

    public function createSlug(string $name): string;
}
