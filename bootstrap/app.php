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
            $driverSummary = null;
            if ($sqlState === '42883' && is_string($error->errorInfo[2] ?? null)) {
                $driverSummary = trim(strtok($error->errorInfo[2], "\n") ?: '');
                $driverSummary = mb_substr($driverSummary, 0, 240);
            }

            $message = $sqlState
                ? "Database request failed ({$sqlState}) at {$request->path()}."
                : 'Database request failed.';
            if ($driverSummary) $message .= " {$driverSummary}";

            return response()->json([
                'message' => $message,
                'error_code' => $sqlState,
            ], 500);
        });
        $exceptions->render(function (\Error $error, Request $request) {
            if (! $request->is('api/*')) return null;

            report($error);

            return response()->json([
                'message' => sprintf(
                    'Application request failed at %s (%s).',
                    $request->path(),
                    class_basename($error),
                ),
            ], 500);
        });
    })->create();
