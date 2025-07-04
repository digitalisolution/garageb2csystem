<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Models\TyresProduct;
use App\Models\tyre_brands;
use App\Models\Supplier; // Assuming you have a Supplier model
use App\Models\OrderTypes;
use Illuminate\Support\Facades\Log;

class TyresController extends Controller
{
  public function search(Request $request)
{
    // Resolve dynamic table names
    $tyresTable = (new \App\Models\TyresProduct())->getTable();
    $brandsTable = (new \App\Models\tyre_brands())->getTable();

    // Fetch distinct suppliers
    $suppliers = \App\Models\TyresProduct::from($tyresTable)->distinct()->pluck('tyre_supplier_name');

    // Start the query for tyres
    $query = \App\Models\TyresProduct::from("$tyresTable as tyres_product")
        ->leftJoin("$brandsTable as tyre_brands", 'tyres_product.tyre_brand_id', '=', 'tyre_brands.brand_id')
        ->select('tyres_product.*', 'tyre_brands.name as brand_name');

    // Filters
    if ($request->filled('tyre_ean')) {
        $query->where('tyres_product.tyre_ean', 'like', '%' . $request->tyre_ean . '%');
    }
    if ($request->filled('tyre_sku')) {
        $query->where('tyres_product.tyre_sku', 'like', '%' . $request->tyre_sku . '%');
    }
    if ($request->filled('tyre_brand_name')) {
        $query->where('tyres_product.tyre_brand_id', $request->tyre_brand_name);
    }
    if ($request->filled('tyre_supplier_name')) {
        $query->where('tyres_product.tyre_supplier_name', $request->tyre_supplier_name);
    }
    if ($request->filled('width')) {
        $query->where('tyres_product.tyre_width', 'like', '%' . $request->width . '%');
    }
    if ($request->filled('profile')) {
        $query->where('tyres_product.tyre_profile', 'like', '%' . $request->profile . '%');
    }
    if ($request->filled('diameter')) {
        $query->where('tyres_product.tyre_diameter', 'like', '%' . $request->diameter . '%');
    }
    if ($request->filled('tyre_season')) {
        $query->where('tyre_brands.budget_type', $request->tyre_season);
    }
    if ($request->filled('season_type')) {
        $query->where('tyres_product.tyre_season', 'like', '%' . $request->season_type . '%');
    }
    if ($request->filled('vehicle_type')) {
        $query->where('tyres_product.vehicle_type', 'like', '%' . $request->vehicle_type . '%');
    }
    if ($request->has('rft')) {
        $query->where('tyres_product.tyre_runflat', 1);
    }

    // Stock Status
    $stockStatus = $request->input('stock_status', 'instock');
    switch ($stockStatus) {
        case 'instock':
            $query->where('tyres_product.instock', 1)->where('tyres_product.tyre_quantity', '>', 0);
            break;
        case 'available':
            $query->where('tyres_product.tyre_quantity', '>', 0);
            break;
        case 'all':
            // no filter
            break;
        default:
            $query->where('tyres_product.instock', 1)->where('tyres_product.tyre_quantity', '>', 0);
            break;
    }

    // Sorting
    $sortableColumns = ['tyre_ean', 'tyre_description', 'brand_name', 'tyre_fuel', 'tyre_wetgrip', 'tyre_noisedb', 'vehicle_type', 'tyre_quantity', 'tyre_price', 'tyre_fullyfitted_price', 'trade_costprice', 'tyre_supplier_name', 'lead_time'];
    $sortBy = $request->input('sort_by', 'tyre_price');
    $order = $request->input('order', 'asc');

    if (in_array($sortBy, $sortableColumns)) {
        $query->orderBy("tyres_product.$sortBy", $order);

        // Apply quantity check again if not 'all'
        if ($stockStatus !== 'all') {
            $query->where('tyres_product.tyre_quantity', '>', 0);
        }
    } else {
        $query->orderBy('tyres_product.tyre_price', 'asc');
    }

    // Fetch filters for the frontend
    $brands = \App\Models\tyre_brands::from($brandsTable)
        ->select('brand_id', 'name')
        ->orderBy('name', 'asc')
        ->get();

    $tyreTypes = \App\Models\tyre_brands::from($brandsTable)
        ->whereNotNull('budget_type')
        ->where('budget_type', '!=', '')
        ->distinct()
        ->pluck('budget_type');

    $seasonTypes = \App\Models\TyresProduct::from($tyresTable)
        ->whereNotNull('tyre_season')
        ->where('tyre_season', '!=', '')
        ->distinct()
        ->pluck('tyre_season');

    $vehicleTypes = \App\Models\TyresProduct::from($tyresTable)
        ->whereNotNull('vehicle_type')
        ->where('vehicle_type', '!=', '')
        ->distinct()
        ->pluck('vehicle_type');

        // Clone query to calculate totals from ALL matching OWNSTOCK tyres with quantity > 0
            $ownstockTotals = (clone $query)
            ->where('tyres_product.tyre_supplier_name', 'OWNSTOCK')
            ->where('tyres_product.tyre_quantity', '>', 0)
            ->get();
           /* $ownstockTotals = \App\Models\TyresProduct::from("$tyresTable as tyres_product")
            ->where('tyres_product.tyre_supplier_name', 'OWNSTOCK')
            ->where('tyres_product.tyre_quantity', '>', 0)
            ->get();*/


    // Paginate and return
    $tyres = $query->paginate(12)->appends($request->except('page'));

    // Totals
        $totalQty = $ownstockTotals->sum('tyre_quantity');
        $totalCost = $ownstockTotals->sum(function ($item) {
                //\Log::info("{$item->tyre_description} | Qty: {$item->tyre_quantity} | Cost: {$item->tyre_price}");
            return $item->tyre_price;
        });

    $filters = $request->all();

    //return view('AutoCare.tyres.search', compact('tyres', 'brands', 'tyreTypes', 'sortBy', 'order', 'seasonTypes', 'vehicleTypes', 'suppliers', 'filters'));
    return view('AutoCare.tyres.search', compact('tyres', 'brands', 'tyreTypes', 'sortBy', 'order','seasonTypes', 'vehicleTypes', 'suppliers', 'filters','totalQty', 'totalCost'));
}


