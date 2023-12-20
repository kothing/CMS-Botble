<?php

namespace Botble\Base\Supports;

use Botble\Base\Events\SendMailEvent;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

class EmailHandler
{
    protected string $type = 'plugins';

    protected string|null $module = null;

    protected string|null $template = null;

    protected array $templates = [];

    protected array $variableValues = [];

    protected TwigCompiler $twigCompiler;

    public function __construct()
    {
        $this->twigCompiler = new TwigCompiler([
            'autoescape' => false,
        ]);
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): string|null
    {
        return $this->template;
    }

    public function setTemplate(string|null $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getCoreVariables(): array
    {
        return [
            'header' => trans('core/base::base.email_template.header'),
            'footer' => trans('core/base::base.email_template.footer'),
            'site_title' => trans('core/base::base.email_template.site_title'),
            'site_url' => trans('core/base::base.email_template.site_url'),
            'site_logo' => trans('core/base::base.email_template.site_logo'),
            'date_time' => trans('core/base::base.email_template.date_time'),
            'date_year' => trans('core/base::base.email_template.date_year'),
            'site_admin_email' => trans('core/base::base.email_template.site_admin_email'),
        ];
    }

    public function getTwigFunctions(): array
    {
        return [
            'apply' => [
                'label' => trans('core/base::base.email_template.twig.tag.apply'),
                'sample' => "{% apply upper %}\n\tThis text becomes uppercase\n{% endapply %}",
            ],
            'for' => [
                'label' => trans('core/base::base.email_template.twig.tag.for'),
                'sample' => "{% for user in users %}\n\t{{ user.username|e }}\n{% endfor %}",
            ],
            'if' => [
                'label' => trans('core/base::base.email_template.twig.tag.if'),
                'sample' => "{% if online == false %}\n\t<p>Our website is in maintenance mode. Please, come back later.</p>\n{% endif %}",
            ],
        ];
    }

    public function setVariableValue(string $variable, string $value, string $module = null): self
    {
        Arr::set($this->variableValues, ($module ?: $this->module) . '.' . $variable, $value);

        return $this;
    }

    public function getVariableValues(string|null $module = null): array
    {
        $values = apply_filters('cms_email_variable_values', $this->variableValues, $this->template);

        if ($module) {
            return Arr::get($values, $module, []);
        }

        return $values;
    }

    public function setVariableValues(array $data, string|null $module = null): self
    {
        foreach ($data as $name => $value) {
            $this->variableValues[$module ?: $this->module][$name] = $value;
        }

        return $this;
    }

    public function addTemplateSettings(string $module, array|null $data, string $type = 'plugins'): self
    {
        if (empty($data)) {
            return $this;
        }

        $this->module = $module;

        Arr::set($this->templates, $type . '.' . $module, $data);

        foreach ($data['templates'] as $key => &$template) {
            if (! isset($template['variables'])) {
                $this->templates[$type][$module]['templates'][$key]['variables'] = Arr::get($data, 'variables', []);
            }

            $this->templates[$type][$module]['templates'][$key]['path'] = platform_path(
                $type . '/' . $module . '/resources/email-templates/' . $key . '.tpl'
            );
        }

        return $this;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function getTemplateData(string $type, string $module, string $name): string|array|null
    {
        return Arr::get($this->templates, $type . '.' . $module . '.templates.' . $name);
    }

    public function getVariables(string $type, string $module, string $name): array
    {
        $this->template = $name;

        return $this->getCoreVariables() + Arr::get($this->getTemplateData($type, $module, $name), 'variables', []);
    }

    public function getFunctions(): array
    {
        return $this->getTwigFunctions();
    }

    public function sendUsingTemplate(
        string $template,
        string|array|null $email = null,
        array $args = [],
        bool $debug = false,
        string $type = 'plugins',
        $subject = null
    ): bool {
        if (! $this->templateEnabled($template)) {
            return false;
        }

        $this->type = $type;
        $this->template = $template;

        if (! $subject) {
            $subject = $this->getSubject();
        }

        $this->send($this->getContent(), $subject, $email, $args, $debug);

        return true;
    }

    public function templateEnabled(string $template, string $type = 'plugins'): bool
    {
        return (bool)get_setting_email_status($type, $this->module, $template);
    }

    public function send(
        string $content,
        string $title,
        string|array|null $to = null,
        array $args = [],
        bool $debug = false
    ): void {
        try {
            if (empty($to)) {
                $to = get_admin_email()->toArray();
                if (empty($to)) {
                    $to = setting('email_from_address', config('mail.from.address'));
                }
            }

            $content = $this->prepareData($content);
            $title = $this->prepareData($title);

            event(new SendMailEvent($content, $title, $to, $args, $debug));
        } catch (Exception $exception) {
            if ($debug) {
                throw $exception;
            }

            info($exception->getMessage());

            $this->sendErrorException($exception);
        }
    }

    public function prepareData(string $content): string
    {
        $this->initVariableValues();

        if (! empty($content)) {
            $variables = $this->getCoreVariables();

            if ($this->module && $this->template) {
                $variables = $this->getVariables($this->type ?: 'plugins', $this->module, $this->template);
            }

            $content = $this->replaceVariableValue(array_keys($variables), $this->module, $content);
        }

        return apply_filters(BASE_FILTER_EMAIL_TEMPLATE, $content);
    }

    public function initVariableValues(): void
    {
        $now = Carbon::now();

        $this->variableValues['core'] = [
            'header' => apply_filters(
                BASE_FILTER_EMAIL_TEMPLATE_HEADER,
                get_setting_email_template_content('core', 'base', 'header')
            ),
            'footer' => apply_filters(
                BASE_FILTER_EMAIL_TEMPLATE_FOOTER,
                get_setting_email_template_content('core', 'base', 'footer')
            ),
            'site_title' => setting('admin_title') ?: config('app.name'),
            'site_url' => url(''),
            'site_logo' => setting('admin_logo') ? RvMedia::getImageUrl(setting('admin_logo')) : url(
                config('core.base.general.logo')
            ),
            'date_time' => $now->toDateTimeString(),
            'date_year' => $now->year,
            'site_admin_email' => get_admin_email()->first(),
            'now' => $now,
        ];
    }

    protected function replaceVariableValue(array $variables, string $module, string $content): string
    {
        do_action('email_variable_value');

        $data = [];

        $twigCompiler = apply_filters('cms_twig_compiler', $this->twigCompiler);

        foreach ($variables as $variable) {
            $data[$variable] = $this->getVariableValue($variable, $module);
        }

        foreach ($data as $key => $value) {
            $data[$key] = $value && is_string($value) ? $twigCompiler->compile($value, $data) : $value;
        }

        if (empty($data) || empty($content)) {
            return $content;
        }

        return $twigCompiler->compile($content, $data);
    }

    public function getVariableValue(string $variable, string $module, string $default = ''): string|array|null
    {
        $values = $this->getVariableValues();

        $value = Arr::get($values, $module . '.' . $variable, $default);

        if (! $value) {
            $value = Arr::get($values, 'core.' . $variable, $default);
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        return $value;
    }

    public function sendErrorException(Exception $exception): void
    {
        try {
            $ex = FlattenException::create($exception);

            $url = URL::full();
            $error = $this->renderException($exception);

            $this->send(
                view('core/base::emails.error-reporting', compact('url', 'ex', 'error'))->render(),
                $exception->getFile(),
                ! empty(config('core.base.general.error_reporting.to')) ?
                    config('core.base.general.error_reporting.to') :
                    get_admin_email()->toArray()
            );
        } catch (Throwable $ex) {
            info($ex->getMessage());
        }
    }

    protected function renderException(Throwable|Exception $exception): string
    {
        $renderer = new HtmlErrorRenderer(true);

        $exception = $renderer->render($exception);

        if (! headers_sent()) {
            http_response_code($exception->getStatusCode());

            foreach ($exception->getHeaders() as $name => $value) {
                header($name . ': ' . $value, false);
            }
        }

        return $exception->getAsString();
    }

    public function getTemplateContent(string $template, string $type = 'plugins'): string|null
    {
        $this->template = $template;
        $this->type = $type;

        return get_setting_email_template_content($type, $this->module, $template);
    }

    public function getTemplateSubject(string $template, string $type = 'plugins'): string
    {
        return (string)setting(
            get_setting_email_subject_key($type, $this->module, $template),
            trans(
                config(
                    $type . '.' . $this->module . '.email.templates.' . $template . '.subject',
                    ''
                )
            )
        );
    }

    public function getContent(): string
    {
        return $this->prepareData(get_setting_email_template_content($this->type, $this->module, $this->template));
    }

    public function getSubject(): string
    {
        return $this->prepareData($this->getTemplateSubject($this->template, $this->type));
    }
}
