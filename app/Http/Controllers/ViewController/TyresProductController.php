<?php

namespace App\Http\Controllers\ViewController;

use App\Models\tyre_brands;
use App\Models\TyresProduct;
use App\Models\MetaSettings;
use App\Models\GarageDetails;
use App\Models\Supplier;
use Illuminate\Support\Facades\Http;
use App\Models\OrderTypes;
use Illuminate\Support\Facades\Session;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

   


class TyresProductController extends Controller
{
     protected $distanceService;

    /**
     * Constructor to inject dependencies.
     *
     * @param DistanceService $distanceService
     */
    public function __construct(DistanceService $distanceService) {
        $this->distanceService = $distanceService;
    }

    public function tyreslist(Request $request, 
    $width = null, 
    $profile = null, 
    $diameter = null, 
    $fitting_type = null,
    $postcode = null
    )
{
    $vehicleType = $request->get('vehicle_type');
    if ($width && $profile && $diameter) {
        $request->merge([
            'width' => $width,
            'profile' => $profile,
            'diameter' => $diameter,
        ]);
    }

    // dd(Session::all());
    $widths = TyresProduct::select('tyre_width')->where('vehicle_type', $vehicleType)->distinct()->pluck('tyre_width');
    $profiles = TyresProduct::select('tyre_profile')->where('vehicle_type', $vehicleType)->distinct()->pluck('tyre_profile');
    $diameters = TyresProduct::select('tyre_diameter')->where('vehicle_type', $vehicleType)->distinct()->pluck('tyre_diameter');
    $activeSupplierIds = Supplier::where('website_display_status', 1)->where('status', 1)->pluck('id');
    $getMiles = get_option('get_miles_for_tyres');

    $recommendedTyres = $this->getRecommendedTyres($request);
    $message = empty($recommendedTyres) ? 'No recommended tyres found.' : null;
    $postcode = $postcode ?? $request->input('postcode') ?? session('user_postcode');
    $origin = getGarageDetails()->zone;
    $destination = $postcode ? $postcode . ', UK' : null;

    $distanceMiles = $postcode
        ? $this->distanceService->getDistanceMiles($origin, $postcode)
        : null;

    $query = TyresProduct::query();
    $query->whereIn('supplier_id', $activeSupplierIds);
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
    if ($request->filled('load_index')) {
        $query->where('tyre_loadIndex', $request->load_index);
    }
    if ($request->filled('speed_index')) {
        $query->where('tyre_speed', $request->speed_index);
    }
    if ($request->filled('vehicle_type')) {
        $query->where('vehicle_type', $request->vehicle_type);
    }

    $query->where('status', '=', 1);
        // dd($distanceMiles);
    if ($distanceMiles !== null && $distanceMiles > $getMiles) {
        $query->where('tyre_supplier_name', '!=', 'ownstock');
    }

    $query->orderByRaw("CASE WHEN tyre_supplier_name = 'ownstock' THEN 1 ELSE 2 END")
          ->orderBy('tyre_price', 'asc');

    $tyres = $query->paginate(72)->appends($request->except('page'));
    // --- Lead times ---
    $leadTimes = [];
    $order_type = $fitting_type ?? $request->input('fitting_type') ?? session('user_ordertype') ;
    foreach ($tyres as $tyre) {
        if ($tyre->supplier_id) {
            $supplierLeadTime = $this->getLeadTime($tyre->supplier_id, $order_type);
            $leadTimes[$tyre->product_id] = $supplierLeadTime[$tyre->supplier_id] ?? null;
        }
    }

    // --- Meta Data ---
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
    if ($postcode) {
        session(['user_postcode' => $postcode]);
    }
    if ($postcode) {
        session(['user_ordertype' => $order_type]);
    }

    return view('tyreslist', compact(
        'tyres',
        'widths',
        'profiles',
        'diameters',
        'leadTimes',
        'recommendedTyres',
        'message',
        'metaTitle',
        'metaDescription',
        'metaKeywords',
        'google_tag_manager',
        'tag_manager',
        'analytics',
        'distanceMiles'
    ));
}

    public function getLeadTime($supplierId = '', $fittingType = '')
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

    // In the method that renders your search form page

