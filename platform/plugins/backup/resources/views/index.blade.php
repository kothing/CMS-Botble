@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="clearfix"></div>
    @if (!function_exists('proc_open'))
        <div class="note note-warning">
            <p>{!! BaseHelper::clean(trans('plugins/backup::backup.proc_open_disabled_error')) !!}</p>
        </div>
    @endif

    <div class="note note-warning">
        <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message1')) !!}</p>
        <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message2')) !!}</p>
        <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message3')) !!}</p>
        <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message4')) !!}</p>
    </div>

    @if (auth()->user()->hasPermission('backups.create'))
        <p><button class="btn btn-primary" id="generate_backup">{{ trans('plugins/backup::backup.generate_btn') }}</button></p>
    @endif

    <table class="table table-striped" id="table-backups">
        <thead>
            <tr>
                <th>{{ trans('core/base::tables.name') }}</th>
                <th>{{ trans('core/base::tables.description') }}</th>
                <th>{{ trans('plugins/backup::backup.size') }}</th>
                <th>{{ trans('core/base::tables.created_at') }}</th>
                <th>{{ trans('core/table::table.operations') }}</th>
            </tr>
        </thead>
        <tbody>
            @if (count($backups) > 0)
                @foreach($backups as $key => $backup)
                    @include('plugins/backup::partials.backup-item', ['data' => $backup, 'backupManager' => $backupManager, 'key' => $key, 'odd' => $loop->index % 2 == 0])
                @endforeach
            @else
                <tr class="text-center no-backup-row">
                    <td colspan="5">{{ trans('plugins/backup::backup.no_backups') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if (auth()->user()->hasPermission('backups.create'))
        <x-core-base::modal
            id="create-backup-modal"
            :title="trans('plugins/backup::backup.create')"
            type="info"
            button-id="create-backup-button"
            :button-label="trans('plugins/backup::backup.create_btn')"
        >
            <div class="form-group mb-3">
                <label for="name" class="control-label required">{{ trans('core/base::forms.name') }}</label>
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'id' => 'name', 'placeholder' => trans('core/base::forms.name'), 'data-counter' => 120]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="description" class="control-label">{{ trans('core/base::forms.description') }}</label>
                {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'rows' => 4, 'id' => 'description', 'placeholder' => trans('core/base::forms.description'), 'data-counter' => 400]) !!}
            </div>

        </x-core-base::modal>
        <div data-route-create="{{ route('backups.create') }}"></div>
    @endif

    @if (auth()->user()->hasPermission('backups.restore'))
        <x-core-base::modal
            id="restore-backup-modal"
            :title="trans('plugins/backup::backup.restore')"
            type="info"
            button-id="restore-backup-button"
            :button-label="trans('plugins/backup::backup.restore_btn')"
        >
            {!! trans('plugins/backup::backup.restore_confirm_msg') !!}
        </x-core-base::modal>
    @endif

    @include('core/table::modal')
@stop
