<?php

namespace Botble\ACL\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static UserStatusEnum ACTIVATED()
 * @method static UserStatusEnum DEACTIVATED()
 */
class UserStatusEnum extends Enum
{
    public const ACTIVATED = 'activated';
    public const DEACTIVATED = 'deactivated';

    public static $langPath = 'core/acl::users.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::ACTIVATED => Html::tag('span', self::ACTIVATED()->label(), ['class' => 'label-info status-label'])
                ->toHtml(),
            self::DEACTIVATED => Html::tag(
                'span',
                self::DEACTIVATED()->label(),
                ['class' => 'label-warning status-label']
            )
                ->toHtml(),
            default => parent::toHtml(),
        };
    }
}
