<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HeaderMenu;
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
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' =>  'nullable|string|max:255',
            'parent_type' =>  'nullable|string|max:255',
            'sort' => 'nullable|integer',
        ]);

        $data = $request->all();
        HeaderMenu::create($data);
        return redirect()->route('headermenu.index')->with('success', 'Page created successfully!');
    }

    public function edit(HeaderMenu $page)
    {
        $parentPages = HeaderMenu::whereNull('parent_id')->where('id', '!=', $page->id)->pluck('title', 'id');
        return view('AutoCare.headermenu.create', compact('page', 'parentPages'));
    }

   public function update(Request $request, HeaderMenu $page)
{
    // Validation rules
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'parent_type' => 'nullable|string|max:255',
        'sort' => 'nullable|integer',
    ]);

    // Update the model
    $page->update($request->except(['_token', '_method']));

    return redirect()->route('headermenu.index')->with('success', 'Page updated successfully!');
}
  public function getSlugs($type)
{
    if ($type === 'services') {
        $slugs = \App\Models\CarService::pluck('slug', 'service_id');
    } elseif ($type === 'pages') {
        $slugs = \App\Models\Page::pluck('slug', 'id');
    } elseif ($type === 'brands') {
        // Get brand slugs and prefix them with 'brand/'
        $brands = \App\Models\tyre_brands::pluck('slug', 'brand_id');
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
        $page->delete();
        return redirect()->route('headermenu.index')->with('success', 'Page deleted successfully!');
    }
}
