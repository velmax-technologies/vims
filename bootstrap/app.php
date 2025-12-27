<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                $response = [
                    'response' => [
                        'status'      => 'error',
                        'status_code' => 401,
                        'error'     => [
                            'message'   => $e->getMessage(),
                            'timestamp' => Carbon::now(),
                        ],
                    ]
                ];

                return response()->json($response, 401);
            }
        });
    })->create();
