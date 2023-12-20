<div class="wrapper-filter">
    <p>{{ trans('core/table::table.filters') }}</p>

    <input type="hidden" class="filter-data-url" value="{{ route('tables.get-filter-input') }}">

    <div class="sample-filter-item-wrap hidden">
        <div class="filter-item form-filter">
            {!! Form::customSelect('filter_columns[]', array_combine(array_keys($columns), array_column($columns, 'title')), null, ['class' => 'filter-column-key', 'wrapper_class' => 'mb-0']) !!}

            {!! Form::customSelect('filter_operators[]', [
                'like' => trans('core/table::table.contains'),
                '=' => trans('core/table::table.is_equal_to'),
                '>' => trans('core/table::table.greater_than'),
                '<' => trans('core/table::table.less_than'),
            ], null, ['class' => 'filter-operator filter-column-operator', 'wrapper_class' => 'mb-0']) !!}
            <span class="filter-column-value-wrap">
                <input class="form-control filter-column-value" type="text" placeholder="{{ trans('core/table::table.value') }}"
                       name="filter_values[]">
            </span>
            <span class="btn-remove-filter-item" title="{{ trans('core/table::table.delete') }}">
                <i class="fa fa-trash text-danger"></i>
            </span>
        </div>
    </div>

    {{ Form::open(['method' => 'GET', 'class' => 'filter-form']) }}
        <input type="hidden" name="filter_table_id" class="filter-data-table-id" value="{{ $tableId }}">
        <input type="hidden" name="class" class="filter-data-class" value="{{ $class }}">
        <div class="filter_list inline-block filter-items-wrap">
            @foreach($requestFilters as $filterItem)
                <div class="filter-item form-filter @if ($loop->first) filter-item-default @endif">
                    {!! Form::customSelect('filter_columns[]', ['' => trans('core/table::table.select_field')] + array_combine(array_keys($columns), array_column($columns, 'title')), $filterItem['column'], ['class' => 'filter-column-key', 'wrapper_class' => 'mb-0']) !!}

                    {!! Form::customSelect('filter_operators[]', [
                        'like' => trans('core/table::table.contains'),
                        '=' => trans('core/table::table.is_equal_to'),
                        '>' => trans('core/table::table.greater_than'),
                        '<' => trans('core/table::table.less_than'),
                    ], $filterItem['operator'], ['class' => 'filter-operator filter-column-operator', 'wrapper_class' => 'mb-0']) !!}
                    <span class="filter-column-value-wrap">
                        <input class="form-control filter-column-value" type="text" placeholder="{{ trans('core/table::table.value') }}"
                               name="filter_values[]" value="{{ $filterItem['value'] }}">
                    </span>
                    @if ($loop->first)
                        <span class="btn-reset-filter-item" title="{{ trans('core/table::table.reset') }}">
                            <i class="fa fa-eraser text-info" style="font-size: 13px;"></i>
                        </span>
                    @else
                        <span class="btn-remove-filter-item" title="{{ trans('core/table::table.delete') }}">
                            <i class="fa fa-trash text-danger"></i>
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
        <div style="margin-top: 10px;">
            <a href="javascript:;" class="btn btn-secondary add-more-filter">{{ trans('core/table::table.add_additional_filter') }}</a>
            <a href="{{ URL::current() }}" class="btn btn-info @if (!request()->has('filter_table_id')) hidden @endif">{{ trans('core/table::table.reset') }}</a>
            <button type="submit" class="btn btn-primary btn-apply">{{ trans('core/table::table.apply') }}</button>
        </div>

    {{ Form::close() }}
</div>
