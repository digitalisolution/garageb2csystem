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
        if ($exception instanceof HttpException && $exception->getStatusCode() == 419) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Session expired. Please log in again.'], 419);
            } else {
                return redirect()->route('webmaster.login')->with('error', 'Your session has expired. Please log in again.');
            }
        }
        if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() == 410) {
            return response()->view('errors.410', [], 410);
        }

        // if (method_exists($exception, 'getStatusCode') && $exception->getStatusCode() == 429) {
        // return response()->view('errors.429', [], 429);
        // }

        if ($exception instanceof \Error) {
            \Log::error('Unhandled Error: ' . $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->view('errors.generic', ['message' => 'An unexpected error occurred.'], 500);
        }
        return parent::render($request, $exception);
    }
}
