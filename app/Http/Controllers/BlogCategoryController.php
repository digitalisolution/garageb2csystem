<?php
namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\HeaderLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '21')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['categories'] = BlogCategory::all();
       return view('AutoCare.blogs.blog_categories.index', $viewData);

    }

    public function create()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '21')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        return view('AutoCare.blogs.blog_categories.create', $viewData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        BlogCategory::create([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->title),
        ]);

        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category created!');
    }

    public function edit($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '21')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['category'] = BlogCategory::findOrFail($id);
        return view('AutoCare.blogs.blog_categories.edit', $viewData);

    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $category->update([
            'title' => $request->title,
            'slug' => $request->slug ? Str::slug($request->slug) : Str::slug($request->title),
        ]);

        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        BlogCategory::destroy($id);
        return redirect()->route('AutoCare.blogs.blog_categories.index')->with('success', 'Category deleted!');
    }
}
