<?php

namespace Botble\Base\Events;

use Illuminate\Queue\SerializesModels;

class SendMailEvent extends Event
{
    use SerializesModels;

    public function __construct(
        public string $content,
        public string $title,
        public array|string|null $to,
        public array $args,
        public bool $debug = false
    ) {
    }
}
