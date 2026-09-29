<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfBlocked
{
    public const MESSAGE = 'Je account is volledig geblokkeerd. Neem contact met ons op als dit een fout is.';

    /**
     * Handle an incoming request.
     *
     * If the authenticated user is blocked: log out immediately and
     * return them to login with the blocked message (or 403 JSON for AJAX).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (bool) ($user->is_blocked ?? false)) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => self::MESSAGE], 403);
            }

            return redirect()->route('login')->withErrors(['email' => self::MESSAGE]);
        }

        return $next($request);
    }
}
