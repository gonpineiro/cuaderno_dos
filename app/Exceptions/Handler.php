<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
        $this->renderable(function (NotFoundHttpException $e) {
            return sendResponse(null, $e->getMessage(), 300);
        });

        /** Ocuerre un error en la base de datos */
        $this->renderable(function (QueryException $e) {
            return sendResponse(null, $e, 301);
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return sendResponse(null, $e->getMessage(), 302);
        });
    }
}
