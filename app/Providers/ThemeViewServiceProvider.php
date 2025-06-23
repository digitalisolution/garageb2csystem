<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Resolve the current domain and theme
        $domain = request()->getHost();
        $theme = config('app.current_theme', 'default'); // Default theme fallback

        // Sanitize domain (replace dots with hyphens)
        $sanitizedDomain = str_replace('.', '-', $domain);
        // \Log::info("Sanitized Domain:", ['domain' => $sanitizedDomain]);

        // Dynamically resolve view paths
        $viewPaths = $this->resolveViewPaths($sanitizedDomain, $theme);

        // Set the view paths dynamically using View::addLocation
        $this->setViewPaths($viewPaths);
    }

    private function resolveViewPaths($sanitizedDomain, $theme)
    {
        $paths = [];

        // Add domain-specific override path
        $overridePath = resource_path("views/frontend/themes/{$theme}/override/{$sanitizedDomain}");
        // \Log::info("Checking override path:", ['overridePath' => $overridePath]);

        if (File::exists($overridePath)) {
            // \Log::info("Override file found:", ['overridePath' => $overridePath]);
            $paths[] = $overridePath;
        }

        // Add the main theme path
        $themePath = resource_path("views/frontend/themes/{$theme}");
        // \Log::info("Checking theme path:", ['themePath' => $themePath]);

        if (File::exists($themePath)) {
            // \Log::info("Theme file found:", ['themePath' => $themePath]);
            $paths[] = $themePath;
        }

        // Add the default Laravel views path as a fallback
        $paths[] = resource_path('views');
        return $paths;
    }

    private function setViewPaths(array $paths)
    {
        // Add each path to the view loader
        foreach ($paths as $path) {
            View::addLocation($path);
            // \Log::info("View path added:", ['path' => $path]);
        }

        // Log the final view paths
        // \Log::info('Final view paths added:', ['paths' => $paths]);
    }
}
