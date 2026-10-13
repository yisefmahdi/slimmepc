<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin pages + JSON endpoints must never be served from the browser cache:
 * the admin UI ships inline JS that changes often (chunked sending, toasts),
 * and stale copies cause exactly the "fix deployed but nothing changed" confusion.
 */
class PreventAdminCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
