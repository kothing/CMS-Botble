<?php

namespace Botble\Media\Commands;

use Botble\Media\Facades\RvMedia;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:media:thumbnail:delete', 'Delete thumbnails for all images')]
class DeleteThumbnailCommand extends Command
{
    public function handle(MediaFileInterface $fileRepository): int
    {
        $files = $fileRepository->allBy([], [], ['url', 'mime_type', 'folder_id']);

        $errors = [];

        $description = sprintf('Processing %d %s...', $files->count(), Str::plural('file', $files->count()));

        $this->newLine();
        $this->components->task($description, function () use ($files, &$errors) {
            $this->newLine(2);
            foreach ($files as $file) {
                if (! $file->canGenerateThumbnails()) {
                    continue;
                }

                $this->components->info(sprintf('Processing %s', $file->url));

                try {
                    RvMedia::deleteThumbnails($file);
                } catch (Exception $exception) {
                    $errors[] = $file->url;
                    $this->components->error($exception->getMessage());
                }
            }
        });

        $this->components->info('Thumbnails deleted');

        $errors = array_unique($errors);

        $errors = array_map(function ($item) {
            return [$item];
        }, $errors);

        if ($errors) {
            $this->components->info('We are unable to regenerate thumbnail for these files:');

            $this->table(['File directory'], $errors);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
