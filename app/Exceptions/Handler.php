<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle NotFoundHttpException (e.g., invalid API endpoints)
        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['error' => 'Endpoint not found'], Response::HTTP_NOT_FOUND);
        }

        // Handle ValidationException (custom response for validation errors)
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->all();
            $message = !empty($errors) ? $errors[0] : 'Validation error occurred';
            return response()->json([
                'error' => $message
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Default exception handling for all other exceptions
        return parent::render($request, $exception);
    }
}
