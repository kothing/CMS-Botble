<?php

namespace Botble\JsValidation\Javascript;

use Botble\JsValidation\Exceptions\PropertyNotFoundException;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;

class JavascriptValidator implements Arrayable
{
    /**
     * Registered validator instance.
     *
     * @var ValidatorHandler
     */
    protected $validator;

    /**
     * Selector used in javascript generation.
     *
     * @var string
     */
    protected $selector;

    /**
     * View that renders Javascript.
     *
     * @var string
     */
    protected $view;

    /**
     * Enable or disable remote validations.
     *
     * @var bool
     */
    protected $remote;

    /**
     * 'ignore' option for jQuery Validation Plugin.
     *
     * @var string
     */
    protected $ignore;

    /**
     * @param ValidatorHandler $validator
     * @param array $options
     */
    public function __construct(ValidatorHandler $validator, array $options = [])
    {
        $this->validator = $validator;
        $this->setDefaults($options);
    }

    /**
     * Set default parameters.
     *
     * @param $options
     * @return void
     */
    protected function setDefaults($options)
    {
        $this->selector = empty($options['selector']) ? 'form' : $options['selector'];
        $this->view = empty($options['view']) ? 'core/js-validation::bootstrap' : $options['view'];
        $this->remote = $options['remote'] ?? true;

        if (isset($options['ignore'])) {
            $this->ignore = $options['ignore'];
        }
    }

    /**
     * Render the specified view with validator data.
     *
     * @param null|View|string $view
     * @param null|string $selector
     * @return string
     */
    public function render($view = null, $selector = null)
    {
        $this->view($view);
        $this->selector($selector);

        return view($this->view, ['validator' => $this->getViewData()])
            ->render();
    }

    /**
     * Get the view data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getViewData();
    }

    /**
     * Get the string resulting of render default view.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $exception) {
            return trigger_error($exception->__toString(), E_USER_ERROR);
        }
    }

    /**
     * Gets value from view data.
     *
     * @param string $name
     * @return string
     *
     * @throws PropertyNotFoundException
     */
    public function __get($name)
    {
        $data = $this->getViewData();
        if (! array_key_exists($name, $data)) {
            throw new PropertyNotFoundException($name, get_class());
        }

        return $data[$name];
    }

    protected function getViewData(): array
    {
        $this->validator->setRemote($this->remote);
        $data = $this->validator->validationData();
        $data['selector'] = $this->selector;

        if (! empty($this->ignore)) {
            $data['ignore'] = $this->ignore;
        }

        return $data;
    }

    /**
     * Set the form selector to validate.
     *
     * @param string $selector
     * @return JavascriptValidator
     */
    public function selector($selector)
    {
        $this->selector = empty($selector) ? $this->selector : $selector;

        return $this;
    }

    /**
     * Set the input selector to ignore for validation.
     *
     * @param string $ignore
     * @return JavascriptValidator
     */
    public function ignore($ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * Set the view to render Javascript Validations.
     *
     * @param null|View|string $view
     * @return JavascriptValidator
     */
    public function view($view)
    {
        $this->view = empty($view) ? $this->view : $view;

        return $this;
    }

    /**
     * Enables or disables remote validations.
     *
     * @param null|bool $enabled
     * @return JavascriptValidator
     */
    public function remote($enabled = true)
    {
        $this->remote = $enabled;

        return $this;
    }

    /**
     * Validate Conditional Validations using Ajax in specified fields.
     *
     * @param string $attribute
     * @param string|array $rules
     * @return JavascriptValidator
     */
    public function sometimes($attribute, $rules)
    {
        $this->validator->sometimes($attribute, $rules);

        return $this;
    }
}
