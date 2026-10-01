<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CleanStaleCookies
{
    /**
     * Handle an incoming request and prune any obsolete/legacy cookies.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $allowedCookies = [
            config('session.cookie', 'kb_finvest_session'),
            'XSRF-TOKEN',
        ];

        foreach ($request->cookies->keys() as $cookieName) {
            if (! in_array($cookieName, $allowedCookies, true)) {
                $response->headers->setCookie(
                    new Cookie(
                        name: $cookieName,
                        value: '',
                        expire: 1,
                        path: '/',
                        secure: true,
                        httpOnly: true,
                        sameSite: 'lax'
                    )
                );
            }
        }

        return $response;
    }
}
