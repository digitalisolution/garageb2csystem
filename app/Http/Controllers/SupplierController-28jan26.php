<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\HeaderLink;
use App\Models\TyresProduct;
use App\Models\tyre_brands;
use App\Models\DeliveryTime;
use App\Models\Garage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    public function save(Request $request, $id = null)
    {
        try {
            $viewData['pageTitle'] = 'Supplier Detail';
            $viewData['header_link'] = HeaderLink::where("menu_id", '6')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
            // ✅ ALWAYS load garages
            $viewData['garages'] = Garage::select('id', 'garage_name')
                ->orderBy('garage_name')
                ->get();

            if ($id) {
                $supplier = Supplier::findOrFail($id);
                $viewData['fullyFittedItems'] = DeliveryTime::where('supplier', $id)->where('delivery_type', 'fully_fitted')->get()->toArray();
                $viewData['mobileFittedItems'] = DeliveryTime::where('supplier', $id)->where('delivery_type', 'mobile_fitted')->get()->toArray();

                $getFormAutoFillup = $supplier->toArray();
                $getFormAutoFillup['api_order_details'] = json_decode($getFormAutoFillup['api_order_details'] ?? '[]', true);
                $viewData['item_type'] = in_array($getFormAutoFillup['api_order_details']['item_type'] ?? null, ['tyres', 'all']) ? $getFormAutoFillup['api_order_details']['item_type'] : null;

                return view('AutoCare.supplier.add', $viewData)->with($getFormAutoFillup);
            }
            if ($request->isMethod('get')) {
                $viewData['garages'] = Garage::select('id', 'garage_name')->orderBy('garage_name')->get();
                return view('AutoCare.supplier.add', $viewData);
            }

            /*if ($request->isMethod('get')) {
                return view('AutoCare.supplier.add', $viewData);
            }*/

            $validated = $request->validate([
                'garage_id' => 'required|exists:garages,id',
                'supplier_name' => 'required|string|max:100',
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:20',
                'status' => 'required|boolean',
                'api_name' => 'nullable|string|max:100',
                'api_token' => 'nullable|string|max:255',
                'api_url' => 'nullable|url|max:255',
                'api_order_details.item_type' => 'nullable|in:tyres,all',
            ]);

            $supplierData = $request->except('_token', 'id');
            $supplierData['api_order_details'] = json_encode($request->input('api_order_details', []));

            if ($request->filled('id')) {
                $supplier = Supplier::findOrFail($request->id);
                $supplier->update($supplierData);
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Supplier updated successfully!');
                // return redirect('/AutoCare/supplier/add/' . $request->id);
                return redirect()->route('supplier.manage')->with('success', 'Supplier updated successfully!');
            } else {
                $supplier = Supplier::create($supplierData);
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Supplier saved successfully!');
                 return redirect()->route('supplier.manage')->with('success', 'Supplier updated successfully!');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            \Log::error('Supplier Save Error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            \Log::error("Error creating supplier: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function toggleWebsiteStatus(Request $request)
    {
        $supplier = Supplier::find($request->supplier_id);

        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier not found'
            ]);
        }

        $supplier->website_display_status = $request->website_display_status;
        $supplier->save();

        return response()->json([
            'status' => true,
            'message' => 'Website display status updated successfully'
        ]);
    }
    public function view(Request $request)
    {
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Supplier';
            $viewData['header_link'] = HeaderLink::where("menu_id", '6')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
            $query = Supplier::withCount('products')->orderBy('id', 'desc');
            $Supplier = DB::table('suppliers');
            if ($request->has('id') && $request->id != '') {
                $Supplier->where('id', '=', $request->id);
            }
            if ($request->has('supplier_name') && $request->supplier_name != '') {
                $Supplier->where('supplier_name', 'like', '%' . $request->supplier_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $Supplier->whereDate('created_at', '<=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $Supplier->whereDate('created_at', '>=', $request->created_at_to);
            }
            if ($request->has('mobile') && $request->mobile != '') {
                $Supplier->where('mobile', '=', $request->mobile);
            }
            if ($request->has('status') && $request->status != '') {
                $Supplier->where('status', '=', $request->status);
            }
            if ($request->has('email') && $request->email != '') {
                $Supplier->where('email', '=', $request->email);
            }
            $Supplier->orderBy('id', 'desc');
            $Supplier = $Supplier->get();
            $viewData['supplier'] = $query->get();
            $viewData['supplier'] = json_decode(json_encode($Supplier), true);
            return view('AutoCare.supplier.search', $viewData);

        } else {
            $query = Supplier::withCount('products')->orderBy('id', 'desc');
            $viewData['pageTitle'] = 'Supplier';
            $viewData['header_link'] = HeaderLink::where("menu_id", '6')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

            //$viewData['supplier'] = Supplier::orderBy('id', 'desc')->get();
            $viewData['supplier'] = Supplier::with('garage')->orderBy('id', 'desc')->get();

            $viewData['supplier'] = $query->get();
            return view('AutoCare.supplier.search', $viewData);
        }

    }

    public function permanemetDelete(Request $request, $id)
    {
        try{
            
        if (($id != null) && (Supplier::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', "Supplier was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }
        return redirect()->route('supplier.manage')->with('success', 'Supplier Deleted successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error permanent delete supplier: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function importTyres(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required_if:upload-tab,active|string',
            'file_upload' => 'required|file|mimes:csv,txt|max:22048',
        ]);

        // Check if CSV tab is selected and process the CSV import
        if ($request->has('supplier_name') && strtolower($request->supplier_name) === 'ownstock') {
            return $this->importOwnstockTyres($request);
        }

        return redirect()->back()->withErrors(['mode' => 'Please select a valid import method.']);
    }

    public function importOwnstockTyres(Request $request)
    {
        try{
        // Validate the file input
        $validator = Validator::make($request->all(), [
            'garage_id' => 'required|exists:garages,id',
            'file_upload' => 'required|file|mimes:csv,txt|max:22048',
            'supplier_name' => 'required_if:upload-tab,active|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $garageId = $request->input('garage_id');

        // Read the CSV file and parse data
        $file = $request->file('file_upload');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data);
        $rows = $data;
        //$supplierId = $request->input('supplier_id', 1); // Default to 50 if not provided
        $supplierName = 'ownstock';
        $garageId = $request->garage_id;

        $supplier = Supplier::where('supplier_name', 'ownstock')
            ->where('garage_id', $garageId)
            ->first();

        if (!$supplier) {
            // auto-create ownstock supplier for this garage
            $supplier = Supplier::create([
                'supplier_name' => 'ownstock',
                'garage_id'     => $garageId,
                'status'        => 1,
            ]);
        }

        $supplierId = $supplier->id;


        
        $supplierName = $request->input('supplier_name', 'ownstock'); // Default to 50 if not provided
        if ($request->has('delete_existing') && $request->delete_existing == 1) {
            // Delete existing tyres for the supplier and tyre_supplier_name 'ownstock'
            TyresProduct::where('tyre_supplier_name', $supplierName)->where('supplier_id', $supplierId)->delete();
            $tableName = (new TyresProduct)->getTable();
            $connection = (new TyresProduct)->getConnectionName() ?? config('database.default');

            if (TyresProduct::count() === 0) {
            DB::connection($connection)->statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
            }
        }

        $insertData = [];
        $batchSize = 500; // Number of rows to insert per batch

        foreach ($rows as $row) {
            $row = array_combine($header, $row);

            // Fetch the brand name from the CSV
            $brandName = $row['Brand'] ?? null;

            $manufacturerId = null;
            if ($brandName) {
                $brand = tyre_brands::where('name', '=', $brandName)->first();

                if ($brand) {
                    $manufacturerId = $brand->brand_id;
                } else {
                    $newBrand = tyre_brands::insertGetId([
                        'name' => $brandName,
                        'slug' => Str::slug($brandName),
                        'promoted' => 0,
                        'sort_order' => 1,
                        'status' => 1,
                        'product_type' => 'tyre',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // After inserting, get the brand_id for the new brand
                    $manufacturerId = $newBrand;
                }
            }
        $seasonMap = [
                    'S' => 'Summer',
                    'A' => 'All Season',
                    'W' => 'Winter',
                    'Summer' => 'Summer',
                    'All Season' => 'All Season',
                    'Winter' => 'Winter',
                ];
                $tyre_season = isset($row['Season']) && isset($seasonMap[strtoupper($row['Season'])])
                    ? $seasonMap[strtoupper($row['Season'])]
                    : 'Summer';
                $tyre_model = $row['Model'] ?? null;
                $insertData[] = [
                'tyre_sku' => $row['SKU'] ?? null,
                'tyre_ean' => $row['EAN'] ?? null,
                'tyre_quantity' => ($row['Quantity'] ?? 0) >= 1 ? $row['Quantity'] : 0,
                'tyre_price' => $row['Price'] ?? 0,
                'tyre_brand_id' => $manufacturerId,
                
                'tyre_season' => $tyre_season,
                'tyre_width' => $row['Width'] ?? null,
                'tyre_profile' => $row['Profile'] ?? null,
                'tyre_diameter' => $row['Diameter'] ?? null,
                'tyre_speed' => $row['Speed'] ?? null,
                'status' => ($row['Quantity'] ?? 0) >= 1 ? 1 : 0,
                'tyre_margin' => $row['Margin'],
                'tyre_fullyfitted_price' => ($row['Price'] ?? 0) + ( $row['Margin'] ?? 20),
                'tyre_mobilefitted_price' => ($row['Price'] ?? 0) + ( $row['Margin'] ?? 20),
                'tyre_collection_price' => ($row['Price'] ?? 0) + ( $row['Margin'] ?? 20),
                'tyre_delivery_price' => ($row['Price'] ?? 0) + ( $row['Margin'] ?? 20),
                'trade_costprice' => $row['Price'] + ( $row['Margin'] ?? 20),
                'tyre_brand_name' => $brandName,
                'tyre_model' => $tyre_model,
                'tyre_description' => $tyre_season . ' Tyre ' .$brandName.' '. $tyre_model . ' ' . ($row['Width'] ?? '') . '/' . ($row['Profile'] ?? '') . 'R' . ($row['Diameter'] ?? '') . ' ' . $row['Load_Index'] .' '. $row['Speed'],
                'tyre_loadindex' => $row['Load_Index'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
                'tyre_noisedb' => $row['Noise'] ?? $normalized_row['Noisedb'] ?? '',
                'tyre_fuel' => $row['Fuel'] ?? null,
                'tyre_wetgrip' => $row['Wetgrip'] ?? '',
                'tyre_runflat' => isset($row['Runflat']) && $row['Runflat'] !== '' ? (int)$row['Runflat'] : 0,
                'tyre_extraload' => isset($row['Extraload']) && $row['Extraload'] !== '' ? (int)$row['Extraload'] : 0,
                //'tyre_extraload' => $row['Extraload'] ?? 0,
                'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['Vehicle_Type'] ?? ''))),
                'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                'product_type' => 'tyre',
                'tax_class_id' => 9,
                'instock' => 1,
                // 'date_available' => now(),
                'tyre_image' => $row['IMAGE'] ?? null,
                'supplier_id' => $supplierId,  // Add supplier_id here
                'tyre_supplier_name' => $supplierName, // Add supplier_name here
                'garage_id' => $garageId,
            ];

            // Insert in batches
            if (count($insertData) >= $batchSize) {
                TyresProduct::insert($insertData);
                $insertData = []; // Reset the batch
            }
        }

        // Insert remaining data if any
        if (!empty($insertData)) {
            TyresProduct::insert($insertData);
        }

        return redirect()->back()->with('message.level', 'success')->with('message.content', 'Tyres imported successfully!');
        } catch (\Throwable $e) {
            \Log::error("Error importing supplier: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

        public function downloadCsv($supplierId)
        {
            try{
            // Fetch the supplier details
            $supplier = Supplier::findOrFail($supplierId);
        
            // Determine the file source (FTP or direct file path)
            if ($supplier->import_method === 'ftp') {
                // Fetch the file from FTP
                $ftpDetails = [
                    'ftp_host' => $supplier->ftp_host,
                    'ftp_user' => $supplier->ftp_user,
                    'ftp_password' => $supplier->ftp_password,
                    'ftp_directory' => $supplier->ftp_directory,
                ];
        
                try {
                    $fileContent = $this->fetchFileFromFtp($ftpDetails);
                    $fileName = basename($supplier->ftp_directory); // Extract file name from path
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors(['error' => 'Failed to fetch file from FTP: ' . $e->getMessage()]);
                }
            } elseif ($supplier->import_method === 'file_path') {
                // Use the direct file path
                $filePath = $supplier->file_path;
        
                if (!file_exists($filePath)) {
                    return redirect()->back()->withErrors(['error' => 'File does not exist at the specified path.']);
                }
        
                $fileContent = file_get_contents($filePath);
                $fileName = basename($filePath); // Extract file name from path
            } else {
                return redirect()->back()->withErrors(['error' => 'Invalid import method specified.']);
            }
        
            // Return the file as a downloadable response
            return Response::make($fileContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
            } catch (\Throwable $e) {
                \Log::error("Error download supplier: " . $e->getMessage());
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

    private function fetchFileFromFtp($ftpDetails)
    {
        try{
        $ftp_host = $ftpDetails['ftp_host'];
        $ftp_user = $ftpDetails['ftp_user'];
        $ftp_password = $ftpDetails['ftp_password'];
        $ftp_file_path = $ftpDetails['ftp_directory'];

        // Connect to the FTP server
        $ftpConnection = ftp_connect($ftp_host);
        if (!$ftpConnection) {
            throw new \Exception('Failed to connect to FTP server.');
        }

        // Login to the FTP server
        $login = ftp_login($ftpConnection, $ftp_user, $ftp_password);
        if (!$login) {
            ftp_close($ftpConnection);
            throw new \Exception('Failed to login to FTP server.');
        }

        // Enable passive mode
        ftp_pasv($ftpConnection, true);

        // Check if the file exists
        $fileExists = ftp_size($ftpConnection, $ftp_file_path);
        if ($fileExists == -1) {
            ftp_close($ftpConnection);
            throw new \Exception('File does not exist or cannot be accessed.');
        }

        // Download the file to a temporary location
        $localFile = storage_path('app/temp/' . basename($ftp_file_path));
        $success = ftp_get($ftpConnection, $localFile, $ftp_file_path, FTP_BINARY);
        ftp_close($ftpConnection);

        if (!$success) {
            throw new \Exception('Failed to fetch the file.');
        }

        // Read the file content
        return file_get_contents($localFile);
        } catch (\Throwable $e) {
            \Log::error("Error creating supplier: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        }

        public function store(Request $request, $id)
        {
            try{
            // Validate Fully Fitted
            $request->validate([
                'fully_fitted_supplier' => 'required|integer',
                'fully_fitted_delivery_type.*' => 'nullable|in:fully_fitted',
                'fully_fitted_day.*' => 'nullable|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'fully_fitted_start_hours.*' => 'nullable|digits:2',
                'fully_fitted_start_minutes.*' => 'nullable|digits:2',
                'fully_fitted_end_hours.*' => 'nullable|digits:2',
                'fully_fitted_end_minutes.*' => 'nullable|digits:2',
                'fully_fitted_delivery_time.*' => 'nullable|numeric',
                'fully_fitted_row_id.*' => 'nullable|integer',
            ]);

            // Process Fully Fitted
            $this->processDeliveryTimeData($request, $id, 'fully_fitted_', 'fully_fitted');

            // Validate Mobile Fitted
            $request->validate([
                'mobile_fitted_supplier' => 'required|integer',
                'mobile_fitted_delivery_type.*' => 'nullable|in:mobile_fitted',
                'mobile_fitted_day.*' => 'nullable|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'mobile_fitted_start_hours.*' => 'nullable|digits:2',
                'mobile_fitted_start_minutes.*' => 'nullable|digits:2',
                'mobile_fitted_end_hours.*' => 'nullable|digits:2',
                'mobile_fitted_end_minutes.*' => 'nullable|digits:2',
                'mobile_fitted_delivery_time.*' => 'nullable|numeric',
                'mobile_fitted_row_id.*' => 'nullable|integer',
            ]);

            // Process Mobile Fitted
            $this->processDeliveryTimeData($request, $id, 'mobile_fitted_', 'mobile_fitted');

            return back()->with('success', 'Delivery times saved successfully!');
            } catch (\Throwable $e) {
            \Log::error("Error creating delivery time: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        }


        private function processDeliveryTimeData(Request $request, $supplierId, $prefix, $deliveryType)
        {
            try{
            $days = $request->{$prefix . 'day'} ?? [];
            $submittedIds = [];
            $count = count($days);

            for ($i = 0; $i < $count; $i++) {
                // Skip if day is empty
                if (empty($days[$i])) continue;

                $data = [
                    'supplier'       => $supplierId,
                    'delivery_type'  => $deliveryType,
                    'day'            => $request->{$prefix . 'day'}[$i] ?? null,
                    'start_hours'     => $request->{$prefix . 'start_hours'}[$i] ?? null,
                    'start_minutes'      => $request->{$prefix . 'start_minutes'}[$i] ?? null,
                    'end_hours'       => $request->{$prefix . 'end_hours'}[$i] ?? null,
                    'end_minutes'        => $request->{$prefix . 'end_minutes'}[$i] ?? null,
                    'delivery_time'  => $request->{$prefix . 'delivery_time'}[$i] ?? null,
                ];

                // Update or Create
                $rowId = $request->{$prefix . 'row_id'}[$i] ?? null;
                if ($rowId && $existing = DeliveryTime::where('id', $rowId)->where('supplier', $supplierId)->first()) {
                    $existing->update($data);
                    $submittedIds[] = $existing->id;
                } else {
                    $new = DeliveryTime::create($data);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete rows that were removed in the form for the current delivery type
            DeliveryTime::where('supplier', $supplierId)
                ->where('delivery_type', $deliveryType)
                ->whereNotIn('id', $submittedIds)
                ->delete();
                } catch (\Throwable $e) {
            \Log::error("Error deleting delivery time: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        }

    }


