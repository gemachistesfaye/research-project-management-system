<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * RPMS Custom Exception Handler.
 *
 * Provides:
 *   - Dual-channel error rendering (JSON for API, Blade for Web)
 *   - ModelNotFoundException → 404
 *   - AuthorizationException → 403
 *   - ValidationException → 422 with structured field errors
 *   - All exceptions logged to the dedicated 'rpms' log channel
 */
class Handler extends ExceptionHandler
{
    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $levels = [];

    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception handlers.
     */
    public function register(): void
    {
        // ModelNotFoundException → 404
        $this->renderable(function (ModelNotFoundException $e, $request) {
            $model = class_basename($e->getModel());
            $message = "The requested {$model} was not found.";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors'  => [],
                ], 404);
            }

            return response()->view('errors.404', ['message' => $message], 404);
        });
        // AccessDeniedHttpException → 403
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            $message = $e->getMessage() ?: 'You do not have permission to perform this action.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors'  => [],
                ], 403);
            }

            return response()->view('errors.403', ['message' => $message], 403);
        });
        // ValidationException → 422 with structured field errors (API only)
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            // Let Laravel handle web redirects normally
            return null;
        });
        // AuthenticationException → 401
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please provide a valid API token.',
                    'errors'  => [],
                ], 401);
            }
            return null; // Let Laravel handle the web redirect to /login
        });
        // TokenMismatchException (419 Page Expired) → Redirect to login
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please refresh and sign in again.',
                ], 419);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Your session has expired. Please refresh and sign in again.',
            ]);
        });
        // Generic HTTP exceptions
        $this->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An HTTP error occurred.',
                    'errors'  => [],
                ], $e->getStatusCode());
            }
            return null;
        });
        // Reportable: log ALL exceptions to the RPMS log channel
        $this->reportable(function (Throwable $e) {
            Log::channel('rpms')->error($e->getMessage(), [
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'url'       => request()->fullUrl(),
                'user_id'   => auth()->id(),
                'ip'        => request()->ip(),
            ]);
        });
    }
}
