<?php

namespace Botble\Backup\Supports;

use Botble\Backup\Supports\MySql\MySqlDump;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Zipper;
use Carbon\Carbon;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class Backup
{
    protected string|null $folder = null;

    public function __construct(protected Filesystem $files, protected Zipper $zipper)
    {
    }

    public function createBackupFolder(string $name, string|null $description = null): array
    {
        $backupFolder = $this->createFolder($this->getBackupPath());
        $now = Carbon::now()->format('Y-m-d-H-i-s');
        $this->folder = $this->createFolder($backupFolder . DIRECTORY_SEPARATOR . $now);

        $file = $this->getBackupPath('backup.json');
        $data = [];

        if (file_exists($file)) {
            $data = BaseHelper::getFileData($file);
        }

        $data[$now] = [
            'name' => $name,
            'description' => $description,
            'date' => Carbon::now()->toDateTimeString(),
        ];

        BaseHelper::saveFileData($file, $data);

        return [
            'key' => $now,
            'data' => $data[$now],
        ];
    }

    public function createFolder(string $folder): string
    {
        $this->files->ensureDirectoryExists($folder);

        return $folder;
    }

    public function getBackupPath(string|null $path = null): string
    {
        return storage_path('app/backup') . ($path ? '/' . $path : null);
    }

    public function getBackupDatabasePath(string $key): string
    {
        return $this->getBackupPath($key . '/database-' . $key . '.zip');
    }

    public function isDatabaseBackupAvailable(string $key): bool
    {
        $file = $this->getBackupDatabasePath($key);

        return file_exists($file) && filesize($file) > 1024;
    }

    public function getBackupList(): array
    {
        $file = $this->getBackupPath('backup.json');
        if (file_exists($file)) {
            return BaseHelper::getFileData($file);
        }

        return [];
    }

    public function backupDb(): bool
    {
        $file = 'database-' . Carbon::now()->format('Y-m-d-H-i-s');
        $path = $this->folder . DIRECTORY_SEPARATOR . $file;

        $mysqlPath = rtrim(config('plugins.backup.general.backup_mysql_execute_path'), '/');

        if (! empty($mysqlPath)) {
            $mysqlPath = $mysqlPath . '/';
        }

        $config = DB::connection('mysql')->getConfig();

        if (! $config) {
            return false;
        }

        $sql = $mysqlPath . 'mysqldump --user="' . $config['username'] . '" --password="' . $config['password'] . '"';

        $sql .= ' --host=' . $config['host'] . ' --port=' . $config['port'] . ' ' . $config['database'] . ' > ' . $path . '.sql';

        try {
            Process::fromShellCommandline($sql)->mustRun();
        } catch (Exception) {
            try {
                if (function_exists('system')) {
                    system($sql);
                } else {
                    $this->processMySqlDumpPHP($path, $config);
                }
            } catch (Exception) {
                $this->processMySqlDumpPHP($path, $config);
            }
        }

        if (! $this->files->exists($path . '.sql') || $this->files->size($path . '.sql') < 1024) {
            $this->processMySqlDumpPHP($path, $config);
        }

        $this->compressFileToZip($path, $path . '.zip');

        if ($this->files->exists($path . '.zip')) {
            chmod($path . '.zip', 0755);
        }

        return true;
    }

    protected function processMySqlDumpPHP(string $path, array $config): bool
    {
        $dump = new MySqlDump('mysql:host=' . $config['host'] . ';dbname=' . $config['database'], $config['username'], $config['password']);
        $dump->start($path . '.sql');

        return true;
    }

    public function compressFileToZip(string $path, string $destination): void
    {
        $this->zipper->compress($path . '.sql', $destination);

        $this->deleteFile($path . '.sql');
    }

    protected function deleteFile(string $file): void
    {
        if ($this->files->exists($file)) {
            $this->files->delete($file);
        }
    }

    public function backupFolder(string $source): bool
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . 'storage-' . Carbon::now()->format('Y-m-d-H-i-s') . '.zip';

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        if (! $this->zipper->compress($source, $file)) {
            $this->deleteFolderBackup($this->folder);
        }

        if (file_exists($file)) {
            chmod($file, 0755);
        }

        return true;
    }

    public function deleteFolderBackup(string $path): void
    {
        $backupFolder = $this->getBackupPath();
        if ($this->files->isDirectory($backupFolder) && $this->files->isDirectory($path)) {
            foreach (BaseHelper::scanFolder($path) as $item) {
                $this->files->delete($path . DIRECTORY_SEPARATOR . $item);
            }
            $this->files->deleteDirectory($path);

            if (empty($this->files->directories($backupFolder))) {
                $this->files->deleteDirectory($backupFolder);
            }
        }

        $file = $this->getBackupPath('backup.json');
        $data = [];

        if (file_exists($file)) {
            $data = BaseHelper::getFileData($file);
        }

        if (! empty($data)) {
            unset($data[Arr::last(explode('/', $path))]);
            BaseHelper::saveFileData($file, $data);
        }
    }

    public function restoreDatabase(string $file, string $path): bool
    {
        $this->extractFileTo($file, $path);
        $file = $path . DIRECTORY_SEPARATOR . $this->files->name($file) . '.sql';

        if (! file_exists($file)) {
            return false;
        }

        $databaseName = DB::connection('mysql')->getConfig()['database'];

        // Force the new login to be used
        DB::purge();
        DB::unprepared('USE `' . $databaseName . '`');
        DB::connection()->setDatabaseName($databaseName);
        DB::getSchemaBuilder()->dropAllTables();
        DB::unprepared(file_get_contents($file));

        $this->deleteFile($file);

        return true;
    }

    public function extractFileTo(string $fileName, string $pathTo): bool
    {
        $this->zipper->extract($fileName, $pathTo);

        return true;
    }

    public function cleanDirectory(string $directory): bool
    {
        foreach ($this->files->glob(rtrim($directory, '/') . '/*') as $item) {
            if ($this->files->isDirectory($item)) {
                $this->files->deleteDirectory($item);
            } elseif (! in_array($this->files->basename($item), ['.htaccess', '.gitignore'])) {
                $this->files->delete($item);
            }
        }

        return true;
    }
}
