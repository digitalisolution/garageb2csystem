<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureCustomerIsAuthenticated
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            Log::info('Authenticated User:', ['user' => Auth::user()]);

            // Check if the user has the 'customer' role
            if (Auth::user()->role_id === 2) {
                return $next($request);
            } else {
                Log::warning('User is authenticated but not a customer. Role ID: ' . Auth::user()->role_id);
            }
        } else {
            Log::warning('User is not authenticated.');
        }

        // Redirect if not authorized
        return redirect('/login')->withErrors('Unauthorized access.');
    }
}
