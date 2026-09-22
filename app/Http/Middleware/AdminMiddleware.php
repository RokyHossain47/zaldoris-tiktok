<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Please log in to access the Admin Panel.']);
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Access denied. Administrator privileges required.']);
        }

        return $next($request);
    }
}
