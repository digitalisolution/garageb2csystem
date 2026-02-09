<?php

namespace App\Http\Controllers\ViewController;

use App\Http\Controllers\Controller; // Import the base Controller class
use App\Models\tyre_brands;
use App\Models\TyresProduct;
use App\Models\MetaSettings;
use App\Models\Page;
use App\Models\Blog;
use App\Models\OrderTypes;
use App\Services\DistanceService;
use App\Http\Controllers\ViewController\TyresProductController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class SitemapController extends Controller
{
    public function index()
    {
        $tyreBrandList = tyre_brands::whereIn('brand_id', TyresProduct::distinct()->pluck('tyre_brand_id'))->select('brand_id', 'name', 'slug')->get();
        //dd($tyreBrandList);

        $servicesList = \DB::table('car_services')->where('status', 1)->where('exclude_sitemap', 0)->get();
        //echo "<pre>"; print_r($tyreBrandOptions);
        $data = [
            'title' => 'sitemap',
            'content' => 'Welcome to sitemap',
        ];
        $distanceService = app(DistanceService::class);
        $tyresProductController = new TyresProductController($distanceService);

        $tyreSizesResponse = $tyresProductController->getTyreSizes();
        $orderTypes = OrderTypes::where('status', 1)->pluck('ordertype_name')->toArray();
        $fittingType = $orderTypes[0] ?? 'fully_fitted';
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';
        $tyreSizes = json_decode($tyreSizesResponse->getContent(), true);
        $infoPages = \DB::table('pages')->where('status', 1)->where('exclude_sitemap', 0)->get();
        $blogsList = \DB::table('blogs')->where('status', 1)->get();
        return view('sitemap', compact('servicesList', 'infoPages', 'fittingType','tyreSizes', 'tyreBrandList', 'blogsList', 'google_tag_manager', 'tag_manager', 'analytics'));
    }

    public function sitemapIndex()
    {
        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
        // Check if there are active pages
        $hasPages = Page::where('status', 1)
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->where('exclude_sitemap', 0)
            ->exists();
    
        if ($hasPages) {
            $sitemapContent .= '<sitemap><loc>' . url('sitemap-pages.xml') . '</loc></sitemap>';
        }
    $distanceService = app(DistanceService::class);
        $tyresProductController = new TyresProductController($distanceService);
        // Check if there are tyre sizes with data
        // $tyresProductController = new TyresProductController();
        $tyreSizesResponse = $tyresProductController->getTyreSizes();
        $tyreSizes = json_decode($tyreSizesResponse->getContent(), true);
    
        if (!empty($tyreSizes)) {
            $sitemapContent .= '<sitemap><loc>' . url('sitemap-tyresizes.xml') . '</loc></sitemap>';
        }
    
        $hasBrands = tyre_brands::where('status', 1)->get();
    
        if ($hasBrands && $hasBrands->isNotEmpty()) {
            $sitemapContent .= '<sitemap><loc>' . url('sitemap-brand.xml') . '</loc></sitemap>';
        }

        $hasBlogs = Blog::where('status', 1)->get();
        if ($hasBlogs  && $hasBlogs->isNotEmpty()) {
            $sitemapContent .= '<sitemap><loc>' . url('sitemap-blogs.xml') . '</loc></sitemap>';
        }
    
        $sitemapContent .= '</sitemapindex>';
    
        return response($sitemapContent, 200)
            ->header('Content-Type', 'application/xml');
    }
    public function sitemapPages()
    {
        $pages = Page::where('status', 1)
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->where('exclude_sitemap', 0)
            ->get();

        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapContent .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="https://www.google.com/schemas/sitemap-image/1.1">';
        $sitemapContent .= '<url>';
        $sitemapContent .= '<loc>' . url('/') . '</loc>';
        $sitemapContent .= '</url>';
        foreach ($pages as $page) {
            $sitemapContent .= '<url>';
            $sitemapContent .= '<loc>' . url($page->slug) . '</loc>';
            // $sitemapContent .= '<lastmod>' . $page->updated_at->toAtomString() . '</lastmod>';
            $sitemapContent .= '<changefreq>weekly</changefreq>';
            $sitemapContent .= '<priority>0.7</priority>';
            $sitemapContent .= '</url>';
        }
        $sitemapContent .= '</urlset>';

        return response($sitemapContent, 200)
            ->header('Content-Type', 'application/xml');
    }
    public function sitemapTyreSizes()
    {
        $distanceService = app(DistanceService::class);
        $tyresProductController = new TyresProductController($distanceService);
        $tyreSizesResponse = $tyresProductController->getTyreSizes();
        $tyreSizes = json_decode($tyreSizesResponse->getContent(), true);

        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapContent .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="https://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($tyreSizes as $tyre) {
            $sitemapContent .= '<url>';
            $sitemapContent .= '<loc>' . url('/tyres-size/' . $tyre['tyre_width'] . '-' . $tyre['tyre_profile'] . '-' . $tyre['tyre_diameter']) . '</loc>';
            // $sitemapContent .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $sitemapContent .= '<changefreq>weekly</changefreq>';
            $sitemapContent .= '<priority>0.6</priority>';
            $sitemapContent .= '</url>';
        }

        $sitemapContent .= '</urlset>';

        return response($sitemapContent, 200)
            ->header('Content-Type', 'application/xml');
    }
    public function sitemapBrands()
    {
        $manufacturers = tyre_brands::all();

        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapContent .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="https://www.google.com/schemas/sitemap-image/1.1">';

        foreach ($manufacturers as $manufacturer) {
            $sitemapContent .= '<url>';
            $sitemapContent .= '<loc>' . url('/brand/' . $manufacturer->slug) . '</loc>';
            // $sitemapContent .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $sitemapContent .= '<changefreq>weekly</changefreq>';
            $sitemapContent .= '<priority>0.6</priority>';
            $sitemapContent .= '</url>';
        }

        $sitemapContent .= '</urlset>';

        return response($sitemapContent, 200)
            ->header('Content-Type', 'application/xml');
    }
    public function sitemapBlogs()
    {
        $blogs = Blog::all();
        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapContent .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="https://www.google.com/schemas/sitemap-image/1.1">';
         $sitemapContent .= '<url>';
        $sitemapContent .= '<loc>' . url('blogs') . '</loc>';
        $sitemapContent .= '</url>';
        
        foreach ($blogs as $blog) {
            $sitemapContent .= '<url>';
            $sitemapContent .= '<loc>' . url('/blogs/' . $blog->slug) . '</loc>';
            $sitemapContent .= '<changefreq>weekly</changefreq>';
            $sitemapContent .= '<priority>0.6</priority>';
            $sitemapContent .= '</url>';
        }

        $sitemapContent .= '</urlset>';

        return response($sitemapContent, 200)
            ->header('Content-Type', 'application/xml');
    }

    /*public function sitemapManufacturerModels()
    {
        $products = DB::table('tyres_product as p')
        ->leftJoin('tyre_brands as m', 'p.tyre_brand_id', '=', 'm.brand_id')
        ->select('p.product_id', 'p.tyre_brand_id', 'm.name', 'm.slug', 'p.tyre_model')
        ->where('p.tyre_quantity', '>', 0)
        ->where('p.status', '1')
        ->where('p.date_available', '<=', now())
        ->where('p.tyre_model', '!=', '')
        ->where('p.tyre_ean', '!=', '')
        ->where('p.tyre_fuel', '!=', '')
        ->where('p.tyre_noisedb', '>', 0)
        ->where('p.tyre_wetgrip', '!=', '')
        ->where(function ($query) {
            $query->where('p.product_type', 'tyre')
                  ->orWhere('p.product_type', '');
        })
        ->groupBy('p.tyre_model')
        ->limit(50)
        ->get();

        // Start XML structure
        $output = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<urlset
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

        // Loop through the results to build sitemap URLs
        foreach ($products as $product) {
            $model = trim($product['model']);
            $model = preg_replace('!\s+!', ' ', $model); // Remove extra spaces
            $find = array(" ", "/", "'", "\\", "*", "#", "", "(", ")", "+");
            $replace = array("-", "-", "", "", "", "", "", "", "", "");
            $model = str_replace($find, $replace, $model); // Clean up the model
            $model = strtolower($model); // Convert to lowercase

            $output .= '<url>';
            $output .= '<loc>' . site_url() . 'brands/' . $product['slug'] . '/' . $model . '</loc>';
            $output .= '<changefreq>weekly</changefreq>';
            $output .= '<priority>0.7</priority>';
            $output .= '</url>';
        }
        $output .= '</urlset>';
        header('Content-type: "text/xml"; charset="utf8"');
        $this->output->set_header('Content-Type: application/xml');
        $this->output->set_output($output);
    }*/

  public function sitemapManufacturerModels()
{
    $tyresTable = (new \App\Models\TyresProduct())->getTable(); // dynamic
    $brandsTable = (new \App\Models\tyre_brands())->getTable();

    $products = TyresProduct::from("$tyresTable as p")
        ->leftJoin("$brandsTable as m", 'p.tyre_brand_id', '=', 'm.brand_id')
        ->select('p.product_id', 'p.tyre_brand_id', 'm.name', 'm.slug', 'p.tyre_model')
        ->where('p.tyre_quantity', '>', 0)
        ->where('p.status', 1)
        ->where('p.date_available', '<=', now())
        ->where('p.tyre_model', '!=', '')
        ->where('p.tyre_ean', '!=', '')
        ->where('p.tyre_fuel', '!=', '')
        ->where('p.tyre_noisedb', '>', 0)
        ->where('p.tyre_wetgrip', '!=', '')
        ->where(function ($query) {
            $query->where('p.product_type', 'tyre')
                  ->orWhere('p.product_type', '');
        })
        ->groupBy('p.tyre_model')
        ->limit(50)
        ->get();

    // XML generation
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
    $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    foreach ($products as $product) {
        $model = preg_replace('![\s/\'\\\\*#()+]+!', '-', strtolower(trim($product->tyre_model)));
        $model = preg_replace('/-+/', '-', $model);

        $url = $xml->addChild('url');
        $url->addChild('loc', url("brands/{$product->slug}/{$model}"));
        $url->addChild('changefreq', 'weekly');
        $url->addChild('priority', '0.7');
    }

    return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
}




}

