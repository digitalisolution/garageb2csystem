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
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $imagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/banner/content-pages';
            $destinationimgPath = public_path($imagePath);
            
            if (!file_exists($destinationimgPath)) {
                mkdir($destinationimgPath, 0755, true);
            }
            
            $banner->move($destinationimgPath, $bannerName);
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

        $data = $request->except(['_token', '_method', 'page_banner_path']);

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

    }


    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
    }
}
