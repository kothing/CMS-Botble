<?php

namespace Botble\Base\Events;

use Botble\Base\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UpdatedContentEvent extends Event
{
    use SerializesModels;

    public string $screen;

    public function __construct(string|BaseModel $screen, public Request $request, public bool|BaseModel|null $data)
    {
        if ($screen instanceof BaseModel) {
            $screen = $screen->getTable();
        }

        $this->screen = $screen;
    }
}
