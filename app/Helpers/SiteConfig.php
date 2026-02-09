<?php

use Illuminate\Support\Arr;

if (!function_exists('site_config')) {
    function site_config($key = null, $default = null) {
        $host = request()->getHost();
        $configPath = base_path("website-configs/{$host}/config.php");

        if (file_exists($configPath)) {
            $config = include $configPath;
            return $key ? Arr::get($config, $key, $default) : $config;
        }

        return $default;
    }
}
