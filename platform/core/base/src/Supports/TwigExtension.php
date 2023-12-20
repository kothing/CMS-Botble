<?php

namespace Botble\Base\Supports;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension implements ExtensionInterface
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('trans', 'trans'),
        ];
    }
}
