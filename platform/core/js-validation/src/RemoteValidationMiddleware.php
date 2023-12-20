<?php

namespace Botble\JsValidation;

use Botble\JsValidation\Remote\Resolver;
use Botble\JsValidation\Remote\Validator as RemoteValidator;
use Closure;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;

class RemoteValidationMiddleware
{
    /**
     * Field used to detect Javascript validation.
     */
    protected string $field;

    /**
     * Whether to escape messages or not.
     */
    protected bool $escape;

    public function __construct(protected ValidationFactory $factory, Config $config)
    {
        $this->field = $config->get('core.js-validation.js-validation.remote_validation_field');
        $this->escape = (bool)$config->get('core.js-validation.js-validation.escape', false);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has($this->field)) {
            $this->wrapValidator();
        }

        return $next($request);
    }

    /**
     * Wraps Validator resolver with RemoteValidator resolver.
     */
    protected function wrapValidator(): void
    {
        $resolver = new Resolver($this->factory, $this->escape);
        $this->factory->resolver($resolver->resolver($this->field));
        $this->factory->extend(RemoteValidator::EXTENSION_NAME, $resolver->validatorClosure());
    }
}
