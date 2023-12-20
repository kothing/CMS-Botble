<?php

namespace Botble\Base\Exceptions;

use App\Exceptions\Handler as ExceptionHandler;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Facades\RvMedia;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected BaseHttpResponse $baseHttpResponse;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->ignore(PhpSpreadsheetException::class);
        $this->ignore(DisabledInDemoModeException::class);

        $this->baseHttpResponse = new BaseHttpResponse();
    }

    public function render($request, Throwable $e)
    {
        if (! app()->isDownForMaintenance() && $e instanceof HttpExceptionInterface) {
            do_action(BASE_ACTION_SITE_ERROR, $e->getStatusCode());
        }

        if ($e instanceof ModelNotFoundException || $e instanceof MethodNotAllowedHttpException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        switch (true) {
            case $e instanceof DisabledInDemoModeException:
            case $e instanceof MethodNotAllowedHttpException:
            case $e instanceof TokenMismatchException:
                return $this->baseHttpResponse
                    ->setError()
                    ->setCode($e->getCode())
                    ->setMessage($e->getMessage());
            case $e instanceof PostTooLargeException:
                if (count($request->allFiles())) {
                    return RvMedia::responseError(
                        trans('core/media::media.upload_failed', [
                            'size' => BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize()),
                        ])
                    );
                }

                break;
            case $e instanceof NotFoundHttpException:
                if (setting('redirect_404_to_homepage', 0) == 1) {
                    return redirect(route('public.index'));
                }

                break;
            case $e instanceof HttpExceptionInterface:
                $code = $e->getStatusCode();

                if ($request->expectsJson()) {
                    return match ($code) {
                        401 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/acl::permissions.access_denied_message'))
                            ->setCode($code)
                            ->toResponse($request),
                        403 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/acl::permissions.action_unauthorized'))
                            ->setCode($code)
                            ->toResponse($request),
                        404 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/base::errors.not_found'))
                            ->setCode(404)
                            ->toResponse($request),
                        default => $this->baseHttpResponse
                            ->setError()
                            ->setMessage($e->getMessage())
                            ->setCode($code)
                            ->toResponse($request),
                    };
                }
        }

        return parent::render($request, $e);
    }

    public function report(Throwable $e)
    {
        if ($this->shouldReport($e) && ! $this->isExceptionFromBot()) {
            $key = 'send_error_exception';

            if (Cache::has($key)) {
                return;
            }

            Cache::put($key, 1, Carbon::now()->addMinutes(5));

            if (! app()->isLocal() && ! app()->runningInConsole() && ! app()->isDownForMaintenance()) {
                if (setting('enable_send_error_reporting_via_email', false) &&
                    setting('email_driver', Mail::getDefaultDriver()) &&
                    $e instanceof Exception
                ) {
                    EmailHandler::sendErrorException($e);
                }

                if (config('core.base.general.error_reporting.via_slack', false)) {
                    $request = request();

                    $previous = $e->getPrevious();

                    logger()->channel('slack')->critical(
                        $e->getMessage() . ($previous ? '(' . $previous . ')' : null),
                        [
                            'Request URL' => $request->fullUrl(),
                            'Request IP' => $request->ip(),
                            'Request Referer' => $request->header('referer'),
                            'Request Method' => $request->method(),
                            'Request Form Data' => $request->method() != 'GET' ? BaseHelper::jsonEncodePrettify($request->input()) : null,
                            'Exception Type' => get_class($e),
                            'File Path' => ltrim(str_replace(base_path(), '', $e->getFile()), '/') . ':' .
                                $e->getLine(),
                            'Previous File Path' => $previous ? ltrim(str_replace(base_path(), '', $previous->getFile()), '/') . ':' . $previous->getLine() : null,
                        ]
                    );
                }
            }
        }

        parent::report($e);
    }

    protected function isExceptionFromBot(): bool
    {
        $ignoredBots = config('core.base.general.error_reporting.ignored_bots', []);
        $agent = strtolower(request()->userAgent());

        if (empty($agent)) {
            return false;
        }

        foreach ($ignoredBots as $bot) {
            if (str_contains($agent, $bot)) {
                return true;
            }
        }

        return false;
    }

    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        if (app()->runningInConsole() || request()->wantsJson() || request()->expectsJson()) {
            return parent::getHttpExceptionView($e);
        }

        $code = $e->getStatusCode();

        if (request()->is(BaseHelper::getAdminPrefix() . '/*') || request()->is(BaseHelper::getAdminPrefix())) {
            return 'core/base::errors.' . $code;
        }

        if (class_exists('Theme')) {
            $view = 'theme.' . Theme::getThemeName() . '::views.' . $code;

            if (view()->exists($view)) {
                return $view;
            }
        }

        return parent::getHttpExceptionView($e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->baseHttpResponse
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(401)
                ->toResponse($request);
        }

        return redirect()->guest(route('access.login'));
    }
}
