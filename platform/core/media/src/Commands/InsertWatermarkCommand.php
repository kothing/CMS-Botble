<?php

namespace Botble\Media\Commands;

use Botble\Media\Facades\RvMedia;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('cms:media:insert-watermark', 'Insert watermark for existing images')]
class InsertWatermarkCommand extends Command
{
    public function handle(MediaFileInterface $fileRepository): int
    {
        $this->components->info('Starting to insert watermark...');

        if (! setting('media_watermark_enabled', RvMedia::getConfig('watermark.enabled'))) {
            $this->components->error('Watermark is not enabled!');

            return self::FAILURE;
        }

        $watermarkImage = setting('media_watermark_source', RvMedia::getConfig('watermark.source'));

        if (! $watermarkImage) {
            $this->components->error('Path to watermark image is not correct!');

            return self::FAILURE;
        }

        $watermarkPath = RvMedia::getRealPath($watermarkImage);

        if (! File::exists($watermarkPath)) {
            $this->components->error('Path to watermark image is not correct!');

            return self::FAILURE;
        }

        if ($this->option('folder')) {
            $files = $fileRepository->allBy(
                ['folder_id' => $this->option('folder')],
                [],
                ['url', 'mime_type', 'folder_id']
            );
        } else {
            $files = $fileRepository->allBy([], [], ['url', 'mime_type', 'folder_id']);
        }

        $this->components->info(sprintf('Processing %d %s...', $files->count(), Str::plural('file', $files->count())));

        $errors = [];

        $watermarkImage = setting('media_watermark_source', RvMedia::getConfig('watermark.source'));

        foreach ($files as $file) {
            try {
                if (! $file->canGenerateThumbnails()) {
                    continue;
                }

                if ($file->url == $watermarkImage) {
                    continue;
                }

                $folderIds = json_decode(setting('media_folders_can_add_watermark', ''), true);

                if (empty($folderIds) || in_array($file->folder_id, $folderIds)) {
                    RvMedia::insertWatermark($file->url);
                }
            } catch (Exception $exception) {
                $errors[] = $file->url;
                $this->components->error($exception->getMessage());
            }
        }

        $this->components->info('Inserted watermark successfully!');

        $errors = array_unique($errors);

        $errors = array_map(function ($item) {
            return [$item];
        }, $errors);

        if ($errors) {
            $this->components->info('We are unable to insert watermark for these files:');

            $this->table(['File directory'], $errors);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('folder', 'f', InputOption::VALUE_REQUIRED, 'The folder ID');
    }
}
