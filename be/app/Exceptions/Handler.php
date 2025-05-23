<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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

        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'message' => 'Unauthenticated.',
                    ], 401);
                }

                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'message' => 'Resource not found.',
                    ], 404);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'message' => 'The requested resource was not found.',
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'message' => 'Method not allowed.',
                    ], 405);
                }

                if ($e instanceof AuthorizationException) {
                    return response()->json([
                        'message' => 'This action is unauthorized.',
                    ], 403);
                }

                if (config('app.debug')) {
                    return response()->json([
                        'message' => $e->getMessage(),
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ], 500);
                }

                return response()->json([
                    'message' => 'Server Error',
                ], 500);
            }
        });
    }
}
