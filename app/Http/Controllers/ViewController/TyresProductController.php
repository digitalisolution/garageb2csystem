<?php

namespace App\Http\Controllers\ViewController;

use App\Models\tyre_brands;
use App\Models\TyresProduct;
use App\Models\MetaSettings;
use App\Models\GarageDetails;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TyresProductController extends Controller
{
    public function tyreslist(Request $request, $width = null, $profile = null, $diameter = null, $fitting_type = null)
    {
        if ($width && $profile && $diameter) {
            $request->merge([
                'width' => $width,
                'profile' => $profile,
                'diameter' => $diameter,
            ]);
        }

        $widths = TyresProduct::select('tyre_width')->distinct()->pluck('tyre_width');
        $profiles = TyresProduct::select('tyre_profile')->distinct()->pluck('tyre_profile');
        $diameters = TyresProduct::select('tyre_diameter')->distinct()->pluck('tyre_diameter');

        $recommendedTyres = $this->getRecommendedTyres($request);
        $message = empty($recommendedTyres) ? 'No recommended tyres found.' : null;
        $query = TyresProduct::query();
        $query->where('tyre_quantity', '>', 0);
        $query->where('tyre_fullyfitted_price', '>', 0);
        if ($request->filled('width')) {
            $query->where('tyre_width', $request->width);
        }
        if ($request->filled('profile')) {
            $query->where('tyre_profile', $request->profile);
        }
        if ($request->filled('diameter')) {
            $query->where('tyre_diameter', $request->diameter);
        }
        $query->where('status', '=', 1);
        $query->orderByRaw("CASE WHEN tyre_supplier_name = 'ownstock' THEN 1 ELSE 2 END")
            ->orderBy('tyre_price', 'asc');
            
        $tyres = $query->paginate(72)->appends($request->except('page'));
        $leadTimes = [];
         $order_type = $fitting_type ?? $request->input('fitting_type');
        foreach ($tyres as $tyre) {
            if ($tyre->supplier_id) {
                $supplierLeadTime = $this->getLeadTime($tyre->supplier_id, $order_type); // Call your method
                $leadTimes[$tyre->product_id] = $supplierLeadTime[$tyre->supplier_id] ?? null;
            }
        }
        // dd($leadTimes);
        $garage = GarageDetails::find(1);
        $garageName = $garage->garage_name ?? 'Garage Solutions';
        $size = ($request->filled('width') && $request->filled('profile') && $request->filled('diameter'))
            ? $request->width . ' ' . $request->profile . ' r' . $request->diameter
            : '';
        $metaTitle = MetaSettings::where('name', 'manu_size_meta_title')->value('content') ?? 'Default Meta Title';
        $metaDescription = MetaSettings::where('name', 'manu_size_meta_description')->value('content') ?? 'Default Meta Description';
        $metaKeywords = MetaSettings::where('name', 'manu_size_meta_keyword')->value('content') ?? 'Default Meta Keywords';
        $google_tag_manager = MetaSettings::where('name', 'google_tag_manager')->value('content') ?? '';
        $tag_manager = MetaSettings::where('name', 'tag_manager')->value('content') ?? '';
        $analytics = MetaSettings::where('name', 'analytics')->value('content') ?? '';
        $metaTitle = str_replace(['$store', '$size'], [$garageName, $size], $metaTitle);
        $metaDescription = str_replace(['$store', '$size'], [$garageName, $size], $metaDescription);
        $metaKeywords = str_replace(['$store', '$size'], [$garageName, $size], $metaKeywords);
        return view('tyreslist', compact('tyres', 'widths', 'profiles', 'diameters','leadTimes', 'recommendedTyres', 'message', 'metaTitle', 'metaDescription', 'metaKeywords', 'google_tag_manager', 'tag_manager', 'analytics'));
    }



    public function getLeadTime($supplierId = '', $fittingType='')
    {
        // dd($fittingType);

        $leadtimeArray = [];

        $now = Carbon::now();
        $currentDay = strtolower($now->format('l'));
        $currentDate = $now->format('Y-m-d');

        $query = DB::table('deliverytime');
        if (!empty($supplierId)) {
            $query->where('supplier', $supplierId);
        }
        if (!empty($fittingType)) {
            $query->where('delivery_type', $fittingType);
        }

        $deliveryTimes = $query->get();
            
        foreach ($deliveryTimes as $data) {
            $weekDay = strtolower($data->day);
            $deliveryTimeHours = (int) $data->delivery_time;

            $startTime = Carbon::createFromTime($data->start_hours, $data->start_minutes);
            $endTime = Carbon::createFromTime($data->end_hours, $data->end_minutes);

            if ($currentDay === $weekDay && $now->between($startTime, $endTime)) {
                $endTimeWithDate = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $endTime->format('H:i:s'));
                $deliveryDateTime = $endTimeWithDate->copy()->addHours($deliveryTimeHours);
                $daysBetween = $now->diffInDays($deliveryDateTime);

                $supplierCode = $data->supplier;

                if ($daysBetween === 1) {
                    $label = 'Available Tomorrow';
                } elseif ($currentDate === $deliveryDateTime->format('Y-m-d')) {
                    $label = 'Available Today';
                } else {
                    $label = 'Available From' . ' ' . $deliveryDateTime->format('M jS');
                }

                $leadtimeArray[$supplierCode] = [
                    'label' => $label,
                    'hours' => $deliveryTimeHours,
                ];
            }
        }

        return $leadtimeArray;
    }

    
    // Fetch Profiles Based on Width
    public function getProfiles(Request $request)
    {
        $profiles = TyresProduct::where('tyre_width', $request->width)
            ->select('tyre_profile')
            ->where('tyre_fullyfitted_price', '>', 0)
            ->distinct()
            ->pluck('tyre_profile');

        $options = '<option value="">Select Profile</option>';
        foreach ($profiles as $profile) {
            $options .= "<option value=\"$profile\">$profile</option>";
        }

        return $options;
    }

    // Fetch Diameters Based on Width and Profile
    public function getDiameters(Request $request)
    {
        $diameters = TyresProduct::where('tyre_width', $request->width)
            ->where('tyre_profile', $request->profile)
            ->where('tyre_fullyfitted_price', '>', 0)
            ->select('tyre_diameter')
            ->distinct()
            ->pluck('tyre_diameter');

        $options = '<option value="">Select Diameter</option>';
        foreach ($diameters as $diameter) {
            $options .= "<option value=\"$diameter\">$diameter</option>";
        }

        return $options;
    }
    public function getFuelEfficiencyOptions(Request $request)
    {
        $fuelEfficiencies = TyresProduct::whereNotNull('tyre_fuel')
            ->where('tyre_fuel', '!=', '') 
            ->where('tyre_fuel', '!=', '-') 
            ->distinct()
            ->pluck('tyre_fuel');

        return response()->json($fuelEfficiencies);
    }


    public function getSeasonOptions(Request $request)
    {
        $seasons = TyresProduct::distinct()->where('tyre_season', '!=', '')->pluck('tyre_season'); 

        return response()->json($seasons);
    }
  
    public function getWetGripOptions(Request $request)
    {

        $wetGripOption = TyresProduct::whereNotNull('tyre_wetgrip')
            ->where('tyre_wetgrip', '!=', '') 
            ->where('tyre_wetgrip', '!=', '-') 
            ->distinct()
            ->pluck('tyre_wetgrip'); 

        return response()->json($wetGripOption);
    }

    public function getTyreBrandOptions(Request $request)
    {
        // Fetch unique manufacturer IDs from TyresProduct and their corresponding brand names
        $tyreBrandOptions = tyre_brands::whereIn(
            'brand_id',
            TyresProduct::distinct()->pluck('tyre_brand_id')
        )
            ->select('brand_id', 'name')
            ->orderBy('name', 'asc')
            ->get();
        return response()->json($tyreBrandOptions);
    }


    public function getPriceRange()
    {
        $minPrice = TyresProduct::min('tyre_price');
        $maxPrice = TyresProduct::max('tyre_price');

        return response()->json([
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice
        ]);
    }

    public function filter(Request $request, $width = null, $profile = null, $diameter = null)
    {
        // dd($request);
        if ($width && $profile && $diameter) {
            $request->merge([
                'width' => $width,
                'profile' => $profile,
                'diameter' => $diameter,
            ]);
        }
        $page = $request->input('page', 1);
        $recommendedTyres = $this->getRecommendedTyres($request);
        $message = empty($recommendedTyres) ? 'No recommended tyres found.' : null;
        $query = TyresProduct::query();
        $query->where('tyre_quantity', '>', 0);
        $query->where('tyre_fullyfitted_price', '>', 0);
        $query->where('tyres_product.status', '=', '1');

        $query->join('tyre_brands', 'tyre_brands.brand_id', '=', 'tyres_product.tyre_brand_id')
            ->select('tyres_product.*', 'tyre_brands.recommended_tyre');

        if ($request->filled('width')) {
            $query->where('tyre_width', $request->input('width'));
        }
        if ($request->filled('profile')) {
            $query->where('tyre_profile', $request->input('profile'));
        }
        if ($request->filled('diameter')) {
            $query->where('tyre_diameter', $request->input('diameter'));
        }
        if ($request->filled('minPrice') && $request->filled('maxPrice')) {
            $query->whereBetween('tyre_price', [$request->input('minPrice'), $request->input('maxPrice')]);
        }
        if ($request->filled('fuel')) {
            $fuelEfficiency = $request->input('fuel');
            $query->whereIn('tyre_fuel', $fuelEfficiency);
        }
        if ($request->filled('season')) {
            $getSeason = $request->input('season');
            $query->whereIn('tyre_season', $getSeason);
        }
        if ($request->filled('wetGrip')) {
            $wetGrip = $request->input('wetGrip');
            $query->whereIn('tyre_wetgrip', $wetGrip);
        }
        $runflat = $request->input('runflat'); 
        $extraload = $request->input('extraload'); 
        if ($runflat === '1') {
            $query->where('tyre_runflat', 1);
        }
        if ($extraload === '1') {
            $query->where('tyre_extraload', 1);
        }
        if ($request->filled('tyreBrand')) {
            $tyreBrandIds = $request->input('tyreBrand');
            $query->whereIn('tyres_product.tyre_brand_id', $tyreBrandIds)
                ->orderByRaw('FIELD(tyres_product.tyre_brand_id, ' . implode(',', $tyreBrandIds) . ')');
        }
        $recommendedTyres = [];
        $message = null;
        // Add logic for recommended tyres to show only when a user filters based on size
        if ($request->filled('width') || $request->filled('profile') || $request->filled('diameter')) {
            $recommendedTyres = $this->getRecommendedTyres($request);
        }

        $query->orderByRaw("CASE WHEN tyre_supplier_name = 'ownstock' THEN 1 ELSE 2 END")
            ->orderBy('tyre_price', 'asc');

        // Fetch paginated results
        // $tyres = $query->paginate(7)->appends($request->except('page'));

        $tyres = $query->paginate(72, ['*'], 'page', $page)->appends($request->except('page'));

        // // If no tyres are found, return an appropriate message
        // if ($tyres->isEmpty()) {
        //     return response()->json([
        //         'tyres' => '<p>No tyres found matching the criteria.</p>',
        //         'pagination' => '',
        //         'recommendedTyresMessage' => $message,
        //         'recommendedTyres' => []
        //     ]);
        // }
          $leadTimes = [];
         $order_type = $fitting_type ?? $request->input('fitting_type');
        foreach ($tyres as $tyre) {
            if ($tyre->supplier_id) {
                $supplierLeadTime = $this->getLeadTime($tyre->supplier_id, $order_type); // Call your method
                $leadTimes[$tyre->product_id] = $supplierLeadTime[$tyre->supplier_id] ?? null;
            }
        }

        try {
            $tyresHtml = view('tyre-cards', compact('tyres','leadTimes'))->render();
            $hasMorePages = $tyres->hasMorePages() ? true : false;
    
            // Only render recommended tyres on first load
            $recommendedTyresHtml = $page === 1
                ? view('recommended-tyres', compact('recommendedTyres'))->render()
                : '';
    
        } catch (\Exception $e) {
            \Log::error('View Rendering Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to render view'], 500);
        }

        // Return the response with rendered HTML for tyres, pagination, and recommended tyres
        return response()->json([
            'tyres' => $tyresHtml,
            'has_more_pages' => $hasMorePages,
            'recommendedTyresMessage' => $message,
            'recommendedTyres' => $recommendedTyresHtml,
        ]);
    }




    public function getRecommendedTyres(Request $request, $width = null, $profile = null, $diameter = null)
    {
        // Merge route parameters into the request object
        if ($width && $profile && $diameter) {
            $request->merge([
                'width' => $width,
                'profile' => $profile,
                'diameter' => $diameter,
            ]);
        }

        // Fetch all recommended brands
        $recommendedBrands = DB::table('tyre_brands')
            ->where('recommended_tyre', 1)
            ->pluck('brand_id')
            ->toArray();

        // Initialize the query
        $query = collect(); // Using collection to store final data

        // Loop through each recommended brand and get the best tyre
        foreach ($recommendedBrands as $brandId) {
            // Fetch cheapest tyre from ownstock for this brand
            $ownstockTyre = DB::table('tyres_product as tp')
                ->select('tp.*', 'tb.name as brand_name', 'tb.image as brand_logo', 'tb.budget_type as budget_type', 'tb.recommended_tyre')
                ->join('tyre_brands as tb', 'tb.brand_id', '=', 'tp.tyre_brand_id')
                ->where('tb.brand_id', $brandId)
                ->where('tb.recommended_tyre', 1)
                ->where('tp.tyre_price', '>', 0)
                ->where('tp.tyre_quantity', '>', 0)
                ->where('tp.tyre_supplier_name', '=', 'ownstock');

            // Apply filters dynamically
            if ($request->filled('width')) {
                $ownstockTyre->where('tp.tyre_width', '=', $request->input('width'));
            }
            if ($request->filled('profile')) {
                $ownstockTyre->where('tp.tyre_profile', '=', $request->input('profile'));
            }
            if ($request->filled('diameter')) {
                $ownstockTyre->where('tp.tyre_diameter', '=', $request->input('diameter'));
            }

            // Get the cheapest ownstock tyre
            $ownstockTyre = $ownstockTyre->where('tp.status','=',1)->orderBy('tp.tyre_price', 'asc')->first();

            // If ownstock tyre exists, add it to the collection
            if ($ownstockTyre) {
                $query->push($ownstockTyre);
                continue; // Move to the next brand
            }

            // If no ownstock tyre, fetch the cheapest supplier tyre
            $supplierTyre = DB::table('tyres_product as tp')
                ->select('tp.*', 'tb.name as brand_name', 'tb.image as brand_logo', 'tb.budget_type as budget_type', 'tb.recommended_tyre')
                ->join('tyre_brands as tb', 'tb.brand_id', '=', 'tp.tyre_brand_id')
                ->where('tb.brand_id', $brandId)
                ->where('tb.recommended_tyre', 1)
                ->where('tp.tyre_price', '>', 0)
                ->where('tp.tyre_quantity', '>', 0)
                ->where('tp.status', '=', 1)
                ->where('tp.tyre_supplier_name', '!=', 'ownstock'); // Only supplier tyres

            // Apply filters dynamically
            if ($request->filled('width')) {
                $supplierTyre->where('tp.tyre_width', '=', $request->input('width'));
            }
            if ($request->filled('profile')) {
                $supplierTyre->where('tp.tyre_profile', '=', $request->input('profile'));
            }
            if ($request->filled('diameter')) {
                $supplierTyre->where('tp.tyre_diameter', '=', $request->input('diameter'));
            }

            // Get the cheapest supplier tyre
            $supplierTyre = $supplierTyre->orderBy('tp.tyre_price', 'asc')->first();

            // If supplier tyre exists, add it to the collection
            if ($supplierTyre) {
                $query->push($supplierTyre);
            }
        }

        // Convert the collection into the final response format
        $recommendedTyres = $query->sortBy('tyre_price')->values();
        $leadTimes = [];
         $order_type = $request->input('fitting_type');
            foreach ($recommendedTyres as $tyre) {
                if ($tyre->supplier_id) {
                    $supplierLeadTime = $this->getLeadTime($tyre->supplier_id, $order_type);
                    $leadTime = $supplierLeadTime[$tyre->supplier_id] ?? null;
                } else {
                    $leadTime = null;
                }

                // Attach lead_time to the tyre object
                $tyre->lead_time = $leadTime;
            }

        //dd($recommendedTyres);
        // Return the results
        return $recommendedTyres;
    }


    public function productDetail(Request $request, string $brand, ?string $model = null, ?string $size = null)
    {
        // Extract tyre ID from query string
        $tyreId = $request->query('tyre');
    
        if (!$tyreId) {
            abort(404);
        }
    
        // Fetch tyre by ID
        $tyre = TyresProduct::with('brand') // Assuming you have a relation called brandRelation
            ->findOrFail($tyreId);
    
        // Optional: Verify that brand matches
        if ($tyre->brand && strtolower($tyre->brand->slug) !== $brand) {
            abort(404); // Prevent mismatched URLs
        }
    
        return view('tyre-productDetails', compact('tyre', 'brand', 'model', 'size'));
    }


    public function getTyreSizes()
    {
        // Query to fetch distinct tyre sizes
        $tyreSizes = DB::table('tyres_product as tp')
            ->select('tp.tyre_width', 'tp.tyre_profile', 'tp.tyre_diameter')
            ->distinct()
            ->join('tyre_brands as m', 'm.brand_id', '=', 'tp.tyre_brand_id')
            ->where('tp.tyre_width', '>', 0)
            ->where('tp.tyre_quantity', '>', 0)
            ->where('tp.tyre_fullyfitted_price', '>', 0)
            ->where('tp.status', '=', 1)
            ->groupBy('m.brand_id', 'tp.tyre_width', 'tp.tyre_profile', 'tp.tyre_diameter')
            ->orderBy('tp.tyre_width')
            ->orderBy('tp.tyre_profile')
            ->orderBy('tp.tyre_diameter')
            ->get();

        return response()->json($tyreSizes);
    }



}

