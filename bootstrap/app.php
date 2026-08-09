<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'supabase.auth' => \App\Http\Middleware\AuthenticateWithSupabase::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (QueryException $error, Request $request) {
            if (! $request->is('api/*')) return null;

            $sqlState = $error->errorInfo[0] ?? null;

            return response()->json([
                'message' => $sqlState
                    ? "Database request failed ({$sqlState})."
                    : 'Database request failed.',
                'error_code' => $sqlState,
            ], 500);
        });
    })->create();
