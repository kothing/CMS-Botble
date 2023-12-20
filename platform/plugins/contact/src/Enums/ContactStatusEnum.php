<?php

namespace Botble\Contact\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static ContactStatusEnum UNREAD()
 * @method static ContactStatusEnum READ()
 */
class ContactStatusEnum extends Enum
{
    public const READ = 'read';
    public const UNREAD = 'unread';

    public static $langPath = 'plugins/contact::contact.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::UNREAD => Html::tag('span', self::UNREAD()->label(), ['class' => 'label-warning status-label'])
                ->toHtml(),
            self::READ => Html::tag('span', self::READ()->label(), ['class' => 'label-success status-label'])
                ->toHtml(),
            default => parent::toHtml(),
        };
    }
}
