<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TyresProduct;
use App\Models\Supplier; // Assuming you have a Supplier model
use App\Models\OrderTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TyresController extends Controller
{
    public function search(Request $request)
    {
        // Fetch distinct suppliers
        $suppliers = DB::table('tyres_product')
            ->distinct()
            ->pluck('tyre_supplier_name');
    
        // Start the query for tyres
        $query = TyresProduct::query();
    
        // Join with the tyres_brands table to fetch the brand name
        $query->leftJoin('tyre_brands', 'tyres_product.tyre_brand_id', '=', 'tyre_brands.brand_id')
            ->select(
                'tyres_product.*', // Select all columns from tyres_product
                'tyre_brands.name as brand_name' // Alias the brand name as "brand_name"
            );
    
        // Filter by EAN
        if ($request->filled('tyre_ean')) {
            $query->where('tyre_ean', 'like', '%' . $request->tyre_ean . '%');
        }
    
        // Filter by SKU
        if ($request->filled('tyre_sku')) {
            $query->where('tyre_sku', 'like', '%' . $request->tyre_sku . '%');
        }
    
        // Filter by Brand Name (using brand_id)
        if ($request->filled('tyre_brand_name')) {
            $query->where('tyres_product.tyre_brand_id', $request->tyre_brand_name);
        }
    
        // Filter by Tyre Source
        if ($request->filled('tyre_supplier_name')) {
            $query->where('tyre_supplier_name', $request->tyre_supplier_name);
        }
    
        // New Filters for Width, Profile, Diameter
        if ($request->filled('tyre_width')) {
            $query->where('tyre_width', 'like', '%' . $request->tyre_width . '%');
        }
        if ($request->filled('tyre_profile')) {
            $query->where('tyre_profile', 'like', '%' . $request->tyre_profile . '%');
        }
        if ($request->filled('tyre_diameter')) {
            $query->where('tyre_diameter', 'like', '%' . $request->tyre_diameter . '%');
        }
    
        // Tyre Type Filter (Budget, Midrange, Premium)
        if ($request->filled('tyre_type')) {
            $query->where('tyre_brands.budget_type', $request->tyre_type);
        }
    
        // Season Type Filter
        if ($request->filled('season_type')) {
            $query->where('tyres_product.tyre_season', 'like', '%' . $request->season_type . '%');
        }
    
        // Vehicle Type Filter
        if ($request->filled('vehicle_type')) {
            $query->where('tyres_product.vehicle_type', 'like', '%' . $request->vehicle_type . '%');
        }
    
        // RFT (Run-Flat) Filter
        if ($request->has('rft')) {
            $query->where('tyres_product.tyre_runflat', 1);
        }
    
        // Stock Status Filter
        $stockStatus = $request->input('stock_status', 'instock');
        switch ($stockStatus) {
            case 'instock':
                $query->where('instock', 1)->where('tyre_quantity', '>', 0);
                break;
            case 'available':
                $query->where('tyre_quantity', '>', 0);
                break;
            case 'all':
                // No filtering for "All" stock status
                break;
            default:
                $query->where('instock', 1)->where('tyre_quantity', '>', 0);
                break;
        }
    
        // Sorting logic
        $sortableColumns = ['tyre_ean', 'tyre_description', 'tyre_brand_name', 'tyre_fuel', 'tyre_wetgrip', 'tyre_noisedb', 'vehicle_type', 'tyre_quantity', 'tyre_price', 'tyre_fullyfitted_price', 'trade_costprice', 'tyre_supplier_name', 'lead_time'];
        $sortBy = $request->input('sort_by', 'tyre_price'); // Default sort by price
        $order = $request->input('order', 'asc'); // Default order is ascending
    
        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $order);
    
            // Apply quantity filter only if stock status is not "All"
            if ($stockStatus !== 'all') {
                $query->where('tyre_quantity', '>', 0);
            }
        } else {
            $query->orderBy('tyre_price', 'asc'); // Fallback to default sorting
        }
    
        // Fetch brands, tyre types, season types, and vehicle types for the frontend
        $brands = DB::table('tyre_brands')->select('brand_id', 'name')->orderBy('name', 'asc')->get();
        $tyreTypes = DB::table('tyre_brands')
            ->whereNotNull('budget_type')
            ->where('budget_type', '!=', '')
            ->distinct()
            ->pluck('budget_type');
    
        $seasonTypes = DB::table('tyres_product')
            ->whereNotNull('tyre_season')
            ->where('tyre_season', '!=', '')
            ->distinct()
            ->pluck('tyre_season');
    
        $vehicleTypes = DB::table('tyres_product')
            ->whereNotNull('vehicle_type')
            ->where('vehicle_type', '!=', '')
            ->distinct()
            ->pluck('vehicle_type');
    
        // Get filtered tyres with pagination
        $tyres = $query->paginate(12)->appends($request->except('page'));
    
        // Pass the request query parameters to the view for restoring filters
        $filters = $request->all();
    
        return view('AutoCare.tyres.search', compact('tyres', 'brands', 'tyreTypes', 'sortBy', 'order', 'seasonTypes', 'vehicleTypes', 'suppliers', 'filters'));
    }

