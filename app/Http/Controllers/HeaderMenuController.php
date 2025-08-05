<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HeaderMenu;
use App\Models\CarService;
use App\Models\tyre_brands;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class HeaderMenuController extends Controller
{
    public function index()
    {
        $pages = HeaderMenu::whereNull('parent_id')
            ->with([
                'children' => function ($query) {
                    $query->orderBy('sort', 'asc');
                }
            ])
            ->orderBy('sort', 'asc')
            ->get();

        return view('AutoCare.headermenu.index', compact('pages'));
    }


    public function create()
    {
        $parentPages = HeaderMenu::whereNull('parent_id')->pluck('title', 'id');
        return view('AutoCare.headermenu.create', compact('parentPages'));
    }

    public function store(Request $request)
    {
        try{
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' =>  'nullable|string|max:255',
            'parent_type' =>  'nullable|string|max:255',
            'sort' => 'nullable|integer',
        ]);

        HeaderMenu::create($validated);
        return redirect()->route('headermenu.index')->with('success', 'Page created successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error store Headermenu: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }

    public function edit(HeaderMenu $page)
    {
        try{
        $parentPages = HeaderMenu::whereNull('parent_id')->where('id', '!=', $page->id)->pluck('title', 'id');
        return view('AutoCare.headermenu.create', compact('page', 'parentPages'));
         } catch (\Throwable $e) {
            \Log::error("Error edit Headermenu: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }

   public function update(Request $request, HeaderMenu $page)
{
    try{
    // Validation rules
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'parent_type' => 'nullable|string|max:255',
        'sort' => 'nullable|integer',
    ]);

    // Update the model
   $page->update($validated);

    return redirect()->route('headermenu.index')->with('success', 'Page updated successfully!');
     } catch (\Throwable $e) {
            \Log::error("Error updating Headermenu: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
}
  public function getSlugs($type)
{
    if ($type === 'services') {
        $slugs = CarService::pluck('slug', 'service_id');
    } elseif ($type === 'pages') {
        $slugs = Page::pluck('slug', 'id');
    } elseif ($type === 'brands') {
        // Get brand slugs and prefix them with 'brand/'
        $brands = tyre_brands::pluck('slug', 'brand_id');
        $slugs = $brands->map(function ($slug) {
            return 'brand/' . $slug;
        });
    } else {
        return response()->json([]);
    }

    return response()->json($slugs);
}

    public function destroy(HeaderMenu $page)
    {
        try{
        $page->delete();
        return redirect()->route('headermenu.index')->with('success', 'Page deleted successfully!');
         } catch (\Throwable $e) {
            \Log::error("Error deleting Headermenu: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }
}
