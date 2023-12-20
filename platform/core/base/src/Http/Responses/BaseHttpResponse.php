<?php

namespace Botble\Base\Http\Responses;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class BaseHttpResponse extends Response implements Responsable
{
    protected bool $error = false;

    protected mixed $data = null;

    protected string|null $message = null;

    protected string|null $previousUrl = '';

    protected string|null $nextUrl = '';

    protected bool $withInput = false;

    protected array $additional = [];

    protected int $code = 200;

    public string $saveAction = 'save';

    public function setData(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setPreviousUrl(string $previousUrl): self
    {
        $this->previousUrl = $previousUrl;

        return $this;
    }

    public function setNextUrl(string $nextUrl): self
    {
        $this->nextUrl = $nextUrl;

        return $this;
    }

    public function withInput(bool $withInput = true): self
    {
        $this->withInput = $withInput;

        return $this;
    }

    public function setCode(int $code): self
    {
        if ($code < 100 || $code >= 600) {
            return $this;
        }

        $this->code = $code;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string|null $message): self
    {
        $this->message = BaseHelper::clean($message);

        return $this;
    }

    public function isError(): bool
    {
        return $this->error;
    }

    public function setError(bool $error = true): self
    {
        $this->error = $error;

        return $this;
    }

    public function setAdditional(array $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function toApiResponse(): BaseHttpResponse|JsonResponse|JsonResource|RedirectResponse
    {
        if ($this->data instanceof JsonResource) {
            return $this->data->additional(array_merge([
                'error' => $this->error,
                'message' => $this->message,
            ], $this->additional));
        }

        return $this->toResponse(request());
    }

    public function toResponse($request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            $data = [
                'error' => $this->error,
                'data' => $this->data,
                'message' => $this->message,
            ];

            if ($this->additional) {
                $data = array_merge($data, ['additional' => $this->additional]);
            }

            return response()
                ->json($data, $this->code);
        }

        if ($request->input('submit') === $this->saveAction && ! empty($this->previousUrl)) {
            return $this->responseRedirect($this->previousUrl);
        } elseif (! empty($this->nextUrl)) {
            return $this->responseRedirect($this->nextUrl);
        }

        return $this->responseRedirect(URL::previous());
    }

    protected function responseRedirect(string $url): RedirectResponse
    {
        if ($this->withInput) {
            return redirect()
                ->to($url)
                ->with($this->error ? 'error_msg' : 'success_msg', $this->message)
                ->withInput();
        }

        return redirect()
            ->to($url)
            ->with($this->error ? 'error_msg' : 'success_msg', $this->message);
    }
}
