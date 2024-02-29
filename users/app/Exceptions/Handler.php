<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
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
        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api') || $request->is('api/*') || $request->is('/') || $request->is('/*')) {
                return response()->json([
                    'successful' => false,
                    'message' => 'Specified route is not valid',
                ], 405);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api') || $request->is('api/*') || $request->is('/') || $request->is('/*')) {
                return response()->json([
                    'successful' => false,
                    'message' => 'Requested resource not found',
                ], 404);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api') || $request->is('api/*') || $request->is('/') || $request->is('/*')) {
                return response()->json([
                    'successful' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $this->renderable(function (Exception $e, Request $request) {
            if ($request->is('api') || $request->is('api/*') || $request->is('/') || $request->is('/*')) {
                Log::error($e->getMessage());

                return response()->json([
                    'successful' => false,
                    'message' => 'Something went wrong. Please check the logs for details',
                ], 400);
            }
        });
    }
}
