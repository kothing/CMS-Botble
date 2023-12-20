<div class="tab-pane" id="tab_history">
    <div class="form-group mb-3" style="min-height: 400px;">
        <table class="table table-bordered table-striped" id="table">
            <thead>
                <tr>
                    <th>{{ trans('core/base::tables.author') }}</th>
                    <th>{{ trans('core/base::tables.column') }}</th>
                    <th>{{ trans('core/base::tables.origin') }}</th>
                    <th>{{ trans('core/base::tables.after_change') }}</th>
                    <th>{{ trans('core/base::tables.created_at') }}</th>
                </tr>
            </thead>
            <tbody>
                @if ($model->revisionHistory !== null && count($model->revisionHistory)>0)
                    @foreach($model->revisionHistory as $history)
                        <tr>
                            <td style="min-width: 145px;">{{ $history->userResponsible() ? $history->userResponsible()->name : 'N/A' }}</td>
                            <td style="min-width: 145px;">{{ $history->fieldName() }}</td>
                            <td>{{ $history->oldValue() }}</td>
                            <td><span class="html-diff-content" data-original="{{ $history->oldValue() }}">{{ $history->newValue() }}</span></td>
                            <td style="min-width: 145px;">{{ BaseHelper::formatDateTime($history->created_at) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr class="text-center">
                        <td colspan="5">{{ trans('core/base::tables.no_record') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
