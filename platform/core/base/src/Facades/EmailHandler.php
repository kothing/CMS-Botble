<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\EmailHandler as EmailHandlerSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Botble\Base\Supports\EmailHandler setModule(string $module)
 * @method static string getType()
 * @method static \Botble\Base\Supports\EmailHandler setType(string $type)
 * @method static string|null getTemplate()
 * @method static \Botble\Base\Supports\EmailHandler setTemplate(string|null $template)
 * @method static array getCoreVariables()
 * @method static array getTwigFunctions()
 * @method static \Botble\Base\Supports\EmailHandler setVariableValue(string $variable, string $value, string|null $module = null)
 * @method static array getVariableValues(string|null $module = null)
 * @method static \Botble\Base\Supports\EmailHandler setVariableValues(array $data, string|null $module = null)
 * @method static \Botble\Base\Supports\EmailHandler addTemplateSettings(string $module, array|null $data, string $type = 'plugins')
 * @method static array getTemplates()
 * @method static array|string|null getTemplateData(string $type, string $module, string $name)
 * @method static array getVariables(string $type, string $module, string $name)
 * @method static array getFunctions()
 * @method static bool sendUsingTemplate(string $template, array|string|null $email = null, array $args = [], bool $debug = false, string $type = 'plugins', $subject = null)
 * @method static bool templateEnabled(string $template, string $type = 'plugins')
 * @method static void send(string $content, string $title, array|string|null $to = null, array $args = [], bool $debug = false)
 * @method static string prepareData(string $content)
 * @method static void initVariableValues()
 * @method static array|string|null getVariableValue(string $variable, string $module, string $default = '')
 * @method static void sendErrorException(\Exception $exception)
 * @method static string|null getTemplateContent(string $template, string $type = 'plugins')
 * @method static string getTemplateSubject(string $template, string $type = 'plugins')
 * @method static string getContent()
 * @method static string getSubject()
 *
 * @see \Botble\Base\Supports\EmailHandler
 */
class EmailHandler extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EmailHandlerSupport::class;
    }
}
