<?php

namespace Botble\Captcha;

use Botble\Captcha\Contracts\Captcha as CaptchaContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class CaptchaV3 extends CaptchaContract
{
    protected bool $rendered = false;

    public function verify(string $response, string $clientIp, array $options = []): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        $response = Http::asForm()
            ->withoutVerifying()
            ->post(self::RECAPTCHA_VERIFY_API_URL, [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => $clientIp,
            ]);

        $data = $response->json();

        if (! isset($data['success']) || ! $data['success']) {
            return false;
        }

        $action = $options[0];
        $minScore = isset($options[1]) ? (float) $options[1] : 0.5;

        if ($action && (! isset($data['action']) || $action != $data['action'])) {
            return false;
        }

        $score = $data['score'] ?? false;

        return $score && $score >= $minScore;
    }

    public function display(array $attributes = ['action' => 'form'], array $options = []): string|null
    {
        if (! $this->siteKey || ! $this->isEnabled()) {
            return null;
        }

        $name = Arr::get($options, 'name', self::RECAPTCHA_INPUT_NAME);
        $uniqueId = uniqid($name . '-');
        $action = Arr::get($attributes, 'action', 'form');
        $isRendered = $this->rendered;

        add_filter(THEME_FRONT_FOOTER, function (string|null $html) use ($isRendered, $uniqueId, $action): string {
            $url = self::RECAPTCHA_CLIENT_API_URL . '?' . http_build_query([
                    'onload' => 'onloadCallback',
                    'render' => $this->siteKey,
                    'hl' => app()->getLocale(),
                ]);

            return $html . view('plugins/captcha::v3.script', [
                'siteKey' => $this->siteKey,
                'id' => $uniqueId,
                'action' => $action,
                'url' => $url,
                'isRendered' => $isRendered,
            ])->render();
        }, 99);

        $this->rendered = true;

        return view('plugins/captcha::v3.html', compact('name', 'uniqueId'))->render();
    }
}
