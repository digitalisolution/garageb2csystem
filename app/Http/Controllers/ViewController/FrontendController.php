<?php

namespace App\Http\Controllers\ViewController;
use App\Models\MetaSettings;
use App\Http\Controllers\Controller; // Import the base Controller class
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $data = [
            'title' => 'Home',
            'content' => 'Welcome to our Garage Management System!',
        ];
        // Reference the view in the 'view' folder
        return view('home', compact('data'));
    }
    // public function aboutUs()
    // {
    //     return view('about');
    // }

    public function contact()
    {
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';

        return view('contact', compact('google_tag_manager', 'tag_manager', 'analytics'));
    }

    public function services()
    {
        return view('services');
    }

    public function tyres()
    {
        return view('tyres');
    }
}

