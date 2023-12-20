<?php

namespace Botble\Base\Supports;

class RepositoryHelper
{
    public static function applyBeforeExecuteQuery($data, $model, bool $isSingle = false)
    {
        $filter = $isSingle ? BASE_FILTER_BEFORE_GET_SINGLE : BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM;

        if (is_in_admin()) {
            $filter = $isSingle ? BASE_FILTER_BEFORE_GET_ADMIN_SINGLE_ITEM : BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM;
        }

        return apply_filters($filter, $data, $model);
    }
}
