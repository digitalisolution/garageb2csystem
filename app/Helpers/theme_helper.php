<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

if (!function_exists('include_dynamic_view')) {
    /**
     * Includes a dynamic view if it exists for the current domain.
     *
     * @param string $viewName
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|string
     */
    function include_dynamic_view($viewName)
    {
        // Get the current domain and theme
        $domain = request()->getHost();
        $theme = config('app.current_theme', 'default'); // Default theme fallback
        $sanitizedDomain = str_replace('.', '-', $domain);

        // Build the override path based on the view name and domain
        $overridePath = resource_path("views/frontend/themes/{$theme}/override/{$sanitizedDomain}/{$viewName}.blade.php");
        $themePath = resource_path("views/frontend/themes/{$theme}/{$viewName}.blade.php");

        // Check if the override file exists, if not, fallback to theme or default
        if (File::exists($overridePath)) {
            return view("frontend.themes.{$theme}.override.{$sanitizedDomain}.{$viewName}");
        }

        if (File::exists($themePath)) {
            return view("frontend.themes.{$theme}.{$viewName}");
        }

        // If no file found, fallback to the default Laravel views path
        return view($viewName);
    }
}

if (!function_exists('include_dynamic_component')) {
    /**
     * Includes a dynamic component if it exists for the current domain.
     *
     * @param string $componentName
     * @return string
     */
    function include_dynamic_component($componentName)
    {
        $domain = request()->getHost();
        $theme = config('app.current_theme', 'default'); // Default theme fallback
        $sanitizedDomain = str_replace('.', '-', $domain);

        $overridePath = resource_path("views/components/override/{$sanitizedDomain}/{$componentName}.blade.php");
        $themePath = resource_path("views/components/themes/{$theme}/{$componentName}.blade.php");
        $defaultPath = resource_path("views/components/{$componentName}.blade.php");

        \Log::info("Checking component paths", [
            'overridePath' => $overridePath,
            'themePath' => $themePath,
            'defaultPath' => $defaultPath,
        ]);

        if (File::exists($overridePath)) {
            \Log::info("Using override component: {$overridePath}");
            return "components.override.{$sanitizedDomain}.{$componentName}";
        }

        if (File::exists($themePath)) {
            \Log::info("Using theme component: {$themePath}");
            return "components.themes.{$theme}.{$componentName}";
        }

        if (File::exists($defaultPath)) {
            \Log::info("Using default component: {$defaultPath}");
            return "components.{$componentName}";
        }

        \Log::error("Component not found: {$componentName}");
        throw new \Exception("Component [{$componentName}] not found.");
    }



}
