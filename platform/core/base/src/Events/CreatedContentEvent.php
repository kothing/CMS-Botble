<?php

namespace Botble\Base\Events;

use Botble\Base\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class CreatedContentEvent extends Event
{
    use SerializesModels;

    public function __construct(public string $screen, public Request $request, public bool|BaseModel|null $data)
    {
    }
}
