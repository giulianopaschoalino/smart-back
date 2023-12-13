<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
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

    private function messageCustom(\Throwable $ex): array
    {

        $messageCustom = [];

        $class_exceptions = [
            AuthorizationException::class => fn ($e) => [
                'Unauthorized',
                Response::HTTP_UNAUTHORIZED
            ],

            AuthenticationException::class => fn ($e) => [
                'Unauthorized',
                Response::HTTP_UNAUTHORIZED
            ],

            ValidationException::class => fn (ValidationException $e) => [
                $e->validator->getMessageBag()->getMessages(),
                Response::HTTP_BAD_REQUEST
            ],

            ModelNotFoundException::class => fn ($e) => [
                'Not Found',
                Response::HTTP_NOT_FOUND
            ],

            NotFoundHttpException::class => fn ($e) => [
                'Not Found',
                Response::HTTP_NOT_FOUND
            ],

            MethodNotAllowedHttpException::class => fn ($e) => [
                'Method Not Allowed',
                Response::HTTP_METHOD_NOT_ALLOWED
            ],

            HttpException::class => fn (HttpException $e) => [
                $e->getMessage(),
                $e->getStatusCode(),
            ],

            QueryException::class => fn (QueryException $e) => [
                App::isLocal()
                    ? ['Message' => $e->getMessage(), 'SQL' => $e->getSql(), 'Bindings' => $e->getBindings()]
                    : 'Internal server error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ],

            MissingAbilityException::class => fn ($_) => [
                'Unauthorized',
                Response::HTTP_UNAUTHORIZED
            ]

        ];

        $exception_message = $class_exceptions[get_class($ex)] ?? null;

        if ($exception_message) {
            $messageCustom = $exception_message($ex);
        } else {
            $messageCustom = [
                App::isLocal() ?  $ex->getMessage() : 'Internal server error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }


        return $messageCustom;
    }



    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Throwable $ex) {
            $message = "Message: {$ex->getMessage()} {n} Line: {$ex->getLine()} {n} File: {$ex->getFile()}{n}Track: {$ex->getTraceAsString()} {n} {n}";

            $message = preg_replace("/\{n\}/", PHP_EOL, $message);

            Log::error($message);

            return false;
        });
    }

    /**
     * Custom render errors
     *
     * @param Illuminate\Http\Request $req
     * @param \Throwable $ex
     * @return \Illuminate\Http\Response
     */
    public function render($req, \Throwable $ex)
    {
        [$message, $status_code] = $this->messageCustom($ex);

        return response()->json(['error' => $message], $status_code);
    }
}
