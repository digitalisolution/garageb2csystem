<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        // Check if the exception is an instance of HttpExceptionInterface
        if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() == 410) {
            return response()->view('errors.410', [], 410);
        }

        // Handle other types of exceptions or errors
        if ($exception instanceof \Error) {
            // Log the error for debugging purposes
            \Log::error('Unhandled Error: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            // Return a generic error response
            return response()->view('errors.generic', ['message' => 'An unexpected error occurred.'], 500);
        }

        // Default Laravel exception handling
        return parent::render($request, $exception);
    }
}
