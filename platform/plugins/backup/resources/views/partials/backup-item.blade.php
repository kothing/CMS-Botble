<tr class="@if (!empty($odd) && $odd) odd @else even @endif">
    <td>{{ $data['name'] }}</td>
    <td>@if ($data['description']) {{ $data['description'] }} @else &mdash; @endif</td>
    <td>{{ BaseHelper::humanFilesize(get_backup_size($key)) }}</td>
    <td style="width: 250px;">{{ $data['date'] }}</td>
    <td style="width: 150px;">
        @if ($backupManager->isDatabaseBackupAvailable($key))
            <a href="{{ route('backups.download.database', $key) }}" class="text-success" data-bs-toggle="tooltip" title="{{ trans('plugins/backup::backup.download_database') }}"><i class="icon icon-database"></i></a>
        @endif

        <a href="{{ route('backups.download.uploads.folder', $key) }}" class="text-primary" data-bs-toggle="tooltip" title="{{ trans('plugins/backup::backup.download_uploads_folder') }}"><i class="icon icon-download"></i></a>

        @if (auth()->user()->hasPermission('backups.destroy'))
            <a href="#" data-section="{{ route('backups.destroy', $key) }}" class="text-danger deleteDialog" data-bs-toggle="tooltip" title="{{ trans('core/base::tables.delete_entry') }}"><i class="icon icon-trash"></i></a>
        @endif

        @if (auth()->user()->hasPermission('backups.restore'))
            <a href="#" data-section="{{ route('backups.restore', $key) }}" class="text-info restoreBackup" data-bs-toggle="tooltip" title="{{ trans('plugins/backup::backup.restore_tooltip') }}"><i class="icon icon-publish"></i></a>
        @endif
    </td>
</tr>
