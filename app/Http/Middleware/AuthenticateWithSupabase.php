<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithSupabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders(['apikey' => config('services.supabase.anon_key')])
                ->withToken($token)
                ->timeout(10)
                ->get(rtrim(config('services.supabase.url'), '/').'/auth/v1/user');
        } catch (ConnectionException) {
            return response()->json(['message' => 'Authentication service unavailable.'], 503);
        }

        if (! $response->successful() || ! $response->json('id')) {
            return response()->json(['message' => 'Invalid or expired access token.'], 401);
        }

        $request->attributes->set('supabase_user', $response->json());
        $request->attributes->set('user_id', $response->json('id'));

        return $next($request);
    }
}
