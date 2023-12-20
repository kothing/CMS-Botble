<?php

namespace Botble\Language\Models;

use Botble\Base\Models\BaseModel;
use Botble\Language\Facades\Language as LanguageFacade;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageMeta extends BaseModel
{
    protected $primaryKey = 'lang_meta_id';

    protected $table = 'language_meta';

    public $timestamps = false;

    protected $fillable = [
        'lang_meta_code',
        'lang_meta_origin',
        'reference_id',
        'reference_type',
    ];

    public function reference(): BelongsTo
    {
        return $this->morphTo();
    }

    public static function saveMetaData(
        BaseModel $model,
        string|null $locale = null,
        string|null $originValue = null
    ): void {
        if (! $locale) {
            $locale = LanguageFacade::getDefaultLocaleCode();
        }

        if (! $originValue) {
            $originValue = md5($model->getKey() . get_class($model) . time());
        }

        LanguageMeta::create([
            'reference_id' => $model->getKey(),
            'reference_type' => get_class($model),
            'lang_meta_code' => $locale,
            'lang_meta_origin' => $originValue,
        ]);
    }
}
