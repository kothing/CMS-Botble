<?php

namespace Botble\Member\Http\Resources;

use Botble\Member\Models\MemberActivityLog;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MemberActivityLog
 */
class ActivityLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'description' => $this->getDescription(),
        ];
    }
}
