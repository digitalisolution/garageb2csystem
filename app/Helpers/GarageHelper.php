<?php

if (!function_exists('getGarageDetails')) {
    function getGarageDetails()
    {
        $garage = \App\Models\GarageDetails::first();
        return $garage ?? (object) ['garage_name' => 'Your Workshop'];
    }
}