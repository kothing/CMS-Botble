@if ($requests->isNotEmpty())
    <div class="scroller">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('core/base::tables.url') }}</th>
                    <th>{{ trans('plugins/request-log::request-log.status_code') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><a href="{{ $request->url }}" target="_blank" title="{{ $request->url }}">{{ Str::limit($request->url, 70) }}</a></td>
                    <td>{{ $request->status_code }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@if ($requests instanceof Illuminate\Pagination\LengthAwarePaginator && $requests->total() > $limit)
    <div class="widget_footer">
        @include('core/dashboard::partials.paginate', ['data' => $requests, 'limit' => $limit])
    </div>
@endif

@else
    @include('core/dashboard::partials.no-data', ['message' => trans('plugins/request-log::request-log.no_request_error')])
@endif
