<?php

namespace Botble\Theme\Events;

use Botble\Base\Events\Event;
use Botble\Slug\Models\Slug;
use Illuminate\Queue\SerializesModels;

class RenderingSingleEvent extends Event
{
    use SerializesModels;

    public function __construct(public Slug $slug)
    {
    }
}
