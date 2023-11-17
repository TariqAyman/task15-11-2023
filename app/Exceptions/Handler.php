<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use App\Helpers\MapResponseError;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelIgnition\Exceptions\ViewException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() ||
            $request->routeIs('api.*') ||
            $request->headers->get('Content-Type') == 'application/json'
        ) {
            $request->headers->add(['Accept' => 'application/json']);
            $request->headers->add(['Content-Type' => 'application/json']);

            return $this->renderExceptionAsJson($request, $exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return abort(404, 'NOT FOUND');
        }

        return parent::render($request, $exception);
    }

    /**
     * Render an exception into a JSON response
     *
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function renderExceptionAsJson($request, Throwable $exception)
    {
        // Currently converts AuthorizationException to 403 HttpException
        // and ModelNotFoundException to 404 NotFoundHttpException
        $exception = $this->prepareException($exception);
        // Default response

        $response = [
            'errors' => [
                [
                    'key' => 'error',
                    'message' => trans('api.Sorry, something went wrong.'),
                ],
            ],
        ];

        // Add debug info if app is in debug mode
        if (config('app.debug')) {
            // Add the exception class name, message and stack trace to response
            $response['debug']['exception'] = get_class($exception); // Reflection might be better here
            $response['debug']['message'] = $exception->getMessage();
            $response['debug']['trace'] = $exception->getTrace();
        }

        $status = 400;

        // Build correct status codes and status texts
        switch ($exception) {
            case $exception instanceof ValidationException:
                $response = MapResponseError::mapResponseError($exception->validator->errors());
                break;
            case $exception instanceof AuthenticationException:
                $status = 401;
                $response['errors'] = [
                    [
                        'key' => 'error',
                        'message' => trans('app.Unauthenticated'),
                    ],
                ];
                break;
            case $this->isHttpException($exception):
                $status = $exception->getCode();
                $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400;
                $response['errors'] = [
                    [
                        'key' => 'error',
                        'message' => Response::$statusTexts[$status] ?? '',
                    ],
                ];
                break;
            default:
                break;
        }

        return response()->json($response, $status);
    }
}
