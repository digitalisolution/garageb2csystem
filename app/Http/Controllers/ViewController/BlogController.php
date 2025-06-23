<?php
namespace App\Http\Controllers\ViewController;

use Illuminate\Support\Str; 
use App\Http\Controllers\Controller; // Import the base Controller class
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('status', 1)->orderBy('date_added', 'desc')->paginate(4);

        $recentBlogs = Blog::where('status', 1)->orderBy('date_added', 'desc')->take(4)->get();

        $categories = BlogCategory::all()->map(function ($cat) {
            $cat->blogs_count = Blog::where('status', 1)
                ->whereRaw("FIND_IN_SET(?, category_id)", [$cat->category_id])
                ->count();
            return $cat;
        });

        $metaTitle = 'Blog';
        $metaDescription = 'Blog';

        return view('blogs.index', compact('blogs', 'recentBlogs', 'categories','metaTitle','metaDescription'));
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $recentBlogs = Blog::where('status', 1)->orderBy('date_added', 'desc')->take(4)->get();
        $categories = BlogCategory::all()->map(function ($cat) {
            $cat->blogs_count = Blog::where('status', 1)
                ->whereRaw("FIND_IN_SET(?, category_id)", [$cat->category_id])
                ->count();
            return $cat;
        });

        $metaTitle = $blog->meta_title ?? $blog->title;
        $metaKeywords = $blog->meta_keywords ?? '';
        $metaDescription = $blog->meta_description ?? Str::limit(strip_tags($blog->description), 160);

        return view('blogs.show', compact('blog', 'recentBlogs', 'categories','metaTitle','metaKeywords','metaDescription'));
    }

    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $blogs = Blog::where('status', 1)
                    ->where('category_id', $category->category_id)
                    ->orderBy('date_added', 'desc')
                    ->paginate(6);

        $recentBlogs = Blog::where('status', 1)->orderBy('date_added', 'desc')->take(5)->get();
        $categories = BlogCategory::all()->map(function ($cat) {
            $cat->blogs_count = Blog::where('status', 1)
                ->whereRaw("FIND_IN_SET(?, category_id)", [$cat->category_id])
                ->count();
            return $cat;
        });

        $metaTitle = $category->title . ' Blogs';
        $metaDescription = $category->title . ' Blogs';

        return view('blogs.index', compact('blogs', 'recentBlogs', 'categories', 'metaTitle','metaDescription'));
    }
    /*public function search(Request $request)
    {
        $query = $request->input('q');

        $blogs = Blog::where('status', 1)
            ->where(function ($q1) use ($query) {
                $q1->where('title', 'like', '%' . $query . '%')
                   ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderBy('date_added', 'desc')
            ->paginate(6);

        $recentBlogs = Blog::where('status', 1)->orderBy('date_added', 'desc')->take(5)->get();

        $categories = BlogCategory::all()->map(function ($cat) {
            $cat->blogs_count = Blog::where('status', 1)
                ->whereRaw("FIND_IN_SET(?, categories)", [$cat->category_id])
                ->count();
            return $cat;
        });
       $metaTitle = 'Search: ' . $query;
    $metaDescription = 'Search results for "' . $query . '"';

    return view('blogs.index', compact('blogs', 'recentBlogs', 'categories', 'metaTitle', 'metaDescription'))->with('search', $query);

    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('term');

        $results = Blog::where('title', 'LIKE', "%{$search}%")
            ->where('status', 1)
            ->limit(10)
            ->get(['title', 'slug']);

        return response()->json($results);
    }*/

}

