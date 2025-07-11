<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\HeaderLink;
use App\Models\TyresProduct;
use App\Models\tyre_brands;
use App\Models\DeliveryTime;
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
        $viewData['pageTitle'] = 'Supplier Detail';
        $viewData['option1'] = 'Add Supplier';
        $viewData['optionValue1'] = "AutoCare/supplier/add";
        $viewData['option2'] = 'Add Product Detail';
        $viewData['optionValue2'] = "AutoCare/product/add";
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
         $viewData['fullyFittedItems'] = DeliveryTime::where('supplier', $id)
        ->where('delivery_type', 'fully_fitted')
        ->get()
        ->toArray();

        $viewData['mobileFittedItems'] = DeliveryTime::where('supplier', $id)
        ->where('delivery_type', 'mobile_fitted')
        ->get()
        ->toArray();

        // Fill data for update if id is provided
        if (isset($id) && $id != null) {
            $getFormAutoFillup = Supplier::whereId($id)->first()->toArray();

            // Decode JSON for api_order_details if exists
            if (isset($getFormAutoFillup['api_order_details']) && !empty($getFormAutoFillup['api_order_details'])) {
                $getFormAutoFillup['api_order_details'] = json_decode($getFormAutoFillup['api_order_details'], true);
            } else {
                $getFormAutoFillup['api_order_details'] = []; // Set as empty if not available
            }
            
            $item_type = $getFormAutoFillup['api_order_details']['item_type'] ?? null;
            $viewData['item_type'] = in_array($item_type, ['tyres', 'all']) ? $item_type : null;

            return view('AutoCare.supplier.add', $viewData)->with($getFormAutoFillup);
        } else if ((!isset($id) && $id == null) && !$request->isMethod('post')) {
            return view('AutoCare.supplier.add', $viewData);
        } else {
            // Handle save or update
            if ($request->isMethod('post')) {
                // Prepare data excluding CSRF token
                $supplierManame = $request->except('_token'); // Exclude CSRF token

                // Get api_order_details from the request
                $api_order_details = $request->input('api_order_details', []); // Extract api_order_details as an array

                // Now assign the api_order_details array
                $supplierManame['api_order_details'] = json_encode($api_order_details); // Encode to JSON
                // dd($supplierManame);
                // Save or update the supplier
                if (isset($request->id) && $request->id != null) {
                    // Update existing supplier
                    $supplier = Supplier::find($request->id);
                    if ($supplier) {
                        $supplier->fill($supplierManame);  // Fill the updated values
                        $supplier->save();

                        $request->session()->flash('message.level', 'success');
                        $request->session()->flash('message.content', 'Supplier updated successfully!');
                    }

                    return redirect('/AutoCare/supplier/add/' . $request->id);
                } else {
                    // Save new supplier
                    $supplierManame = new Supplier($supplierManame);
                    if ($supplierManame->save()) {
                        $request->session()->flash('message.level', 'success');
                        $request->session()->flash('message.content', 'Supplier Saved Successfully!');
                    }

                    return redirect('/AutoCare/supplier/add'); // Redirect after saving
                }
            }
        }
    }
    public function view(Request $request)
    {
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Supplier';
            $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
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
            $viewData['supplier'] = Supplier::orderBy('id', 'desc')->get();
            $viewData['supplier'] = $query->get();
            // dd($viewData['supplier']);
            //	$Supplier= DB::table('Suppliers');
            //$Supplier->orderBy('id','desc');
            //$Supplier= $Supplier->get();
            //$viewData['Supplier']=json_decode(json_encode($Supplier), true);
            return view('AutoCare.supplier.search', $viewData);
        }

    }
    public function trash(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        if (($id != null) && (Supplier::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'Supplier was Trashed!');
            $viewData['pageTitle'] = 'Supplier';
            $viewData['supplier'] = Supplier::paginate(10);
            return view('AutoCare.supplier.search', $viewData);
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'Supplier';
            $viewData['supplier'] = Supplier::paginate(10);
            return view('AutoCare.supplier.search', $viewData);
        }

    }
    public function trashedList()
    {
        $TrashedParty = Supplier::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        // $TrashedParty=$TrashedParty
        return view('AutoCare.supplier.delete', compact('TrashedParty', 'TrashedParty'));
    }
    public function permanemetDelete(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        if (($id != null) && (Supplier::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', "Supplier was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }

        $TrashedParty = Supplier::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.supplier.delete', compact('TrashedParty', 'TrashedParty'));
    }

    public function importTyres(Request $request)
    {
        // dd($request);
        // dd($request->getBaseUrl());
        // dd($request->server->get('SCRIPT_NAME'), $request->server->get('SCRIPT_FILENAME'), $request->getBasePath());


        // dd(request()->fullUrl(), request()->path(), request()->url());

        // Validate the import method (either csv or ftp)
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
        // Validate the file input
        $validator = Validator::make($request->all(), [
            'file_upload' => 'required|file|mimes:csv,txt|max:22048',
            'supplier_name' => 'required_if:upload-tab,active|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Read the CSV file and parse data
        $file = $request->file('file_upload');
        $data = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($data);
        $rows = $data;

        // Get supplier_id from the request or session (assuming it's in the request)
        $supplierId = $request->input('supplier_id', 1); // Default to 50 if not provided
        $supplierName = $request->input('supplier_name', 'ownstock'); // Default to 50 if not provided

        // Step 1: Delete existing tyres for the supplier and tyre_supplier_name 'ownstock'

        if ($request->has('delete_existing') && $request->delete_existing == 1) {
            // Delete existing tyres for the supplier and tyre_supplier_name 'ownstock'
            TyresProduct::where('tyre_supplier_name', $supplierName)->where('supplier_id', $supplierId)->delete();
            // Reset AUTO_INCREMENT for the table
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
            $brandName = $row['BRAND'] ?? null;

            // If brand name exists, fetch the corresponding manufacturer_id from the tyre_brand table
            $manufacturerId = null;
            if ($brandName) {
                // Try to find the brand in the tyre_brand table
                $brand = tyre_brands::where('name', '=', $brandName)->first();

                if ($brand) {
                    // Brand found, use the existing manufacturer_id
                    $manufacturerId = $brand->manufacturer_id;
                } else {
                    // Brand not found, create a new brand entry
                    $newBrand = tyre_brands::insertGetId([
                        'name' => $brandName,
                        'slug' => Str::slug($brandName),
                        'promoted' => 0,
                        'image' => Str::slug($brandName) . '.jpg',
                        'sort_order' => 1,
                        'status' => 1,
                        'product_type' => 'tyre',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // After inserting, get the manufacturer_id for the new brand
                    $manufacturerId = $newBrand;
                }
            }

            // Prepare the data to insert into tyres_product table
            $insertData[] = [
                'tyre_sku' => $row['SKU'] ?? null,
                'tyre_ean' => $row['EAN'] ?? null,
                'tyre_quantity' => ($row['STOCKBAL'] ?? 0) >= 1 ? $row['STOCKBAL'] : 0,
                'tyre_price' => $row['COST_PRICE'] ?? 0,
                'tyre_brand_id' => $manufacturerId,
                'tyre_season' => $row['SEASON'] ?? null,
                'tyre_width' => $row['SECTION'] ?? null,
                'tyre_profile' => $row['PROFILE'] ?? null,
                'tyre_diameter' => $row['RIM'] ?? null,
                'tyre_speed' => $row['SPEED'] ?? null,
                'status' => ($row['STOCKBAL'] ?? 0) >= 1 ? 1 : 0,
                'tyre_fullyfitted_price' => ($row['PRICE_FULLYFITTED'] ?? 0) + 20,
                'trade_costprice' => $row['PRICE_FULLYFITTED'],
                'tyre_brand_name' => $brandName,
                'tyre_model' => $row['PATTERN'] ?? null,
                'tyre_description' =>$row['SEASON'] . 'Tyre ' . $row['PATTERN'] . ' ' . ($row['SECTION'] ?? '') . '/' . ($row['PROFILE'] ?? '') . 'R' . ($row['RIM'] ?? '') . ' ' . $row['LOAD_INDEX'] . $row['SPEED'],
                'tyre_loadindex' => $row['LOAD_INDEX'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
                'tyre_noisedb' => $row['NOISE'] ?? $normalized_row['noisedb'] ?? '',
                'tyre_fuel' => $row['FUEL'] ?? null,
                'tyre_wetgrip' => $row['WET'] ?? '',
                'tyre_runflat' => ($row['RFT'] === 'Yes' || $row['RFT'] === 'RFT') ? 1 : 0,
                'tyre_extraload' => ($row['XL'] === 'XL') ? 1 : 0,
                'vehicle_type' => strtolower(trim(str_replace('Passenger', '', $row['VEHICLE_TYPE'] ?? ''))),
                'tyre_weight' => $row['WEIGHT'] ?? $row['WEIGHT_KG'] ?? null,
                'product_type' => 'tyre',
                'tax_class_id' => 9,
                'instock' => 1,
                'date_available' => now(),
                'tyre_image' => $row['IMAGE'] ?? null,
                'supplier_id' => $supplierId,  // Add supplier_id here
                'tyre_supplier_name' => $supplierName, // Add supplier_name here
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
    }


    public function downloadCsv($supplierId)
    {
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
    }

    private function fetchFileFromFtp($ftpDetails)
{
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
    }

    public function store(Request $request, $id)
    {
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
    }


    private function processDeliveryTimeData(Request $request, $supplierId, $prefix, $deliveryType)
    {
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
    }

}


