<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class PageController extends Controller
{
    public function index()
    {
        $pages = Page::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->orderBy('sort', 'asc');
                }
            ])
            ->orderBy('sort', 'asc')
            ->get();

        return view('AutoCare.pages.index', compact('pages'));
    }


    public function create()
    {
        $parentPages = Page::whereNull('parent_id')->pluck('title', 'id');
        return view('AutoCare.pages.create', compact('parentPages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug',
            'page_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
            'sort' => 'nullable|integer',
        ]);

        $data = $request->all();
        if ($request->hasFile('page_banner_path')) {
            $banner = $request->file('page_banner_path');
            $bannerName = $banner->getClientOriginalName();
            $banner->storeAs('uploads/pages/banners', $bannerName, 'public');
            $data['page_banner_path'] = $bannerName;
        }

        Page::create($data);
        return redirect()->route('pages.index')->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        $parentPages = Page::whereNull('parent_id')->where('id', '!=', $page->id)->pluck('title', 'id');
        return view('AutoCare.pages.create', compact('page', 'parentPages'));
    }

    public function update(Request $request, Page $page)
{
    // Validation rules
    $request->validate([
        'title' => 'required|string|max:255',
        'slug' => [
            'required',
            'string',
            Rule::unique('pages', 'slug')->ignore($page->id),
            'regex:/^[a-zA-Z0-9#\/-]+$/',
        ],
        'page_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        'sort' => 'nullable|integer',
    ]);

    // Determine domain folder
    $host = request()->getHttpHost(); // e.g. www.digitalideasltd.in
    $domainParts = explode('.', $host);
    $domain = count($domainParts) >= 2 ? implode('-', $domainParts) : 'theme';
    $allowedDomains = ['www-digitalideasltd-in', 'example-com'];

    if (!in_array($domain, $allowedDomains)) {
        $domain = 'theme';
    }

    // Handle file upload to public path
    if ($request->hasFile('page_banner_path')) {
        $image = $request->file('page_banner_path');
        $bannerName = time() . '_' . $image->getClientOriginalName(); // optional unique name

        $destinationPath = public_path("frontend/{$domain}/img/pages/banners");

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0775, true); // create directory if not exists
        }

        $image->move($destinationPath, $bannerName);

        $page->page_banner_path = $bannerName;
    }

    // Update other fields
    $page->fill($request->except(['_token', '_method', 'page_banner_path']));
    $page->save();

    return redirect()->route('pages.index')->with('success', 'Page updated successfully!');
}


/*public function update(Request $request, Page $page)
{
    // Validation rules
    $request->validate([
        'title' => 'required|string|max:255',
        'slug' => [
            'required',
            'string',
            Rule::unique('pages', 'slug')->ignore($page->id),
            'regex:/^[a-zA-Z0-9#\/-]+$/',
        ],
        'page_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        'sort' => 'nullable|integer',
    ]);

    // Get current domain and convert to safe folder name
    $host = request()->getHttpHost(); // e.g. www.digitalideasltd.in
    $domainParts = explode('.', $host);
    
    // Convert domain into folder-safe format
    $domain = count($domainParts) >= 2 ? implode('-', $domainParts) : 'theme';

    // Optional: Fallback to 'theme' if domain is not recognized
    $allowedDomains = ['www-digitalideasltd-in', 'example-com'];
    if (!in_array($domain, $allowedDomains)) {
        $domain = 'theme';
    }

      if ($request->hasFile('page_banner_path')) {
        $image = $request->file('page_banner_path');
        $bannerName = $image->getClientOriginalName();
        $image->storeAs('frontend/themes/default/img/pages/banners', $bannerName, 'public');
        $page->page_banner_path = $bannerName;
    }

    // // Handle the page banner upload
    // if ($request->hasFile('page_banner_path')) {
    //     $banner = $request->file('page_banner_path');
    //     $bannerName = $banner->getClientOriginalName();

    //     // Define dynamic path
    //     $path = "frontend/theme/img/pages/banners";

    //     // Log the path to check where file should be saved
    //     \Log::info("Attempting to save to: " . $path);

    //     // Ensure the directory exists (recursive)
    //     // if (!Storage::disk('public')->exists($path)) {
    //     //     Storage::disk('public')->makeDirectory($path);
    //     //     \Log::info("Created folder: " . $path);
    //     // }

    //     // Store file
    //     $banner->storeAs($path, $bannerName, 'public');

    //     // Update model
    //     $page->page_banner_path = $bannerName;
    // }

    // Update the page, excluding unnecessary fields
    $page->update($request->except(['_token', '_method', 'page_banner_path']));

    return redirect()->route('pages.index')->with('success', 'Page updated successfully!');
}*/


    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
    }
}
