<?php

namespace Botble\Api\Http\Requests;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

abstract class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = [];

        foreach ($validator->errors()->toArray() as $key => $message) {
            $errors[] = [
                $key => Arr::first($message),
            ];
        }

        $response = (new BaseHttpResponse())
            ->setError()
            ->setMessage('The given data is invalid')
            ->setData($errors)
            ->setCode(422);

        throw new ValidationException($validator, $response);
    }
}
