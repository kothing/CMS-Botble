<?php

namespace Botble\Captcha;

use Botble\Base\Facades\Html;
use Exception;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;

class MathCaptcha
{
    public function __construct(protected SessionManager|Store|null $session = null)
    {
    }

    /**
     * Returns the math question as string. The second operand is always a larger
     * number then the first one. So it's on first position because we don't want
     * any negative results.
     */
    public function label(): string
    {
        $label = $this->getMathLabelOnly();

        return __('Please solve the following math function: :label = ?', compact('label'));
    }

    public function getMathLabelOnly(): string
    {
        return sprintf(
            '%d %s %d',
            $this->getMathSecondOperator(),
            $this->getMathOperand(),
            $this->getMathFirstOperator()
        );
    }

    public function input(array $attributes = []): string
    {
        $default = [];
        $default['type'] = 'text';
        $default['id'] = 'math-captcha';
        $default['name'] = 'math-captcha';
        $default['required'] = 'required|string';
        $default['value'] = old('math-captcha');

        $attributes = array_merge($default, $attributes);

        return '<input ' . Html::attributes($attributes) . '>';
    }

    public function verify(string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        return $value == $this->getMathResult();
    }

    /**
     * Reset the math operators to regenerate a new question.
     */
    public function reset(): void
    {
        $this->session->forget('math-captcha.first');
        $this->session->forget('math-captcha.second');
        $this->session->forget('math-captcha.operand');
    }

    /**
     * Operand to be used ('*','-','+')
     */
    protected function getMathOperand(): string
    {
        if (! $this->session->get('math-captcha.operand')) {
            $this->session->put(
                'math-captcha.operand',
                config(
                    'plugins.captcha.general.math-captcha.operands.' . array_rand(
                        config('plugins.captcha.general.math-captcha.operands')
                    )
                )
            );
        }

        return $this->session->get('math-captcha.operand');
    }

    /**
     * The first math operand.
     */
    protected function getMathFirstOperator(): int
    {
        if (! $this->session->get('math-captcha.first')) {
            $this->session->put(
                'math-captcha.first',
                rand(
                    config('plugins.captcha.general.math-captcha.rand-min'),
                    config('plugins.captcha.general.math-captcha.rand-max')
                )
            );
        }

        return $this->session->get('math-captcha.first');
    }

    /**
     * The second math operand
     */
    protected function getMathSecondOperator(): int
    {
        if (! $this->session->get('math-captcha.second')) {
            $this->session->put(
                'math-captcha.second',
                $this->getMathFirstOperator() + rand(
                    config('plugins.captcha.general.math-captcha.rand-min'),
                    config('plugins.captcha.general.math-captcha.rand-max')
                )
            );
        }

        return $this->session->get('math-captcha.second');
    }

    protected function getMathResult(): float|int
    {
        return match ($this->getMathOperand()) {
            '+' => $this->getMathFirstOperator() + $this->getMathSecondOperator(),
            '*' => $this->getMathFirstOperator() * $this->getMathSecondOperator(),
            '-' => abs($this->getMathFirstOperator() - $this->getMathSecondOperator()),
            default => throw new Exception('Math captcha uses an unknown operand.'),
        };
    }
}
