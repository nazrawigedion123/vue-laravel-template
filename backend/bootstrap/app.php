<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::get('/media/{path}', function (string $path) {
                $mediaRoot = realpath(base_path('../public/media'));
                $filePath = $mediaRoot ? realpath($mediaRoot . DIRECTORY_SEPARATOR . $path) : false;

                abort_unless(
                    $mediaRoot && $filePath && str_starts_with($filePath, $mediaRoot . DIRECTORY_SEPARATOR) && File::isFile($filePath),
                    404
                );

                return response()->file($filePath);
            })->where('path', '.*');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        $middleware->append(\App\Http\Middleware\RequestResponseLogger::class);
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });
    })->create();
