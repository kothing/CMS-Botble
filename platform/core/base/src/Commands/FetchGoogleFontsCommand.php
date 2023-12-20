<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:google-fonts:fetch', 'Fetch Google Fonts and store them on a local disk')]
class FetchGoogleFontsCommand extends Command
{
    public function handle(): int
    {
        $this->info('Start fetching Google Fonts...');

        $font = $this->argument('font');

        $this->components->info(sprintf('Fetching <comment>%s</comment>...', $font));

        app('core:google-fonts')->load($font, forceDownload: true);

        $this->components->info('All done!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('font', null, 'The font URL');
    }
}