public function getLeadTime($supplierName = '')
{
    $leadtimeArray = [];

    // Get current date & time details
    $now = Carbon::now();
    $currentTime = $now->format('H:i');
    $currentDay = strtolower($now->format('l'));
    $currentDate = $now->format('Y-m-d');

    // Fetch delivery times where supplier matches (if given), else fetch all
    $query = DB::table('deliverytime');

    if (!empty($supplierName)) {
        $query->where('supplier', $supplierName);
    }

    $deliveryTimes = $query->get();

    foreach ($deliveryTimes as $data) {
        $weekDay = strtolower($data->day);
        $deliveryTimeHours = (int) $data->delivery_time;

        $startTime = Carbon::createFromTime($data->start_time, $data->start_mnt);
        $endTime = Carbon::createFromTime($data->end_time, $data->end_mnt);

        if ($currentDay === $weekDay) {
            if ($now->between($startTime, $endTime)) {
                $endTimeWithDate = Carbon::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $endTime->format('H:i:s'));
                $deliveryDateTime = $endTimeWithDate->copy()->addHours($deliveryTimeHours);
                $daysBetween = $now->diffInDays($deliveryDateTime);

                $key = $supplierName ?: $data->supplier;
                $supplierCode = $data->supplier_code;

                if ($daysBetween === 1) {
                    $leadtimeArray[$key] = __('text_available_tomorrow');
                    $leadtimeArray[$supplierCode] = $deliveryTimeHours;
                } elseif ($currentDate === $deliveryDateTime->format('Y-m-d')) {
                    $leadtimeArray[$key] = __('text_available_today');
                    $leadtimeArray[$supplierCode] = $deliveryTimeHours;
                } else {
                    $leadtimeArray[$key] = __('text_available_from') . ' ' . $deliveryDateTime->format('M jS');
                    $leadtimeArray[$supplierCode] = $deliveryTimeHours;
                }
            }
        }
    }

    return $leadtimeArray;
}



    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'tyre_sku' => 'required|string|max:64',
                'tyre_price' => 'required|numeric|min:0',
                'tyre_description' => 'nullable|string',
                'tyre_model' => 'nullable|string|max:64',
                'tyre_quantity' => 'required|integer|min:0',
                'tyre_image' => 'nullable|string|max:255',
                'tyre_width' => 'nullable|string|max:4',
                'tyre_profile' => 'nullable|string|max:3',
                'tyre_diameter' => 'nullable|string|max:2',
                'tyre_season' => 'nullable|string|max:15',
                'tax_class_id' => 'required|in:0,9', // Ensure it is either 0 (No VAT) or 9 (VAT)
                'tyre_brand_id' => 'required|integer',
            ]);

            // Fetch the brand name from the tyre_brands table using brand_id
            $brandName = DB::table('tyre_brands')->where('brand_id', $request->tyre_brand_id)->value('name');
            $supplierName = DB::table('suppliers')
            ->where('id', $request->supplier_id)
            ->value('supplier_name');
            // Create the TyresProduct record
            TyresProduct::create([
                'tyre_ean' => $request->tyre_ean,
                'tyre_sku' => $request->tyre_sku,
                'tyre_price' => $request->tyre_price,
                'tyre_description' => $request->tyre_season . ' Tyre '
                    . $brandName . ' ' // Use the fetched brand name here
                    . $request->tyre_model . ' '
                    . $request->tyre_width . '/'
                    . $request->tyre_profile . 'R'
                    . $request->tyre_diameter . ' '
                    . $request->tyre_loadindex . ' '
                    . $request->tyre_speed . ' '
                    . ($request->tyre_reinforced == 1 ? 'XL ' : '') // Include XL if reinforced
                    . ($request->tyre_runflat == 1 ? 'RFT' : ''),  // Include RFT if runflat
                'tyre_model' => $request->tyre_model,
                'tyre_quantity' => $request->tyre_quantity,
                'tyre_image' => $request->tyre_image,
                'tyre_width' => $request->tyre_width,
                'tyre_profile' => $request->tyre_profile,
                'tyre_diameter' => $request->tyre_diameter,
                'tyre_season' => $request->tyre_season,
                'tyre_noisedb' => $request->tyre_noisedb,
                'tyre_speed' => $request->tyre_speed,
                'tyre_wetgrip' => $request->tyre_wetgrip,
                'tyre_loadindex' => $request->tyre_loadindex,
                'tyre_runflat' => $request->tyre_runflat,
                'tyre_extraload' => $request->tyre_extraload,
                'tyre_supplier_name' => $supplierName,
                'instock' => $request->instock,
                'supplier_id' => (strtolower($request->tyre_source) == 'ownstock' ? 1 : $request->supplier_id),
                'tax_class_id' => $request->tax_class_id,
                'tyre_brand_id' => $request->tyre_brand_id,
                'tyre_brand_name' => $brandName,
                'tyre_margin' => $request->tyre_margin,
                'status' => $request->status,
                'tyre_fuel' => $request->tyre_fuel,
                'vehicle_type' => ucwords(strtolower($request->vehicle_type)),
                'product_type' => $request->product_type ?? 'tyre',
                // 'price_collection' => $request->price_collection,
                'tyre_fullyfitted_price' => $request->tyre_fullyfitted_price,
                'tyre_mailorder_price' => $request->tyre_mailorder_price,
                'tyre_mobilefitted_price' => $request->tyre_mobilefitted_price,
                'tyre_collection_price' => $request->tyre_collection_price,
                'lead_time' => $request->lead_time,
                'trade_costprice' => $request->trade_costprice,
            ]);
            // Redirect with success message
            return redirect()->route('AutoCare.tyres.search')->with('success', 'Tyre added successfully!');
        } catch (\Exception $e) {
            // Log::error("Tyre update failed: " . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $product_id)
    {
        try {
            $request->validate([

                'tyre_sku' => 'required|string|max:64',
                'tyre_price' => 'required|numeric|min:0',
                'tyre_description' => 'nullable|string',
                'tyre_model' => 'nullable|string|max:64',
                'tyre_quantity' => 'nullable|integer|min:0',
                'tyre_image' => 'nullable|string|max:255',
                'tyre_width' => 'nullable|string|max:4',
                'tyre_profile' => 'nullable|string|max:3',
                'tyre_diameter' => 'nullable|string|max:2',
                'tyre_season' => 'nullable|string|max:15',
                'tax_class_id' => 'required|in:0,9',
                'tyre_brand_id' => 'required|integer',
                'product_type' => 'nullable|string|max:15',
                // Other validations here
            ]);

            // Fetch the Tyre product
            $tyre = TyresProduct::where('product_id', $product_id)->firstOrFail();

            // Fetch the brand name from the tyre_brands table using brand_id
            $brandName = DB::table('tyre_brands')->where('brand_id', $request->tyre_brand_id)->value('name');
            $supplierName = DB::table('suppliers')
            ->where('id', $request->supplier_id)
            ->value('supplier_name');
            if ($request->stock_type === 'Increase') {
                $quantity = $request->tyre_quantity + $tyre->tyre_quantity;
            } elseif ($request->stock_type === 'Decrease') {
                $quantity = $tyre->tyre_quantity - $request->tyre_quantity;
            } else {
                $quantity = $tyre->tyre_quantity; // Default to current stock quantity
            }
            // dd($request->product_type);
            // Update the Tyre product
            $tyre->update([
                'tyre_ean' => $request->tyre_ean,
                'tyre_sku' => $request->tyre_sku,
                'tyre_price' => $request->tyre_price,
                'product_type' => $request->product_type ?? 'tyre',
                'tyre_description' => $request->tyre_season . ' Tyre '
                    . $brandName . ' ' // Use the fetched brand name here
                    . $request->tyre_model . ' '
                    . $request->tyre_width . '/'
                    . $request->tyre_profile . 'R'
                    . $request->tyre_diameter . ' '
                    . $request->tyre_loadindex . ' '
                    . $request->tyre_speed . ' '
                    . ($request->tyre_extraload == 1 ? 'XL ' : '') // Include XL if reinforced
                    . ($request->tyre_runflat == 1 ? 'RFT' : ''),  // Include RFT if runflat
                'tyre_model' => $request->tyre_model,
               // Check the value of stock_Type and perform addition or subtraction accordingly
                'tyre_quantity' => $quantity,
                'tyre_image' => $request->tyre_image,
                'tyre_width' => $request->tyre_width,
                'tyre_profile' => $request->tyre_profile,
                'tyre_diameter' => $request->tyre_diameter,
                'tyre_season' => $request->tyre_season,
                'tyre_noisedb' => $request->tyre_noisedb,
                'tyre_speed' => $request->tyre_speed,
                'tyre_wetgrip' => $request->tyre_wetgrip,
                'tyre_loadindex' => $request->tyre_loadindex,
                'tyre_runflat' => $request->tyre_runflat,
                'tyre_extraload' => $request->tyre_extraload,
                'tyre_supplier_name' => $supplierName,
                'instock' => $request->instock,
                'supplier_id' =>$request->supplier_id,
                'tax_class_id' => $request->tax_class_id,
                'tyre_brand_id' => $request->tyre_brand_id,
                'tyre_brand_name' => $brandName,
                'tyre_margin' => $request->tyre_margin,
                'status' => $request->status,
                'tyre_fuel' => $request->tyre_fuel,
                'vehicle_type' => ucwords(strtolower($request->vehicle_type)),
                // 'price_collection' => $request->price_collection,
                'tyre_fullyfitted_price' => $request->tyre_fullyfitted_price,
                'tyre_mailorder_price' => $request->tyre_mailorder_price,
                'tyre_mobilefitted_price' => $request->tyre_mobilefitted_price,
                'tyre_collection_price' => $request->tyre_collection_price,
                'lead_time' => $request->lead_time,
                'trade_costprice' => $request->trade_costprice,
            ]);
            if ($request->filled('tyre_quantity') && $supplierName === 'ownstock') {
                // Prepare data for stock_history table
                $stockHistoryData = [
                    'product_id' => $product_id,
                    'product_type' => 'tyre',
                    'sku' => $request->tyre_sku,
                    'ean' => $request->tyre_ean,
                    'supplier' => $supplierName,
                    'qty' => $request->tyre_quantity,
                    'cost_price' => $request->tyre_price,
                    'stock_type' => $request->stock_type,
                    'reason' => $request->reason,
                    'other_reason' => $request->other_reason,
                    'ref_id' => $request->ref_id,
                    'ref_type' => $request->ref_type ?? '',
                    'stock_date' => now()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
    
                // Insert data into the stock_history table
                DB::table('stock_history')->insert($stockHistoryData);
            }
            $queryParams = request()->query();
            // dd($queryParams);
        // Redirect back to the search results page with the query parameters
        return redirect()->route('AutoCare.tyres.search', $queryParams)->with('success', 'Tyre updated successfully!');
    
        } catch (\Exception $e) {
            // Log::error("Tyre update failed: " . $e->getMessage());
            return back()->withErrors(['error' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        // Fetch all brands from the tyre_brands table
        $brands = DB::table('tyre_brands')->get();

        return view('AutoCare.tyres.create', compact('brands'));
    }

    public function edit($product_id = null)
    {
        if ($product_id && $product_id != 'new') {
            $tyre = TyresProduct::where('product_id', $product_id)->firstOrFail();
            $brands = DB::table('tyre_brands')->get();
            $suppliers = Supplier::where('status', 1)->get();
            $queryParams = request()->query();
            // dd($queryParams);
            return view('AutoCare.tyres.edit', compact('tyre', 'brands', 'suppliers','queryParams'));
        }
    
        // If no product_id is provided, we are adding a new tyre
        $brands = DB::table('tyre_brands')->get();
        $suppliers = Supplier::where('status', 1)->get();
        return view('AutoCare.tyres.edit', compact('brands','suppliers'));
    }

    // Method to delete a tyre
    public function destroy($product_id)
    {
        $tyre = TyresProduct::where('product_id', $product_id)->firstOrFail();
        $tyre->delete(); // Delete tyre record

        return redirect()->route('AutoCare.tyres.search')->with('success', 'Tyre deleted successfully!');
    }
    public function getTyreProducts(Request $request)
    {
        // dd($request);
        // Start the query
        $query = DB::table('tyres_product as tp')
            ->leftJoin('tyre_brands as tb', 'tp.tyre_brand_id', '=', 'tb.brand_id')
            ->select('tp.*', 'tb.name as brand_name')
            ->where('tp.tyre_quantity', '>', 0);

        // Apply filters
        if ($request->tyre_ean) {
            $query->where('tp.tyre_ean', 'LIKE', "%{$request->tyre_ean}%");
        }
        if ($request->tyre_width) {
            $query->where('tp.tyre_width', 'LIKE', "%{$request->tyre_width}%");
        }
        if ($request->tyre_profile) {
            $query->where('tp.tyre_profile', 'LIKE', "%{$request->tyre_profile}%");
        }
        if ($request->tyre_diameter) {
            $query->where('tp.tyre_diameter', 'LIKE', "%{$request->tyre_diameter}%");
        }
        if ($request->tyre_brand_name) {
            $query->where('tp.tyre_brand_name', 'LIKE', "%{$request->tyre_brand_name}%");
        }
        if ($request->tyre_supplier_name) {
            $query->where('tp.tyre_supplier_name', 'LIKE', "%{$request->tyre_supplier_name}%");
        }
        if ($request->tyre_runflat) {
            $query->where('tp.tyre_runflat', 'LIKE', "%{$request->tyre_runflat}%");
        }

        // Determine the price field based on fittingtype
        $fittingType = $request->fittingtype;
        $priceField = 'tyre_fullyfitted_price'; // Default price field
        if ($fittingType === 'fully_fitted') {
            $priceField = 'tyre_fullyfitted_price';
        } elseif ($fittingType === 'mailorder') {
            $priceField = 'tyre_mailorder_price';
        } elseif ($fittingType === 'mobile_fitted') {
            $priceField = 'tyre_mobilefitted_price';
        } elseif ($fittingType === 'collection_price') {
            $priceField = 'tyre_collection_price';
        } elseif ($fittingType === 'trade_customer_price') {
            $priceField = 'trade_costprice';
        }

        // Add the selected price field to the query
        $query->addSelect(DB::raw("tp.$priceField as selected_price"));

        // Order by the selected price field
        $query->orderBy("tp.$priceField", 'asc');

        // Paginate results
        $tyreProducts = $query->paginate(25)->appends($request->except('page'));
        // dd($tyreProducts);
        return response()->json(['tyre_products' => $tyreProducts]);
    }

    /**
     * Fetch all suppliers for the dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuppliers()
    {
        try {
            // Fetch all suppliers from the database
            $suppliers = Supplier::select('id', 'supplier_name')->where('status', 1)->get();
            // dd($suppliers);
            // Return the suppliers as a JSON response
            return response()->json([
                'success' => true,
                'suppliers' => $suppliers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching suppliers: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getOrderType()
    {
        try {
            // Fetch all suppliers from the database
            $suppliers = OrderTypes::select('id', 'ordertype_name')->where('status', '!=', '0')->get();
            // dd($suppliers);
            // Return the suppliers as a JSON response
            return response()->json([
                'success' => true,
                'ordertype_name' => $suppliers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching suppliers: ' . $e->getMessage(),
            ], 500);
        }
    }


}
