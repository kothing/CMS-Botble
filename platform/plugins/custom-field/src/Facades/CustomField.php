<?php

namespace Botble\CustomField\Facades;

use Botble\CustomField\Support\CustomFieldSupport as BaseCustomFieldSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\CustomField\Support\CustomFieldSupport expandRuleGroup(string $groupName)
 * @method static \Botble\CustomField\Support\CustomFieldSupport registerRuleGroup(string $groupName)
 * @method static \Botble\CustomField\Support\CustomFieldSupport expandRule(string $group, string $title, string $slug, \Closure|array|string $data)
 * @method static \Botble\CustomField\Support\CustomFieldSupport registerRule(string $group, string $title, string $slug, \Closure|array|string $data)
 * @method static \Botble\CustomField\Support\CustomFieldSupport setRules(array|string|null $rules)
 * @method static \Botble\CustomField\Support\CustomFieldSupport addRule(array|string $ruleName, $value = null)
 * @method static array exportCustomFieldsData(string $morphClass, string|int|null $morphId)
 * @method static string renderRules()
 * @method static string renderCustomFieldBoxes(array $boxes)
 * @method static void renderAssets()
 * @method static bool saveCustomFields(\Illuminate\Http\Request $request, \Botble\Base\Models\BaseModel $data)
 * @method static bool deleteCustomFields(\Botble\Base\Models\BaseModel|null $data)
 * @method static \Botble\CustomField\Support\CustomFieldSupport registerModule(array|string $module)
 * @method static bool isSupportedModule(string $module)
 * @method static array supportedModules()
 * @method static array|string|null getField(\Botble\Base\Models\BaseModel $data, $key = null, $default = null)
 * @method static array|string|null getChildField(array $parentField, string $key, $default = null)
 *
 * @see \Botble\CustomField\Support\CustomFieldSupport
 */
class CustomField extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseCustomFieldSupport::class;
    }
}
