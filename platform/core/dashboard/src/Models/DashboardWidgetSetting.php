<?php

namespace Botble\Dashboard\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidgetSetting extends BaseModel
{
    protected $table = 'dashboard_widget_settings';

    protected $fillable = [
        'settings',
        'widget_id',
        'user_id',
        'order',
        'status',
    ];

    protected $casts = [
        'settings' => 'json',
    ];

    public function widget(): BelongsTo
    {
        return $this->belongsTo(DashboardWidget::class);
    }
}
