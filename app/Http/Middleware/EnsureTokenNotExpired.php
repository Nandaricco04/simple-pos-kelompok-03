<?php

namespace App\Http\Middleware;

use Closure;
use DateTimeInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenNotExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $token = $user->currentAccessToken();

            // 1. Cek jika access token berupa model PersonalAccessToken dengan kolom expires_at
            if ($token instanceof PersonalAccessToken) {
                if ($token->expires_at && $token->expires_at->isPast()) {
                    throw new AuthenticationException('Token sudah kedaluwarsa.');
                }

                $expiration = config('sanctum.expiration');
                if ($expiration && $token->created_at && $token->created_at->lt(now()->subMinutes((int) $expiration))) {
                    throw new AuthenticationException('Token sudah kedaluwarsa.');
                }
            }

            // 2. Cek jika token memiliki properti expires_at
            if ($token && isset($token->expires_at) && $token->expires_at instanceof DateTimeInterface) {
                if ($token->expires_at->isPast()) {
                    throw new AuthenticationException('Token sudah kedaluwarsa.');
                }
            }

            // 3. Cek jika token/mock di-set dengan accessTokenExpiresAt (misalnya melalui Sanctum::actingAs)
            if (isset($user->accessTokenExpiresAt) && $user->accessTokenExpiresAt instanceof DateTimeInterface) {
                if ($user->accessTokenExpiresAt->isPast()) {
                    throw new AuthenticationException('Token sudah kedaluwarsa.');
                }
            }
        }

        return $next($request);
    }
}
