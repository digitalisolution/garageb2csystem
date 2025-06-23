<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetCanonicalUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Define the canonical URL
        $canonicalUrl = URL::full();  // This will get the full URL of the current page
        // Share the canonical URL with all views
        // Customize canonical URL for tyre search
        $canonicalUrl = URL::full();

    // Only apply trailing slash on homepage
        if ($request->is('/')) {
            $canonicalUrl = url('/') . '/';  // Ensure trailing slash
        }

        if ($request->is('search') && $request->filled(['width', 'profile', 'diameter'])) {
            $width = $request->input('width');
            $profile = $request->input('profile');
            $diameter = $request->input('diameter');

            $canonicalUrl = url("/tyres-size/{$width}-{$profile}-{$diameter}");
        }
        view()->share('canonical', $canonicalUrl);

        return $next($request);
    }
}
