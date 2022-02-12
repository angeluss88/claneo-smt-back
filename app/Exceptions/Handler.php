<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json(['message' => 'Not Found!'], 404);
        }

        return response()->json(
            $this->getJsonMessage($e),
            $this->getExceptionHTTPStatusCode($e)
        );
    }

    protected function getJsonMessage($e): array
    {
        return [
            'status' => 'false',
            'message' => $e->getMessage(),
        ];
    }

    protected function getExceptionHTTPStatusCode($e): int
    {
        return method_exists($e, 'getStatusCode') ?
            $e->getStatusCode() : 500;
    }
}
