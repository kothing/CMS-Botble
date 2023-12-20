@if (count($pages) > 0)
    <div class="scroller">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('core/base::tables.url') }}</th>
                    <th>{{ trans('core/base::tables.views') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td class="text-start"><a href="{{ $page['url'] }}" target="_blank">{{ Str::limit($page['pageTitle']) }}</a></td>
                        <td>{{ $page['pageViews'] }} ({{ ucfirst(trans('plugins/analytics::analytics.views')) }})</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    @include('core/dashboard::partials.no-data')
@endif
