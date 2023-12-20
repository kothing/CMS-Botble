<?php

namespace Botble\Media\Models;

use Botble\Base\Models\BaseModel;

class MediaSetting extends BaseModel
{
    protected $table = 'media_settings';

    protected $fillable = [
        'key',
        'value',
        'user_id',
    ];

    protected $casts = [
        'value' => 'json',
    ];
}
