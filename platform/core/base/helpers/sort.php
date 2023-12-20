<?php

use Illuminate\Support\Collection;

if (! function_exists('sort_item_with_children')) {
    function sort_item_with_children(
        Collection|array $list,
        array &$result = [],
        int|string $parent = null,
        int $depth = 0
    ): array {
        if ($list instanceof Collection) {
            $listArr = [];
            foreach ($list as $item) {
                $listArr[] = $item;
            }

            $list = $listArr;
        }

        foreach ($list as $key => $object) {
            if ($object->parent_id == $object->id) {
                $result[] = $object;

                continue;
            }

            if ((int)$object->parent_id == (int)$parent) {
                $result[] = $object;
                $object->depth = $depth;
                unset($list[$key]);
                sort_item_with_children($list, $result, $object->id, $depth + 1);
            }
        }

        return $result;
    }
}
