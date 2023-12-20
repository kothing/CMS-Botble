<?php

namespace Botble\Base\Commands\Traits;

use Illuminate\Support\Facades\Validator;
use Throwable;

trait ValidateCommandInput
{
    protected function askWithValidate(string $message, string $rules, bool $secret = false): string
    {
        do {
            if ($secret) {
                try {
                    $input = $this->secret($message);
                } catch (Throwable) {
                    $input = $this->ask($message);
                }
            } else {
                $input = $this->ask($message);
            }

            $validate = $this->validate(compact('input'), ['input' => $rules]);
            if ($validate['error']) {
                $this->components->error($validate['message']);
            }
        } while ($validate['error']);

        return $input;
    }

    protected function validate(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'error' => true,
                'message' => $validator->messages()->first(),
            ];
        }

        return [
            'error' => false,
        ];
    }
}
