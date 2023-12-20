<?php

namespace Botble\Backup\Http\Controllers;

use Botble\Backup\Http\Requests\BackupRequest;
use Botble\Backup\Supports\Backup;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BackupController extends BaseController
{
    public function __construct(protected Backup $backup)
    {
    }

    public function getIndex()
    {
        PageTitle::setTitle(trans('plugins/backup::backup.menu_name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/backup/js/backup.js'])
            ->addStylesDirectly(['vendor/core/plugins/backup/css/backup.css']);

        $backupManager = $this->backup;

        $backups = $this->backup->getBackupList();

        return view('plugins/backup::index', compact('backups', 'backupManager'));
    }

    public function store(BackupRequest $request, BaseHttpResponse $response)
    {
        try {
            BaseHelper::maximumExecutionTimeAndMemoryLimit();

            $data = $this->backup->createBackupFolder($request->input('name'), $request->input('description'));
            $this->backup->backupDb();
            $this->backup->backupFolder(config('filesystems.disks.public.root'));

            do_action(BACKUP_ACTION_AFTER_BACKUP, BACKUP_MODULE_SCREEN_NAME, $request);

            $data['backupManager'] = $this->backup;

            return $response
                ->setData(view('plugins/backup::partials.backup-item', $data)->render())
                ->setMessage(trans('plugins/backup::backup.create_backup_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(string $folder, BaseHttpResponse $response)
    {
        try {
            $this->backup->deleteFolderBackup($this->backup->getBackupPath($folder));

            return $response->setMessage(trans('plugins/backup::backup.delete_backup_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getRestore(string $folder, Request $request, BaseHttpResponse $response)
    {
        try {
            $path = $this->backup->getBackupPath($folder);

            $hasSQL = false;

            foreach (BaseHelper::scanFolder($path) as $file) {
                if (Str::contains(basename($file), 'database')) {
                    $hasSQL = true;
                    $this->backup->restoreDatabase($path . DIRECTORY_SEPARATOR . $file, $path);
                }
            }

            if (! $hasSQL) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/backup::backup.cannot_restore_database'));
            }

            foreach (BaseHelper::scanFolder($path) as $file) {
                if (Str::contains(basename($file), 'storage')) {
                    $pathTo = config('filesystems.disks.public.root');
                    $this->backup->cleanDirectory($pathTo);
                    $this->backup->extractFileTo($path . DIRECTORY_SEPARATOR . $file, $pathTo);
                }
            }

            setting()->set('media_random_hash', md5((string)time()))->save();

            Helper::clearCache();

            do_action(BACKUP_ACTION_AFTER_RESTORE, BACKUP_MODULE_SCREEN_NAME, $request);

            return $response->setMessage(trans('plugins/backup::backup.restore_backup_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getDownloadDatabase(string $folder, BaseHttpResponse $response)
    {
        $path = $this->backup->getBackupPath($folder);

        foreach (BaseHelper::scanFolder($path) as $file) {
            if (Str::contains(basename($file), 'database')) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $response
            ->setError()
            ->setMessage(trans('plugins/backup::backup.database_backup_not_existed'));
    }

    public function getDownloadUploadFolder(string $folder, BaseHttpResponse $response)
    {
        $path = $this->backup->getBackupPath($folder);

        foreach (BaseHelper::scanFolder($path) as $file) {
            if (Str::contains(basename($file), 'storage')) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $response
            ->setError()
            ->setMessage(trans('plugins/backup::backup.uploads_folder_backup_not_existed'));
    }
}
