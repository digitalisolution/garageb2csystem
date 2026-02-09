<?php

namespace App\Http\Controllers;

use App\Models\TyrePricing;
use App\Models\Supplier;
use App\Models\TyresProduct;
use App\Models\Garage;
use App\Models\OrderTypes;
use App\Models\HeaderLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TyrePricingController extends Controller
{
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '12')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['tyrePricings'] = TyrePricing::all();
        return view('AutoCare.pricing.manage', $viewData);
    }

    public function create()
    {
        //$suppliers = Supplier::all();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();
        $OrderTypes = OrderTypes::all();
        $viewData['garages'] = Garage::where('garage_status', 1)->get();
        $categories = ['all' => 'All', '4x4' => '4X4', 'van' => 'Van', 'winter' => 'Winter', 'runflat' => 'Run-flat'];
        $price_by_size = range(10, 24); // Example: tyre sizes 10 to 22 inches
        $priceData = [];
        $viewData['header_link'] = HeaderLink::where("menu_id", '12')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        return view('AutoCare.pricing.add',array_merge($viewData, compact('suppliers', 'OrderTypes', 'categories', 'price_by_size','priceData')));
    }

    public function store(Request $request)
    {

        // dd($request);
        // Validation: Make sure fields match the form input names
        $validated = $request->validate([
            'pricing_name' => 'required|string|max:255',
            'supplier_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !Supplier::where('id', $value)->exists()) {
                        $fail('The selected supplier is invalid.');
                    }
                },
            ],
            'order_type_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !OrderTypes::where('id', $value)->exists()) {
                        $fail('The selected order type is invalid.');
                    }
                },
            ],
            'margin_type' => 'required|string|in:prizebysize,defaultmargin',
            'default_price' => 'nullable|numeric',
            'price_by_size' => 'nullable|array',
            'product_type' => 'required|string', // Add product_type validation
            'status' => 'required|string',
        ]);



        // Encode price_by_size if it's present
        if (isset($validated['price_by_size'])) {
            $validated['price_by_size'] = json_encode($validated['price_by_size']);
        }

        // Provide a default for product_type if not set
        if (!isset($validated['product_type'])) {
            $validated['product_type'] = 'tyre'; // You can set a default value here
        }
        if ($request->sync_action == 1) {

            if ($request->status == 1) {

                $this->updateTyrePrices($validated); // Call your sync logic
            } else {
                return redirect()->route('AutoCare.pricing.manage')->with('success', 'Please active your status for save and sync pricing');
            }
        }

        // Store the data
        // Example: Store logic
        TyrePricing::create([
            'pricing_name' => $validated['pricing_name'],
            'supplier_id' => $validated['supplier_id'] == 0 ? 0 : $validated['supplier_id'],
            'order_type_id' => $validated['order_type_id'] == 0 ? 0 : $validated['order_type_id'],
            'margin_type' => $validated['margin_type'],
            'default_price' => $validated['default_price'],
            'price_by_size' => isset($validated['price_by_size']) ? json_encode($validated['price_by_size']) : null,
            'product_type' => $validated['product_type'],
            'status' => $validated['status']
        ]);


        return redirect()->route('AutoCare.pricing.manage')->with('success', 'Tyre Pricing added successfully.');
    }

    public function edit($id)
    {
        $tyrePricing = TyrePricing::findOrFail($id);
        //$suppliers = Supplier::all();
        $suppliers = Supplier::where('status', 1)->orderBy('supplier_name')->get();
        $OrderTypes = OrderTypes::all();
        $viewData['garages'] = Garage::where('garage_status', 1)->get();
        $categories = ['all' => 'All', '4x4' => '4X4', 'van' => 'Van', 'winter' => 'Winter', 'runflat' => 'Run-flat'];
        $price_by_size = range(10, 24);

        // Decode the stored price_by_size data
        // $priceData = json_decode($tyrePricing->price_by_size, true);
        $priceData = $tyrePricing->price_by_size ? json_decode($tyrePricing->price_by_size, true) : [];
         
        $viewData['header_link'] = HeaderLink::where("menu_id", '12')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        //dd($suppliers);
        return view('AutoCare.pricing.add',array_merge($viewData, compact('tyrePricing', 'suppliers', 'OrderTypes', 'categories', 'price_by_size', 'priceData')));
    }

    public function update(Request $request, $id)
    {
        $tyrePricing = TyrePricing::findOrFail($id);

        $validated = $request->validate([
            'pricing_name' => 'required|string|max:255',
            'supplier_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !Supplier::where('id', $value)->exists()) {
                        $fail('The selected supplier is invalid.');
                    }
                },
            ],
            'order_type_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !OrderTypes::where('id', $value)->exists()) {
                        $fail('The selected order type is invalid.');
                    }
                },
            ],
            'margin_type' => 'required|string|in:prizebysize,defaultmargin',
            'default_price' => 'nullable|numeric',
            'price_by_size' => 'nullable|array',
            'product_type' => 'required|string', // Add product_type validation
            'status' => 'required|string',
        ]);

        if (isset($validated['price_by_size'])) {
            $validated['price_by_size'] = json_encode($validated['price_by_size']);
        }
        if ($request->sync_action == 1) {

            if ($request->status == 1) {

                $this->updateTyrePrices($validated); // Call your sync logic
            } else {
                return redirect()->route('AutoCare.pricing.manage')->with('success', 'Please active your status for save and sync pricing');
            }
        }
        $tyrePricing->update([
            'pricing_name' => $validated['pricing_name'],
            'supplier_id' => $validated['supplier_id'] == 0 ? 0 : $validated['supplier_id'],
            'order_type_id' => $validated['order_type_id'] == 0 ? 0 : $validated['order_type_id'],
            'margin_type' => $validated['margin_type'],
            'default_price' => $validated['default_price'],
            'price_by_size' => isset($validated['price_by_size']) ? json_encode($validated['price_by_size']) : null,
            'product_type' => $validated['product_type'],
            'status' => $validated['status']
        ]);


        return redirect()->route('AutoCare.pricing.manage')->with('success', 'Tyre Pricing updated successfully.');
    }

    public function destroy($id)
    {
        $tyrePricing = TyrePricing::findOrFail($id);
        $tyrePricing->delete();

        return redirect()->route('AutoCare.pricing.manage')->with('success', 'Tyre Pricing deleted successfully.');
    }

    public function updateTyrePrices(array $data)
    {

        //$marginData = json_decode($data['price_by_size'], true);
        $marginData = is_array($data['price_by_size'])
    ? $data['price_by_size']
    : json_decode($data['price_by_size'] ?? '{}', true);
        $supplierId = $data['supplier_id'];
        $orderTypeId = $data['order_type_id'];
        $marginType = $data['margin_type'];
        $defaultMargin = $data['default_price'];
        $query = TyresProduct::query();
        if ($supplierId != 0) {
            $supplier = DB::table('suppliers')->find($supplierId);
            
            if (!$supplier) {
                return response()->json(['error' => 'Supplier not found'], 404);
            }
            $query->where('supplier_id', $supplier->id);

            // If supplier is "ownstock" (id = 1), check for instock = 1
            if (strtolower($supplier->supplier_name) == 'ownstock') {
                $query->where('instock', '=', 1);
            } else {
                $query->where('instock', '=', 0);
            }
        } else {
            $ownstockSuppliers = DB::table('suppliers')
                ->where('supplier_name', 'ownstock')
                ->pluck('id');

            $query->whereIn('supplier_id', $ownstockSuppliers)
                  ->where('instock', 1); // ownstock products

            //$query->where('supplier_id', '!=', 1);
            //$query->where('instock', '=', 0);  // For non-specified supplierId, instock should be 0
        }

        if ($orderTypeId != 0) {
            $orderType = DB::table('order_types')->find($orderTypeId);

            if (!$orderType) {
                return response()->json(['error' => 'Order type not found'], 404);
            }
            $orderTypeName = $orderType->ordertype_name;
        }

        $products = $query->get();
        foreach ($products as $product) {
            $newPrice = 0;
            $sizeMargins = []; // Initialize the margins array

            // Normalize fields for consistent comparison
            $vehicleType = strtolower($product->vehicle_type);
            $tyreType = strtolower($product->tyre_season);
            $tyreAntiflat = $product->tyre_runflat == 1 ? 'runflat' : 'not_runflat';

            if ($marginType === 'defaultmargin') {
                $newPrice = $product->tyre_price + $defaultMargin;
            }

            if ($marginType === 'prizebysize') {
                if (isset($marginData['sizes'][$product->tyre_diameter])) {
                    $sizeMargins = $marginData['sizes'][$product->tyre_diameter];
                    if (isset($sizeMargins['all'])) {
                        $newPrice = $product->tyre_price + $sizeMargins['all'];
                    }
                    if ($vehicleType === '4x4' && isset($sizeMargins['4x4'])) {
                        $newPrice = $product->tyre_price + $sizeMargins['4x4'];
                    }
                    if ($vehicleType === 'van' && isset($sizeMargins['van'])) {
                        $newPrice = $product->tyre_price + $sizeMargins['van'];
                    }
                    if ($tyreType === 'winter' && isset($sizeMargins['winter'])) {
                        $newPrice = $product->tyre_price + $sizeMargins['winter'];
                    }
                    if ($tyreAntiflat === 'runflat' && isset($sizeMargins['runflat'])) {
                        $newPrice = $product->tyre_price + $sizeMargins['runflat'];
                    }
                }
            }

            if ($orderTypeId != 0) {
                $priceColumn = null;
                switch (strtolower($orderTypeName)) {
                    case 'fullyfitted':
                        $priceColumn = 'tyre_fullyfitted_price';
                        break;
                    case 'mailorder':
                        $priceColumn = 'tyre_mailorder_price';
                        break;
                    case 'mobilefitted':
                        $priceColumn = 'tyre_mobilefitted_price';
                        break;
                    case 'collection':
                        $priceColumn = 'tyre_collection_price';
                        break;
                        case 'delivery':
                        $priceColumn = 'tyre_delivery_price';
                        break;
                    case 'trade':
                        $priceColumn = 'trade_costprice';
                        break;
                    default:
                        $priceColumn = 'tyre_fullyfitted_price'; // You can choose the default here
                        break;
                }

                if ($priceColumn) {
                    TyresProduct::where('product_id', $product->product_id)
                    ->where('supplier_id', $product->supplier_id) // match the supplier
                        ->update([
                            $priceColumn => $newPrice,
                        ]);
                }
            } else {
                TyresProduct::where('product_id', $product->product_id)
                    ->update([
                        'tyre_fullyfitted_price' => $newPrice,
                        'tyre_mailorder_price' => $newPrice,
                        'tyre_mobilefitted_price' => $newPrice,
                        'tyre_collection_price' => $newPrice,
                        'tyre_delivery_price' => $newPrice,
                    ]);
            }
        }

        return response()->json(['success' => 'Prices updated updateTyrePrices successfully']);
    }


    public function saveTyrePricing(Request $request)
    {
        if ($request->sync_action != 1) {
            return response()->json(['error' => 'Invalid sync_action'], 400);
        }

        try {
            $this->processTyrePricingUpdate();
            return response()->json(['success' => 'Prices updated saveTyrePricing successfully']);
        } catch (\Exception $e) {
            Log::error('Tyre pricing update failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function processTyrePricingUpdate()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $tyrePricingData = DB::table('tyre_pricing')->where('status', 1)->get();

        if ($tyrePricingData->isEmpty()) {
            throw new \Exception('No active tyre pricing data found.');
        }

        foreach ($tyrePricingData as $row) {
            $priceBySize = $row->price_by_size;

            // Validate and decode `price_by_size`
            if (!empty($priceBySize) && is_string($priceBySize)) {
                $decodedPriceBySize = json_decode($priceBySize, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $priceBySize = $decodedPriceBySize;
                } else {
                    throw new \Exception('Invalid JSON format in price_by_size for row ID: ' . $row->id);
                }
            } else {
                throw new \Exception('price_by_size is missing or invalid for row ID: ' . $row->id);
            }

            $data = [
                'pricing_name' => $row->pricing_name,
                'supplier_id' => $row->supplier_id,
                'order_type_id' => $row->order_type_id,
                'margin_type' => $row->margin_type,
                'default_price' => $row->default_price,
                'price_by_size' => $priceBySize,
                'product_type' => $row->product_type,
                'status' => $row->status,
            ];
            //dd($data);

            $this->updateTyrePrices($data);
        }
    }





}
