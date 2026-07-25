<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     * Redirect unverified users to waiting page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Admin is always considered verified
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Check if user is verified
        if (!$request->user()->isVerified()) {
            return redirect()->route('verification.pending');
        }

        return $next($request);
    }
}

