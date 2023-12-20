@if (count($browsers) > 0)
    <div class="scroller">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('core/base::tables.browser') }}</th>
                    <th>{{ trans('core/base::tables.session') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($browsers as $browser)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td class="text-start">{{ $browser['browser'] }}</td>
                        <td>{{ $browser['sessions'] }} ({{ trans('plugins/analytics::analytics.sessions') }})</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    @include('core/dashboard::partials.no-data')
@endif
