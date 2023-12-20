<?php

namespace Botble\Block\Repositories\Eloquent;

use Botble\Block\Models\Block;
use Botble\Block\Repositories\Interfaces\BlockInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class BlockRepository extends RepositoriesAbstract implements BlockInterface
{
    public function createSlug(string|null $name, int|string|null $id): string
    {
        return Block::createSlug($name, $id, 'alias');
    }
}
