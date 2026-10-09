<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrTechnician
{
    /**
     * Handle an incoming request.
     *
     * Allows admins AND technicians into /admin shell.
     * Fine-grained protection (admin-only pages, no-delete for techs)
     * is enforced per-route / per-controller on top of this.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin', 'technician'], true)) {
            abort(403, 'Geen toegang.');
        }

        $response = $next($request);

        // Adminpagina's nooit uit de browser-cache tonen (voorkomt verouderde
        // knoppen/pagina's via terug-knop of bfcache na een update).
        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
