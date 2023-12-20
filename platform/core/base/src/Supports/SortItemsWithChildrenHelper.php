<?php

namespace Botble\Base\Supports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SortItemsWithChildrenHelper
{
    protected array|Collection $items;

    protected string $parentField = 'parent_id';

    protected string $compareKey = 'id';

    protected string $childrenProperty = 'children_items';

    protected array $result = [];

    public function setItems(array|Collection $items): self
    {
        if (! $items instanceof Collection) {
            $items = collect($items);
        }

        $this->items = $items;

        return $this;
    }

    public function setParentField(string $string): self
    {
        $this->parentField = $string;

        return $this;
    }

    public function setCompareKey(string $key): self
    {
        $this->compareKey = $key;

        return $this;
    }

    public function setChildrenProperty(string $string): self
    {
        $this->childrenProperty = $string;

        return $this;
    }

    public function sort(): array
    {
        return $this->processSort();
    }

    protected function processSort(int|string $parentId = 0): array
    {
        $result = [];
        $filtered = $this->items->where($this->parentField, $parentId);
        foreach ($filtered as $item) {
            if (is_object($item)) {
                $item->{$this->childrenProperty} = $this->processSort($item->{$this->compareKey});
            } else {
                $item[$this->childrenProperty] = $this->processSort(Arr::get($item, $this->compareKey));
            }
            $result[] = $item;
        }

        return $result;
    }
}
