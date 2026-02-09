<?php

if (!function_exists('theme_asset')) {
    /**
     * Dynamically resolve the asset path for the current domain and theme.
     *
     * @param string $type The asset type (e.g., 'css', 'js', 'images', etc.)
     * @param string $fileName The file name (e.g., 'style.css', 'logo.png', etc.)
     * @return string The resolved asset URL.
     */
    function theme_asset($type, $fileName)
    {
        $domain = request()->getHost();
        $theme = config('app.current_theme', 'default');
        $sanitizedDomain = str_replace('.', '-', $domain);

        // Domain-specific asset path
        $domainPath = public_path("frontend/{$sanitizedDomain}/{$type}/{$fileName}");
        if (\Illuminate\Support\Facades\File::exists($domainPath)) {
            return asset("frontend/{$sanitizedDomain}/{$type}/{$fileName}");
        }

        // Theme asset path
        $themePath = public_path("themes/{$theme}/{$type}/{$fileName}");
        if (\Illuminate\Support\Facades\File::exists($themePath)) {
            return asset("frontend/themes/{$theme}/{$type}/{$fileName}");
        }

        // Fallback asset path
        $fallbackPath = public_path("default/{$type}/{$fileName}");
        if (\Illuminate\Support\Facades\File::exists($fallbackPath)) {
            return asset("frontend/themes/default/{$type}/{$fileName}");
        }
        return '';
    }

    
     if (!function_exists('versioned_asset')) {
    function versioned_asset($path) {
        $fullPath = public_path($path);
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return asset($path) . '?v=' . $version;
    }
    }
    
}