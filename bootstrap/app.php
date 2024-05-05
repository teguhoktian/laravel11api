<?php

use App\APIResponseBuilder;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,

            // Laravel Permission
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Error Handling for Model Binding Route Exceptions
        $exceptions->render(function (NotFoundHttpException $e): JsonResponse {
            return APIResponseBuilder::notFound(__('URL is not found.'), $e->getStatusCode());
        });

        // Error Handling Unauthenticated Exceptions
        $exceptions->render(function (AuthenticationException $e): JsonResponse {
            return APIResponseBuilder::unauthorized($e->getMessage());
        });

        // Error Handling Validation Exceptions
        $exceptions->render(function (ValidationException $e): JsonResponse {
            return APIResponseBuilder::invalidData($e->getMessage(), $e->errors());
        });

        // Error Handling Http Exceptions
        $exceptions->render(function (HttpException $e): JsonResponse {
            return APIResponseBuilder::unauthorized($e->getMessage(), $e->getStatusCode());
        });
    })->create();
