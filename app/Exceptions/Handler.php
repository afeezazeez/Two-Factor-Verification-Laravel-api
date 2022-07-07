<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jerry\JWT\Exceptions\TokenExpiredException;
use Jerry\JWT\Exceptions\TokenFormatException;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e)
    {

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $this->error('Resource not found', Response::HTTP_NOT_FOUND, null);
        }

        if($e instanceof  AuthenticationException ){
            return $this->error('Unauthenticated', Response::HTTP_UNAUTHORIZED, null);
        }

        if($e instanceof  TokenFormatException ){
            return $this->error('Token is Invalid', Response::HTTP_UNAUTHORIZED, null);
        }

        if($e instanceof  TokenExpiredException ){
            return $this->error('Token has expired', Response::HTTP_UNAUTHORIZED, null);
        }



        return $this->error('Error handling request. Please try again', Response::HTTP_INTERNAL_SERVER_ERROR, null);
    }
}
