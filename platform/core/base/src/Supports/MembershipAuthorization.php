<?php

namespace Botble\Base\Supports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MembershipAuthorization
{
    protected string $url;

    public function __construct()
    {
        $this->url = rtrim(url('/'), '/');
    }

    public function authorize(): bool
    {
        try {
            if (! filter_var($this->url, FILTER_VALIDATE_URL)) {
                return false;
            }

            if ($this->isInvalidDomain()) {
                return false;
            }

            $authorizeDate = setting('membership_authorization_at');

            if (! $authorizeDate) {
                return $this->processAuthorize();
            }

            $authorizeDate = Carbon::createFromFormat('Y-m-d H:i:s', $authorizeDate);
            if (Carbon::now()->diffInDays($authorizeDate) > 7) {
                return $this->processAuthorize();
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    protected function isInvalidDomain(): bool
    {
        if (filter_var($this->url, FILTER_VALIDATE_IP)) {
            return true;
        }

        $blacklistDomains = [
            'localhost',
            '.local',
            '.test',
            '127.0.0.1',
            '192.',
            'mail.',
            '8000',
        ];

        foreach ($blacklistDomains as $blacklistDomain) {
            if (Str::contains($this->url, $blacklistDomain)) {
                return true;
            }
        }

        return false;
    }

    protected function processAuthorize(): bool
    {
        try {
            $response = Http::withoutVerifying()
                ->asJson()
                ->acceptJson()
                ->post('https://botble.com/membership/authorize', [
                    'website' => $this->url,
                ]);

            if (! $response->ok()) {
                return true;
            }

            setting()
                ->set('membership_authorization_at', Carbon::now()->toDateTimeString())
                ->save();

            return true;
        } catch (Throwable) {
            return true;
        }
    }
}