    public function store(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'tyre_ean' => 'required|string|max:64',
                'tyre_sku' => 'required|string|max:64',
                'tyre_price' => 'required|numeric|min:0',
                'tyre_description' => 'nullable|string',
                'tyre_model' => 'nullable|string|max:64',
                'tyre_quantity' => 'required|integer|min:0',
                'tyre_image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'tyre_width' => 'nullable|string|max:4',
                'tyre_profile' => 'nullable|string|max:3',
                'tyre_diameter' => 'nullable|string|max:4',
                'tyre_season' => 'nullable|string|max:15',
                'tax_class_id' => 'required|in:0,9', // Ensure it is either 0 (No VAT) or 9 (VAT)
                'tyre_brand_id' => 'required|integer',
            ]);

            if ($request->hasFile('tyre_image')) {
                $image = $request->file('tyre_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('frontend/themes/img/tyre_images'), $imageName);
                $tyre_image = $imageName;
            }

            // Fetch the brand name from the tyre_brands table using brand_id
            $brandName = tyre_brands::where('brand_id', $request->tyre_brand_id)->value('name');
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
                    . ($request->tyre_extraload == 1 ? 'XL ' : '') // Include XL if reinforced
                    . ($request->tyre_runflat == 1 ? 'RFT' : ''),  // Include RFT if antiflat
                'tyre_model' => $request->tyre_model,
                'tyre_quantity' => $request->tyre_quantity,
                'tyre_image' => isset($tyre_image) ? basename($tyre_image) : null,
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
                'supplier_id' => (strtolower($request->tyre_supplier_name) == 'ownstock' ? 1 : $request->supplier_id),
                'tax_class_id' => $request->tax_class_id,
                'tyre_brand_id' => $request->tyre_brand_id,
                'tyre_brand_name' => $brandName,
                'tyre_margin' => $request->tyre_margin,
                'status' => $request->status,
                'tyre_fuel' => $request->tyre_fuel,
                'vehicle_type' => ucwords(strtolower($request->vehicle_type)),
                'product_type' => $request->product_type ?? 'tyre',
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

                'tyre_ean' => 'required|string|max:64',
                'tyre_sku' => 'required|string|max:64',
                'tyre_price' => 'required|numeric|min:0',
                'tyre_description' => 'nullable|string',
                'tyre_model' => 'nullable|string|max:64',
                'tyre_quantity' => 'nullable|integer|min:0',
                'tyre_image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'tyre_width' => 'nullable|string|max:4',
                'tyre_profile' => 'nullable|string|max:3',
                'tyre_diameter' => 'nullable|string|max:4',
                'tyre_season' => 'nullable|string|max:15',
                'tax_class_id' => 'required|in:0,9',
                'tyre_brand_id' => 'required|integer',
            ]);

            if ($request->hasFile('tyre_image')) {
                $image = $request->file('tyre_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('frontend/themes/img/tyre_images'), $imageName);
                $tyre_image = $imageName;
            }
        

            $tyre = TyresProduct::where('product_id', $product_id)->firstOrFail();
                        // Only update image if uploaded
            /*if ($imageName) {
                $tyre->tyre_image = $imageName;
            }*/
            $brandName = tyre_brands::where('brand_id', $request->tyre_brand_id)->value('name');
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

            $tyre->update([
                'tyre_ean' => $request->tyre_ean,
                'tyre_sku' => $request->tyre_sku,
                'tyre_price' => $request->tyre_price,
                'tyre_description' => $request->tyre_season . ' Tyre '
                    . $brandName . ' '
                    . $request->model . ' '
                    . $request->tyre_width . '/'
                    . $request->tyre_profile . 'R'
                    . $request->tyre_diameter . ' '
                    . $request->tyre_loadindex . ' '
                    . $request->tyre_speed . ' '
                    . ($request->tyre_extraload == 1 ? 'XL ' : '')
                    . ($request->tyre_runflat == 1 ? 'RFT' : ''),
                'tyre_model' => $request->tyre_model,
                'tyre_quantity' => $quantity,
                //'tyre_image' => $request->tyre_image,
                'tyre_image' => isset($tyre_image) ? basename($tyre_image) : null,
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
                'product_type' => $request->product_type ?? 'tyre',
                'tyre_fullyfitted_price' => $request->tyre_fullyfitted_price,
                'tyre_mailorder_price' => $request->tyre_mailorder_price,
                'tyre_mobilefitted_price' => $request->tyre_mobilefitted_price,
                'tyre_collection_price' => $request->tyre_collection_price,
                'lead_time' => $request->lead_time,
                'trade_costprice' => $request->trade_costprice,
            ]);
            if ($request->filled('tyre_quantity') && $supplierName === 'ownstock') {
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
                DB::table('stock_history')->insert($stockHistoryData);
            }
            $queryParams = request()->query();
        return redirect()->route('AutoCare.tyres.search', $queryParams)->with('success', 'Tyre updated successfully!');
    
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Something went wrong! ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        // Fetch all brands from the tyre_brands table
        $brands = tyre_brands::where('status',1)->get();

        return view('AutoCare.tyres.create', compact('brands'));
    }

    public function edit($product_id = null)
    {
        if ($product_id && $product_id != 'new') {
            $tyre = TyresProduct::where('product_id', $product_id)->firstOrFail();
            $brands = tyre_brands::where('status',1)->get();
            $suppliers = Supplier::where('status', 1)->get();
            $queryParams = request()->query();
            // dd($queryParams);
            return view('AutoCare.tyres.edit', compact('tyre', 'brands', 'suppliers','queryParams'));
        }
    
        // If no product_id is provided, we are adding a new tyre
        $brands = tyre_brands::where('status',1)->get();
        $suppliers = Supplier::where('status', 1)->get();
        $tyre = null;
        return view('AutoCare.tyres.edit', compact('tyre','brands','suppliers'));
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
         // Resolve dynamic model table names
        $tpTable = (new \App\Models\TyresProduct())->getTable();
            $tbTable = (new \App\Models\tyre_brands())->getTable();

        // Start the query with correct table names
        $query = \App\Models\TyresProduct::from("$tpTable as tp")
            ->leftJoin("$tbTable as tb", 'tp.tyre_brand_id', '=', 'tb.brand_id')
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

        // Determine the price field based on fitting type
        $priceFieldMap = [
            'fully_fitted'           => 'tyre_fullyfitted_price',
            'mailorder'              => 'tyre_mailorder_price',
            'mobile_fitted'          => 'tyre_mobilefitted_price',
            'collection'             => 'tyre_price_collection',
            'trade_customer_price'   => 'trade_costprice',
        ];

        $fittingType = $request->fittingtype;
        $priceField = $priceFieldMap[$fittingType] ?? 'tyre_fullyfitted_price';

        // Add the selected price field to the query
        $query->addSelect(DB::raw("tp.$priceField as selected_price"));

        // Order by the selected price field
        $query->orderBy("tp.$priceField", 'asc');

        // Paginate and return results
        $tyreProducts = $query->paginate(25)->appends($request->except('page'));

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
            $OrderTypes = OrderTypes::select('id', 'ordertype_name')->where('status', '!=', '0')->get();
            // dd($suppliers);
            // Return the suppliers as a JSON response
            return response()->json([
                'success' => true,
                'ordertype_name' => $OrderTypes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching suppliers: ' . $e->getMessage(),
            ], 500);
        }
    }


}
