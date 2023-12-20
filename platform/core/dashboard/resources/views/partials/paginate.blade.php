@if ($data instanceof Illuminate\Pagination\LengthAwarePaginator && $data->withQueryString())
    <div class="row g-0">
        <div class="col-3 number_record">
            <div class="f_com">
                <input type="number" class="numb" value="{{ $limit }}" step="5" min="5" max="{{ $data->total() }}">
                <div class="btn_grey btn_change_paginate btn_up"></div>
                <div class="btn_grey btn_change_paginate btn_down"></div>
            </div>
        </div>
        <div class="col-9 widget_pagination">
            <div class="d-flex justify-content-end ">
                @php
                    $info = $data->total() > 0 ? ($data->currentPage() - 1) * $limit + 1 : 0;
                    $info .= '- ' . ($limit < $data->total() ? $data->currentPage() * $limit : $data->total()) . ' ';
                    $info .= trans('core/base::tables.in') . ' ' . $data->total() . ' ' . trans('core/base::tables.records');
                @endphp
                <span class="d-flex align-items-center">{{ $info }}</span>
                <a class="btn btn_grey page_previous @if ($data->onFirstPage()) disabled @endif" href="{{ $data->previousPageUrl() }}"></a>
                <a class="btn btn_grey page_next @if (!$data->hasMorePages()) disabled @endif" href="{{ $data->nextPageUrl() }}"></a>
            </div>
        </div>
    </div>
@endif
