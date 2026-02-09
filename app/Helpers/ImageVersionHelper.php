<?php

namespace App\Helpers;

class ImageVersionHelper
{
    public static function addVersionToImages($html)
    {
        return preg_replace_callback('/<img[^>]+src="([^"]+)"/i', function ($matches) {
            $src = $matches[1];

            // Only version local images (not external)
            if (!preg_match('#^(https?:)?//#', $src)) {
                $relativePath = ltrim(parse_url($src, PHP_URL_PATH), '/');
                $fullPath = public_path($relativePath);

                if (file_exists($fullPath)) {
                    $version = filemtime($fullPath);
                    $src = strtok($src, '?') . '?v=' . $version;
                }
            }

            return str_replace($matches[1], $src, $matches[0]);
        }, $html);
    }
}
