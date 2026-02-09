<?php
namespace App\Http\Controllers\ViewController;

use App\Http\Controllers\Controller;
use App\Models\MetaSettings;
use App\Models\GarageDetails;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $garage = GarageDetails::find(1); // Replace 1 with the appropriate ID or query to get the garage record
        $garageName = $garage->garage_name ?? 'Garage Solutions';
        // dd($garageName);
        // Retrieve meta settings by their names
        $metaTitle = MetaSettings::where('name', 'home_meta_title')->value('content') ?? $garageName;
        $metaDescription = MetaSettings::where('name', 'home_meta_description')->value('content') ?? $garageName;

        // Replace $store with $garageName in meta tags
        $metaTitle = str_replace('$store', $garageName, $metaTitle);
        $metaDescription = str_replace('$store', $garageName, $metaDescription);
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';

        return view('home', compact('metaTitle', 'metaDescription', 'google_tag_manager', 'tag_manager', 'analytics'));
    }

    public function contact()
    {
        $garage = GarageDetails::find(1); // Replace 1 with the appropriate ID or query to get the garage record
        $garageName = $garage->garage_name ?? 'Garage Solutions';
        // dd($garageName);
        // Retrieve meta settings by their names
        $metaTitle = MetaSettings::where('name', 'home_meta_title')->value('content') ?? $garageName;
        $metaDescription = MetaSettings::where('name', 'home_meta_description')->value('content') ?? $garageName;

        // Replace $store with $garageName in meta tags
        $metaTitle = 'contact Us | '. $garageName;
        //str_replace('$store', $garageName, $metaTitle);
        $metaDescription = 'contact Us | '. $garageName;
        //str_replace('$store', $garageName, $metaDescription);
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';

        return view('contact', compact('metaTitle', 'metaDescription', 'google_tag_manager', 'tag_manager', 'analytics'));
    }

}
