<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use File;

class SetDomainAssets
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current domain from the request
        $domain = $request->getHost(); // Example: 'domain1.com'

        // Set the theme (could be based on a session or configuration)
        $themeName = session('current_theme', 'theme'); // Default to theme1 if not set

        // Check if domain-specific assets exist and override them
        $this->setDomainAssets($domain, $themeName);

        return $next($request);
    }

    protected function setDomainAssets($domain, $themeName)
    {
        // Check for domain-specific CSS, JS, images, etc.
        $this->setDomainCSS($domain, $themeName);
        $this->setDomainJS($domain, $themeName);
        $this->setDomainImages($domain, $themeName);
        $this->setDomainFonts($domain, $themeName);
    }

    protected function setDomainCSS($domain, $themeName)
    {
        $domainCSSPath = public_path("frontend/{$domain}/css/");
        $defaultCSSPath = public_path("themes/{$themeName}/css/");

        // Check if the domain folder has any CSS files
        $this->setDomainAssetsFromDirectory($domainCSSPath, 'css', $domain, $themeName, $defaultCSSPath);
    }

    protected function setDomainJS($domain, $themeName)
    {
        $domainJSPath = public_path("frontend/{$domain}/js/");
        $defaultJSPath = public_path("themes/{$themeName}/js/");

        // Check if the domain folder has any JS files
        $this->setDomainAssetsFromDirectory($domainJSPath, 'js', $domain, $themeName, $defaultJSPath);
    }

    protected function setDomainImages($domain, $themeName)
    {
        $domainImagePath = public_path("frontend/{$domain}/images/");
        $defaultImagePath = public_path("themes/{$themeName}/images/");

        // Check if the domain folder has any images (e.g., logo.png)
        $this->setDomainAssetsFromDirectory($domainImagePath, 'images', $domain, $themeName, $defaultImagePath);
    }

    protected function setDomainFonts($domain, $themeName)
    {
        $domainFontPath = public_path("frontend/{$domain}/fonts/");
        $defaultFontPath = public_path("themes/{$themeName}/fonts/");

        // Check if the domain folder has any fonts (e.g., custom-font.ttf)
        $this->setDomainAssetsFromDirectory($domainFontPath, 'fonts', $domain, $themeName, $defaultFontPath);
    }

    // Generic function to handle checking and setting assets
    protected function setDomainAssetsFromDirectory($directoryPath, $assetType, $domain, $themeName, $fallbackPath)
    {
        // Check if the directory exists and contains files
        if (File::exists($directoryPath)) {
            // If files exist, iterate over them and add them to the session
            $files = File::files($directoryPath);
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                session(["domain_{$assetType}_{$fileName}" => asset("frontend/{$domain}/{$assetType}/{$fileName}")]);
            }
        } else {
            // If no domain-specific files exist, fallback to theme assets
            $this->setFallbackAssets($fallbackPath, $assetType, $themeName);
        }
    }

    // Fallback to theme assets if no domain-specific files are found
    protected function setFallbackAssets($fallbackPath, $assetType, $themeName)
    {
        if (File::exists($fallbackPath)) {
            // Get files from the default theme and store them in the session
            $files = File::files($fallbackPath);
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                session(["theme_{$assetType}_{$fileName}" => asset("themes/{$themeName}/{$assetType}/{$fileName}")]);
            }
        }
    }
}
