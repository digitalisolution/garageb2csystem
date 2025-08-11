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
        $pages = page::orderBy('sort', 'asc')->get();
        return view('AutoCare.pages.index', compact('pages'));
    }


    public function create()
    {
        return view('AutoCare.pages.create');

    }

    public function store(Request $request)
    {
        try{
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug|regex:/^[a-zA-Z0-9#\/-]+$/',
        'tyre_search_form' => 'required|in:0,1',
        'exclude_sitemap' => 'required|in:0,1',
        'content' => 'nullable|string',
        'page_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        'sort' => 'nullable|integer',
        'status' => 'required|in:0,1',
        'schema_status' => 'nullable|string',
        'meta_title' => 'nullable|string|max:150',
        'meta_keywords' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:5000', // Optional long length
        ]);

        if ($request->hasFile('page_banner_path')) {
            $banner = $request->file('page_banner_path');
            $bannerName = $banner->getClientOriginalName();
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/banner/content-pages';
            $destinationimgPath = public_path($imagePath);
            
            if (!file_exists($destinationimgPath)) {
                mkdir($destinationimgPath, 0755, true);
            }
            
            $banner->move($destinationimgPath, $bannerName);
            $validated['page_banner_path'] = $bannerName;
        }

        Page::create($validated);
        return redirect()->route('pages.index')->with('success', 'Page created successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error creating page: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }

    public function edit(Page $page)
    {
        return view('AutoCare.pages.create', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        // Validation rules
       $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                Rule::unique('pages', 'slug')->ignore($page->id),
                'regex:/^[a-zA-Z0-9#\/-]+$/',
            ],
        'tyre_search_form' => 'required|in:0,1',
        'exclude_sitemap' => 'required|in:0,1',
        'content' => 'nullable|string',
        'page_banner_path' => 'nullable|image|mimes:jpeg,webp,png,jpg,gif|max:2048',
        'sort' => 'nullable|integer',
        'status' => 'required|in:0,1',
        'schema_status' => 'nullable|string',
        'meta_title' => 'nullable|string|max:150',
        'meta_keywords' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:5000',
        ]);
        
        $data = collect($validated)->except(['page_banner_path'])->toArray();
        try{

        if ($request->hasFile('page_banner_path')) {
            $banner = $request->file('page_banner_path');
            $bannerName = $banner->getClientOriginalName();
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/banner/content-pages';
            $destinationimgPath = public_path($imagePath);
            
            if (!file_exists($destinationimgPath)) {
                mkdir($destinationimgPath, 0755, true);
            }
            
            $banner->move($destinationimgPath, $bannerName);
            $data['page_banner_path'] = $bannerName;
        }
        $page->update($data);

        return redirect()->route('pages.index')->with('success', 'Page updated successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error updating page: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }

    }


    public function destroy(Page $page)
    {
        try{
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error deleting page: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }
}
