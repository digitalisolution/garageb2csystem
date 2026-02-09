<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (!function_exists('get_option')) {
    function get_option($name, $default = null)
    {
        return DB::table('general_settings')->where('name', $name)->value('value') ?? $default;

    }
}
