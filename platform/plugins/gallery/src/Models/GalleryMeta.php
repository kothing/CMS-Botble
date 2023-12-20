<?php

namespace Botble\Gallery\Models;

use Botble\Base\Models\BaseModel;

class GalleryMeta extends BaseModel
{
    protected $table = 'gallery_meta';

    protected $casts = [
        'images' => 'json',
    ];
}
