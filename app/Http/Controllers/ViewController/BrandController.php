<?php

namespace App\Http\Controllers\ViewController;
use App\Models\tyre_brands;
use App\Models\MetaSettings;
use App\Models\GarageDetails;
use App\Http\Controllers\Controller; // Import the base Controller class
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $tyresModel = new TyresProduct();
        $tyresTable = $tyresModel->getTable(); // Dynamic table name
        $brandsTable = (new \App\Models\tyre_brands())->getTable(); // 
        $query = TyresProduct::from("$tyresTable as tp");
        // Retrieve brands with the necessary conditions
        $brands = tyre_brands::from("$brandsTable as m")
            ->leftJoin("$tyresTable as p", 'm.brand_id', '=', 'p.tyre_brand_id')
            ->select('m.name', 'm.brand_id', 'm.image', 'm.slug')
            ->where('p.tyre_quantity', '>', 0)
            ->whereNotNull('m.name')
            ->whereNotNull('p.product_type')
            ->where('m.status', 1)
            ->groupBy('m.name', 'm.brand_id', 'm.image', 'm.slug')
            ->orderBy('m.name', 'asc')
            ->get();
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';
        return view('brands.index', compact('brands', 'google_tag_manager', 'tag_manager', 'analytics'));

    }
    public function show($slug)
    {
        // Retrieve the brand by its slug
        $brand = tyre_brands::where('slug', $slug)->firstOrFail();

        // Retrieve the garage details
        $garage = GarageDetails::find(1); // Replace 1 with the appropriate ID or query to get the garage record
        $garageName = $garage->garage_name ?? 'Garage Solutions';

        // Retrieve meta settings by their names
        $metaTitle = MetaSettings::where('name', 'manu_meta_title')->value('content') ?? 'Default Meta Title';
        $metaDescription = MetaSettings::where('name', 'manu_meta_description')->value('content') ?? 'Default Meta Description';
        $metaKeywords = MetaSettings::where('name', 'manu_meta_keywords')->value('content') ?? 'Default Meta Keywords';
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';

        // Replace placeholders in meta tags
        $metaTitle = str_replace(['$store', '$brand'], [$garageName, $brand->name], $metaTitle);
        $metaDescription = str_replace(['$store', '$brand'], [$garageName, $brand->name], $metaDescription);
        $metaKeywords = str_replace(['$store', '$brand'], [$garageName, $brand->name], $metaKeywords);

        // Return the view with brand and meta data
        return view('brands.show', compact('brand', 'metaTitle', 'metaDescription', 'metaKeywords', 'google_tag_manager', 'tag_manager', 'analytics'));
    }

}

