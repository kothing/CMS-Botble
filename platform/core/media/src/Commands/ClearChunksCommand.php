<?php

namespace Botble\Media\Commands;

use Botble\Media\Chunks\Storage\ChunkStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('cms:media:chunks:clear', 'Clears the chunks upload directory. Deletes only .part objects.')]
class ClearChunksCommand extends Command
{
    public function handle(ChunkStorage $storage): int
    {
        $verbose = OutputInterface::VERBOSITY_VERBOSE;

        $oldFiles = $storage->oldChunkFiles();

        if ($oldFiles->isEmpty()) {
            $this->components->warn('Chunks: no old files');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d chunk files', $oldFiles->count()), $verbose);
        $deleted = 0;

        foreach ($oldFiles as $file) {
            $this->comment('> ' . $file, $verbose);

            if ($file->delete()) {
                ++$deleted;
            } else {
                $this->components->error('> chunk not deleted: ' . $file);
            }
        }

        $this->components->info(sprintf('Chunks: cleared %d %s', $deleted, Str::plural('file', $deleted)));

        return self::SUCCESS;
    }
}
