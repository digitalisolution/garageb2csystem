<?php

namespace App\Http\Controllers\ViewController;
use App\Models\GarageDetails;
use App\Models\MetaSettings;
use App\Models\Page;
use App\Http\Controllers\Controller; // Import the base Controller class
use Illuminate\Http\Request;

use App\Helpers\ImageVersionHelper;

use Illuminate\Support\Facades\DB;

class SlugController extends Controller
{
    public function handleSlug($slug = null)
    {
        if (!$slug) {
            abort(404, "Slug not provided");
        }

        // Check if the slug exists in the services table
        $service = DB::table('car_services')->where('slug', $slug)->first();
        $metaTitle = $service->meta_title ?? 'Default title for the service.';
        $metaDescription = $service->meta_description ?? 'Default description for the service.';
        $metaKeywords = $service->meta_keywords ?? 'default, keywords';
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';
        if ($service) {
            return view('serviceDetails', compact('service', 'metaTitle', 'metaDescription', 'metaKeywords', 'google_tag_manager', 'tag_manager', 'analytics'));
        }

        // Check if the slug exists in the pages table
        $page = DB::table('pages')->where('slug', $slug)->where('status', 1)->orderBy('sort', 'asc')->first();
        if ($page) {
            // Fetch additional data if needed
            $subPages = DB::table('pages')
                ->where('parent_id', $page->id)
                ->where('include_headermenu', 1)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
            $page->content = ImageVersionHelper::addVersionToImages($page->content);
            $metaTitle = $page->meta_title ?? $page->title;
            $metaDescription = $page->meta_description ?? 'Default description for the page.';
            $metaKeywords = $page->meta_keywords ?? 'default, keywords';
            $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
            $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
            $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';
            return view('pages.show', compact('page', 'subPages', 'metaTitle', 'metaDescription', 'metaKeywords', 'google_tag_manager', 'tag_manager', 'analytics'));
        }

        // If neither is found, return a 404 response
        abort(404);
    }
}