    public function getWidths(Request $request)
{
    $vehicleType = $request->get('vehicleType');
    
    $widths = TyresProduct::select('tyre_width')
        ->where('vehicle_type', strtolower($vehicleType))
        ->where('tyre_width', '>', 0)
        ->where('tyre_quantity', '>', 0)
        ->where('tyre_fullyfitted_price', '>', 0)
        ->where('status', 1)
        ->distinct()
        ->orderBy('tyre_width')
        ->pluck('tyre_width');
    
    $options = '<option value="">Select Width</option>';
    foreach ($widths as $width) {
        $options .= '<option value="' . $width . '">' . $width . '</option>';
    }
    
    return $options;
}

    public function getProfiles(Request $request)
{
    $vehicleType = $request->get('vehicleType') ?? 'car';
    $profiles = TyresProduct::where('tyre_width', $request->width)
        ->where('vehicle_type', strtolower($vehicleType))
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

    public function getDiameters(Request $request)
    {
    $width = $request->get('width');
    $profile = $request->get('profile');
    $vehicleType = $request->get('vehicleType') ?? 'car';
         $diameters = TyresProduct::select('tyre_diameter')
        ->where('vehicle_type', strtolower($vehicleType))
        ->where('tyre_width', $width)
        ->where('tyre_profile', $profile)
        ->where('tyre_diameter', '>', 0)
        ->where('tyre_quantity', '>', 0)
        ->where('tyre_fullyfitted_price', '>', 0)
        ->where('status', 1)
        ->distinct()
        ->orderBy('tyre_diameter')
        ->pluck('tyre_diameter');
        $options = '<option value="">Select Rim</option>';
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
            ->orderBy('tyre_fuel')
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
            ->orderBy('tyre_wetgrip')
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
    public function getLoadIndexOptions()
    {
        try {

            $loadIndexes = TyresProduct::select('tyre_loadIndex')
                                        ->where('tyre_loadIndex', '!=', '')
                                        ->whereNotNull('tyre_loadIndex')
                                        ->distinct()
                                        ->pluck('tyre_loadIndex');

            return response()->json($loadIndexes);
        } catch (\Exception $e) {
            \Log::error('Error fetching Load Index options: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    public function getNoiseLevelOptions()
    {
        try {
            $noiseLevels = TyresProduct::select('tyre_noisedb')
                ->whereNotNull('tyre_noisedb')
                ->where('tyre_noisedb', '!=', '')
                ->groupBy('tyre_noisedb')
                ->orderBy('tyre_noisedb')
                ->pluck('tyre_noisedb');
            
            return response()->json($noiseLevels);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch noise levels'], 500);
        }
    }
    public function getSpeedIndexOptions()
    {
        try {

            $columnName = 'tyre_speed';
            $speedIndexes = TyresProduct::select($columnName)
                                        ->where($columnName, '!=', '')
                                        ->whereNotNull($columnName)
                                        ->distinct()
                                        ->pluck($columnName);
            $speedIndexRanking = [
                'L' => 1, 'M' => 2, 'N' => 3, 'P' => 4, 'Q' => 5, 'R' => 6,
                'S' => 7, 'T' => 8, 'U' => 9, 'H' => 10, 'V' => 11, 'W' => 12,
                'Y' => 13, 'ZR' => 14
            ];
            $sortedUniqueSpeedIndexes = $speedIndexes->sort(function ($a, $b) use ($speedIndexRanking) {
                $rankA = $speedIndexRanking[$a] ?? 999;
                $rankB = $speedIndexRanking[$b] ?? 999;
                if ($rankA == $rankB) {
                    return strcmp($a, $b);
                }
                return $rankA <=> $rankB;
            })->values();
            return response()->json($sortedUniqueSpeedIndexes);

        } catch (\Exception $e) {
            \Log::error('Error fetching Speed Index options: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    public function getVehicleTypeOptions()
    {
        try {
            $vehicleTypes = TyresProduct::select('vehicle_type')
                ->whereNotNull('vehicle_type')
                ->where('vehicle_type', '!=', '')
                ->distinct()
                ->pluck('vehicle_type');

            return response()->json($vehicleTypes);
        } catch (\Exception $e) {
            \Log::error('Error fetching Vehicle Type options: ' . $e->getMessage());
            return response()->json([]);
        }
    }
    public function getOrderTypesOptions(Request $request)
    {
        try {
             $typeMap = [
            'car' => 'car',
            'truck' => 'truck',
            'commercial truck' => 'truck',
            'motorbike' => 'motorbike',
        ];

        $vehicleType = strtolower(trim($request->vehicleType ?? 'car'));
        $vehicleType = $typeMap[$vehicleType] ?? 'car';
        if (!in_array($vehicleType, ['car', 'truck', 'motorbike'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vehicle type.'
            ], 400);
        }
            // dd($vehicleType);
        // Query order types dynamically by column
        $orderTypes = OrderTypes::where('status', 1)
            ->where($vehicleType, 1)
            ->pluck('ordertype_name');
            return response()->json($orderTypes);
        } catch (\Exception $e) {
            \Log::error("Order Type Fetch Failed", ['error' => $e->getMessage()]);
            return response()->json([]);
        }
    }

/*   public function getOrderTypes(Request $request)
    {
        // Map frontend types to DB columns
        $typeMap = [
            'car' => 'car',
            'truck' => 'truck',
            'commercial truck' => 'truck',
            'motorbike' => 'motorbike',
        ];

        // Normalize request value → fallback to car
        $vehicleType = strtolower($request->vehicleType ?? 'car');
        $vehicleType = $typeMap[$vehicleType] ?? 'car';

        // Query order types
        $orderTypes = OrderTypes::where('status', 1)
            ->where($vehicleType, 1)
            ->pluck('ordertype_name');

        return response()->json($orderTypes);
    }*/

    public function getOrderTypes(Request $request)
{
    try {
        // Map frontend types to DB columns
        $typeMap = [
            'car' => 'car',
            'truck' => 'truck',
            'commercial truck' => 'truck',
            'motorbike' => 'motorbike',
        ];

        // Normalize request value → fallback to car
        $vehicleType = strtolower(trim($request->vehicleType ?? 'car'));
        $vehicleType = $typeMap[$vehicleType] ?? 'car';

        // Ensure the column exists before querying
        if (!in_array($vehicleType, ['car', 'truck', 'motorbike'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vehicle type.'
            ], 400);
        }

        // Query order types dynamically by column
        $orderTypes = OrderTypes::where('status', 1)
            ->where($vehicleType, 1)
            ->pluck('ordertype_name');

        // Return structured JSON
        return response()->json([
            'success' => true,
            'vehicle_type' => $vehicleType,
            'order_types' => $orderTypes,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching order types: ' . $e->getMessage(),
        ], 500);
    }
}




    public function filter(Request $request, $width = null, $profile = null, $diameter = null)
    {
        if ($width && $profile && $diameter) {
            $request->merge([
                'width' => $width,
                'profile' => $profile,
                'diameter' => $diameter,
            ]);
        }

        $postcode = $postcode ?? $request->input('postcode') ?? session('user_postcode');
        $origin = getGarageDetails()->zone;
        $destination = $postcode ? $postcode . ', UK' : null;
        $getMiles = get_option('get_miles_for_tyres');

        $distanceMiles = $postcode
            ? $this->distanceService->getDistanceMiles($origin, $postcode)
            : null;
            
        $page = $request->input('page', 1);
        $recommendedTyres = $this->getRecommendedTyres($request);
        $message = empty($recommendedTyres) ? 'No recommended tyres found.' : null;
        
        $tyresModel = new TyresProduct();
        $tyresTable = $tyresModel->getTable();
        $brandsTable = (new \App\Models\tyre_brands())->getTable();
        
        $activeSupplierIds = Supplier::where('website_display_status', 1)->where('status', 1)->pluck('id');
        $query = TyresProduct::from("$tyresTable as tp");
        $query->whereIn('tp.supplier_id', $activeSupplierIds);
        $query->where('tyre_quantity', '>', 0);
        $query->where('tyre_fullyfitted_price', '>', 0);
        $query->where('tp.status', '=', '1');

        $query->join("$brandsTable as tb", 'tb.brand_id', '=', 'tp.tyre_brand_id')
            ->select('tp.*', 'tb.recommended_tyre');

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
            $query->whereIn('tp.tyre_brand_id', $tyreBrandIds)
                ->orderByRaw('FIELD(tp.tyre_brand_id, ' . implode(',', $tyreBrandIds) . ')');
        }

        if ($request->filled('load_index') && $request->input('load_index') !== '') {
            $query->where('tyre_loadIndex', $request->input('load_index'));
        }

        if ($request->filled('speed_index') && $request->input('speed_index') !== '') {
            $query->where('tyre_speed', $request->input('speed_index'));
        }

        if ($request->filled('vehicle_type') && $request->input('vehicle_type') !== '') {
            $query->where('vehicle_type', $request->input('vehicle_type'));
        }

        if ($request->filled('noiseLevel')) {
            $noiseLevels = $request->input('noiseLevel');
            $query->whereIn('tyre_noisedb', $noiseLevels);
        }

        if ($distanceMiles !== null && $distanceMiles > $getMiles) {
            $query->where('tyre_supplier_name', '!=', 'ownstock');
        }

        $recommendedTyres = [];
        if ($request->filled('width') || $request->filled('profile') || $request->filled('diameter')) {
            $recommendedTyres = $this->getRecommendedTyres($request);
        }

        $query->orderByRaw("CASE WHEN tyre_supplier_name = 'ownstock' THEN 1 ELSE 2 END")
            ->orderBy('tyre_fullyfitted_price', 'asc');

        $tyres = $query->paginate(72, ['*'], 'page', $page)->appends($request->except('page'));

        $leadTimes = [];
        $order_type = $fitting_type ?? $request->input('fitting_type');
        foreach ($tyres as $tyre) {
            if ($tyre->supplier_id) {
                $supplierLeadTime = $this->getLeadTime($tyre->supplier_id, $order_type);
                $leadTimes[$tyre->product_id] = $supplierLeadTime[$tyre->supplier_id] ?? null;
            }
        }

        try {
            $tyresHtml = view('tyre-cards', compact('tyres','leadTimes'))->render();
            $hasMorePages = $tyres->hasMorePages();
            
            $recommendedTyresHtml = $page === 1
                ? view('recommended-tyres', compact('recommendedTyres'))->render()
                : '';

        } catch (\Exception $e) {
            \Log::error('View Rendering Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to render view'], 500);
        }

        return response()->json([
            'tyres' => $tyresHtml,
            'has_more_pages' => $hasMorePages,
            'recommendedTyresMessage' => $message,
            'recommendedTyres' => $recommendedTyresHtml,
        ]);
    }

    public function getRecommendedTyres(Request $request, $width = null, $profile = null, $diameter = null)
    {
        if ($width && $profile && $diameter) {
            $request->merge([
                'width' => $width,
                'profile' => $profile,
                'diameter' => $diameter,
            ]);
        }
        $postcode = $postcode ?? $request->input('postcode') ?? session('user_postcode');
            $origin = getGarageDetails()->zone;
            $destination = $postcode ? $postcode . ', UK' : null;
            $getMiles = get_option('get_miles_for_tyres');

            $distanceMiles = $postcode
                ? $this->distanceService->getDistanceMiles($origin, $postcode)
                : null;

        $recommendedBrands = tyre_brands::where('recommended_tyre', 1)
            ->pluck('brand_id')
            ->toArray();
        $query = collect();

        // Loop through each recommended brand and get the best tyre
        $tyresModel = new \App\Models\TyresProduct();
        $brandsModel = new \App\Models\tyre_brands();

        $tyresTable = $tyresModel->getTable();
        $brandsTable = $brandsModel->getTable();
        $connection = $tyresModel->getConnectionName();

        $db = \DB::connection($connection);
        $query = collect();
        $activeSupplierIds = Supplier::where('website_display_status', 1)->where('status', 1)->pluck('id');

        foreach ($recommendedBrands as $brandId) {
            // Try to get cheapest ownstock tyre
            $ownstockTyre = $db->table("$tyresTable as tp")
                ->join("$brandsTable as tb", 'tb.brand_id', '=', 'tp.tyre_brand_id')
                ->select('tp.*', 'tb.name as brand_name', 'tb.image as brand_logo', 'tb.budget_type', 'tb.recommended_tyre')
                ->where('tp.tyre_supplier_name', 'ownstock')
                ->whereIn('tp.supplier_id', $activeSupplierIds)
                ->where('tp.tyre_price', '>', 0)
                ->where('tp.tyre_quantity', '>', 0)
                ->where('tp.status', '=', 1)
                ->where('tb.brand_id', $brandId)
                ->where('tb.recommended_tyre', 1);

            // Apply dynamic filters
            if ($request->filled('width')) {
                $ownstockTyre->where('tp.tyre_width', $request->input('width'));
            }
            if ($request->filled('profile')) {
                $ownstockTyre->where('tp.tyre_profile', $request->input('profile'));
            }
            if ($request->filled('diameter')) {
                $ownstockTyre->where('tp.tyre_diameter', $request->input('diameter'));
            }

            $ownstockTyre = $ownstockTyre->orderBy('tp.tyre_price', 'asc')->first();

            if ($ownstockTyre) {
                $query->push($ownstockTyre);
                continue;
            }

            // Fallback to cheapest supplier tyre
            $supplierTyre = $db->table("$tyresTable as tp")
                ->join("$brandsTable as tb", 'tb.brand_id', '=', 'tp.tyre_brand_id')
                ->select('tp.*', 'tb.name as brand_name', 'tb.image as brand_logo', 'tb.budget_type', 'tb.recommended_tyre')
                ->where('tp.tyre_supplier_name', '!=', 'ownstock')
                ->whereIn('tp.supplier_id', $activeSupplierIds)
                ->where('tp.tyre_price', '>', 0)
                ->where('tp.tyre_quantity', '>', 0)
                ->where('tp.status', '=', 1)
                ->where('tb.brand_id', $brandId)
                ->where('tb.recommended_tyre', 1);

            // Apply dynamic filters
            if ($request->filled('width')) {
                $supplierTyre->where('tp.tyre_width', $request->input('width'));
            }
            if ($request->filled('profile')) {
                $supplierTyre->where('tp.tyre_profile', $request->input('profile'));
            }
            if ($request->filled('diameter')) {
                $supplierTyre->where('tp.tyre_diameter', $request->input('diameter'));
            }
            if ($request->filled('load_index')) {
                $supplierTyre->where('tyre_loadIndex', $request->load_index);
            }
            if ($request->filled('speed_index')) {
                $supplierTyre->where('tyre_speed', $request->speed_index);
            }
            if ($request->filled('vehicle_type')) {
                $supplierTyre->where('vehicle_type', $request->vehicle_type);
            }

            if ($distanceMiles !== null && $distanceMiles > $getMiles) {
            $supplierTyre->where('tp.tyre_supplier_name', '!=', 'ownstock');
            }

            $supplierTyre = $supplierTyre->orderBy('tp.tyre_price', 'asc')->first();

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
        $tyreId = $request->query('tyre');
        if (!$tyreId) {
            abort(404);
        }
        $tyre = TyresProduct::with('brand')
            ->findOrFail($tyreId);

        if ($tyre->brand && strtolower($tyre->brand->slug) !== $brand) {
            abort(404);
        }

        return view('tyre-productDetails', compact('tyre', 'brand', 'model', 'size'));
    }


    public function getTyreSizes()
{
    try {
        $activeSupplierIds = Supplier::where('website_display_status', 1)->where('status', 1)->pluck('id');

        $tyreSizes = TyresProduct::select('tyre_width', 'tyre_profile', 'tyre_diameter', 'vehicle_type')
            ->where('tyre_width', '>', 0)
            ->where('tyre_quantity', '>', 0)
            ->where('tyre_fullyfitted_price', '>', 0)
            ->whereIn('supplier_id', $activeSupplierIds)
            ->where('status', 1)
            ->distinct()
            ->groupBy('tyre_width', 'tyre_profile', 'tyre_diameter', 'vehicle_type')
            ->orderBy('tyre_width')
            ->orderBy('tyre_profile')
            ->orderBy('tyre_diameter')
            ->get();
        $typeMap = [
            'car' => 'car',
            'truck' => 'truck',
            'commercial truck' => 'truck',
            'motorbike' => 'motorbike',
        ];
        $orderTypes = OrderTypes::where('status', 1)->get();
        $tyreSizesWithOrderTypes = $tyreSizes->map(function ($tyre) use ($orderTypes, $typeMap) {
            $vehicleType = strtolower(trim($tyre->vehicle_type ?? 'car'));
            $vehicleType = $typeMap[$vehicleType] ?? 'car';
            $validOrderTypes = $orderTypes->filter(function ($orderType) use ($vehicleType) {
                return $orderType->$vehicleType == 1;
            })->pluck('ordertype_name')->values();
            $tyre->order_types = $validOrderTypes;
            return $tyre;
        });
        return response()->json($tyreSizesWithOrderTypes);
    } catch (\Exception $e) {
        \Log::error("Tyre Sizes Fetch Failed", ['error' => $e->getMessage()]);
        return response()->json([], 500);
    }
}




}

