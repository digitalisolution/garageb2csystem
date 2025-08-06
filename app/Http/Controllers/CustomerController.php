<?php

namespace App\Http\Controllers;

use App\Models\VehicleDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\RegionCounty;
use App\Models\Workshop;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\HeaderLink;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatementEmail;
use Illuminate\Support\Str;
class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function save(Request $request, $id = null)
    {
        $viewData['pageTitle'] = 'Customer Dretail';
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $viewData['counties'] = RegionCounty::where('status', 1)->get();
        $getFormAutoFillup = array();
        if (isset($id) && $id != null) {
            $getFormAutoFillup = Customer::whereId($id)->first()->toArray();
        } else {
            if (((!isset($id) && $id == null) && $request->isMethod('post'))) {
                $allInputValue = $request->all();
                $allInputValueManage = new Customer($allInputValue);
                $allInputValueManage['created_by'] = Auth::user()->id;
                if ($allInputValueManage->save()) {
                    $request->session()->flash('message.level', 'success');
                    $request->session()->flash('message.content', 'Customer Detail Saved Successfully!');
                    $request->session()->flash('message.icon', 'check');
                } else {
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Somthing went Worng! !');
                    $request->session()->flash('message.icon', 'times');
                }

            }

        }
        $viewData['customer'] = Customer::orderBy('id', 'desc')->get();
        return view('AutoCare.customer', $viewData)->with($getFormAutoFillup);
    }

    public function update(Request $request, $id = null)
    {
        $viewData['pageTitle'] = 'customer Dretail';
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        // $viewData['customer'] = Customer::orderBy('id','desc')->get();
        $viewData['counties'] = RegionCounty::where('status', 1)->get();
        $getFormAutoFillup = array();

        if (((!isset($id) && $id == null) && $request->isMethod('post'))) {
            $allInputValueForUpdate = request()->except(['_token']);
            $allInputValueForUpdate['created_by'] = Auth::user()->id;

            if (Customer::where([['id', '=', $request->id]])->update($allInputValueForUpdate)) {
                $request->session()->flash('message.level', 'info');
                $request->session()->flash('message.content', 'customer Dretails Updated Successfully!');
                $request->session()->flash('message.icon', 'check');
            } else {
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'Somthing went Worng! !');
                $request->session()->flash('message.icon', 'times');
            }

        }

        $viewData['customer'] = Customer::orderBy('id', 'desc')->get();
        return view('AutoCare.customer', $viewData)->with($getFormAutoFillup);
    }


    public function view(Request $request)
    {
        // Fetch header links
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
    
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'customer';
            $customer = DB::table('customers');
            $viewData['counties'] = RegionCounty::where('status', 1)->get();
    
            // Search by ID
            if ($request->has('id') && $request->id != '') {
                $searchTerm = '%' . $request->id . '%'; // Use LIKE for partial match
                $customer->where('id', 'like', $searchTerm);
            }
    
            // Search by customer name or company name
            if ($request->has('customer_name') && $request->customer_name != '') {
                $searchTerm = '%' . $request->customer_name . '%';
                $customer->where(function ($query) use ($searchTerm) {
                    $query->where('customer_name', 'like', $searchTerm)
                          ->orWhere('company_name', 'like', $searchTerm);
                });
            }
    
            // Search by created_at_from
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $created_at_from = date("Y-m-d", strtotime($request->created_at_from));
                $customer->whereDate('created_at', '<=', $created_at_from);
            }
    
            // Search by created_at_to
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $created_at_to = date("Y-m-d", strtotime($request->created_at_to));
                $customer->whereDate('created_at', '>=', $created_at_to);
            }
    
            // Search by customer contact number
            if ($request->has('customer_contact_number') && $request->customer_contact_number != '') {
                $searchTerm = '%' . $request->customer_contact_number . '%'; // Use LIKE for partial match
                $customer->where('customer_contact_number', 'like', $searchTerm);
            }
    
            // Search by customer email
            if ($request->has('customer_email') && $request->customer_email != '') {
                $searchTerm = '%' . $request->customer_email . '%'; // Use LIKE for partial match
                $customer->where('customer_email', 'like', $searchTerm);
            }
    
            // Order by ID descending
            $customer->orderBy('id', 'desc');
    
            // Get the results
            $viewData['customer'] = json_decode(json_encode($customer->get()), true);
    
            return view('AutoCare.customer', $viewData);
        } else {
            // Default view (no search)
            $viewData['pageTitle'] = 'customer';
            $viewData['counties'] = RegionCounty::where('status', 1)->get();
            $viewData['customer'] = Customer::orderBy('id', 'desc')->get();
            return view('AutoCare.customer', $viewData);
        }
    }
    public function trash(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        if (($id != null) && (Customer::where('id', $id)->delete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'customer was Trashed!');
            $viewData['pageTitle'] = 'customer';
            $viewData['customer'] = Customer::get();
            // return view('AutoCare.customer', $viewData);
             return redirect()->back();
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'customer';
            $viewData['customer'] = Customer::get();
            // return view('AutoCare.customer', $viewData);
             return redirect()->back();
        }

    }
    public function trashedList()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $TrashedParty = Customer::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.customer.delete', compact('TrashedParty', 'TrashedParty'));
    }
    public function permanemetDelete(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        if (($id != null) && (Customer::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', "customer was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }
        $TrashedParty = Customer::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.customer', compact('TrashedParty', 'TrashedParty'));
    }



    // Fetch and display customer details
    public function details($id)
    {
        $customer = Customer::findOrFail($id); // Fetch customer by ID
        $counties = RegionCounty::where('status', 1)->get(); // Fetch active counties

        return view('AutoCare.customer.details', compact('customer', 'counties'));
    }


    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|min:2|max:50',
            'customer_last_name' => 'nullable|string|min:2|max:50',
            'customer_email' => 'nullable|email|unique:customers,customer_email,' . $id,
            'customer_contact_number' => 'nullable|string|min:10|max:15',
            'customer_alt_number' => 'nullable|string|min:10|max:15',
            'company_name' => 'nullable|string|max:100',
            'company_website' => 'nullable|string|max:100',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only([
            'customer_name',
            'customer_last_name',
            'customer_email',
            'customer_contact_number',
            'customer_alt_number',
            'company_name',
            'company_website',
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Find the customer by ID
        $customer = Customer::findOrFail($id);

        // Update the customer's password
        $customer->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    public function updateBillingAddress(Request $request, $id)
    {
        $request->validate([
            'billing_address_street' => 'required|string|min:3|max:100',
            'billing_address_city' => 'required|string|min:3|max:50',
            'billing_address_postcode' => 'required|string|min:3|max:10',
            'billing_address_county' => 'required|exists:region_county,zone_id',
            'billing_address_country' => 'required|string',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only([
            'billing_address_street',
            'billing_address_city',
            'billing_address_postcode',
            'billing_address_county',
            'billing_address_country',
        ]));

        return redirect()->back()->with('success', 'Billing address updated successfully!');
    }

    public function updateShippingAddress(Request $request, $id)
    {
        $request->validate([
            'shipping_address_street' => 'required|string|min:3',
            'shipping_address_city' => 'required|string|min:3',
            'shipping_address_postcode' => 'required|string|min:3',
            'shipping_address_county' => 'required|exists:region_county,zone_id',
            'shipping_address_country' => 'required|string',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update($request->only([
            'shipping_address_street',
            'shipping_address_city',
            'shipping_address_postcode',
            'shipping_address_county',
            'shipping_address_country'
        ]));

        return redirect()->back()->with('success', 'Shipping address updated successfully!');
    }



    // Fetch and display customer orders
    public function orders(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch workshops with pagination and eager loading
        $workshops = Workshop::where('customer_id', $customer->id)->where('is_void', false)
            ->with('items')->get();

        // Transform only the items (not the entire paginated object)
       

        return view('AutoCare.customer.orders', compact('workshops', 'customer'));
    }

    // Fetch and display customer invoices
    public function invoices(Request $request, $id)
    {
        $customer = Customer::findOrFail($id); // Fetch customer by ID
        $invoices = Invoice::where('customer_id', $customer->id)->where('is_void', false)
            ->with('items')->get(); // Assuming a relationship exists in the Customer model

        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();

        // Calculate invoice counts for each status
        $unpaidCount = $customer->invoices()->where('payment_status', 0)->where('is_void', false)->count();
        $paidCount = $customer->invoices()->where('payment_status', 1)->where('is_void', false)->count();
        $overdueCount = $customer->invoices()->where('payment_status', 2)->where('is_void', false)->count();
        $partiallyPaidCount = $customer->invoices()->where('payment_status', 3)->where('is_void', false)->count();

        return view('AutoCare.customer.invoices', compact('customer', 'invoices', 'unpaidCount', 'paidCount', 'overdueCount', 'partiallyPaidCount'));

    }

    public function statements(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch invoices within the selected date range
        $query = Invoice::where('customer_id', $id)->where('is_void', false);

        if ($request->filled('from') && $request->filled('to')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay();
                $to = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay();
            } catch (\Exception $e) {
                // Log::error("Invalid dat/e format: " . $e->getMessage());
                return response()->json(['error' => 'Invalid date format. Please use DD-MM-YYYY.'], 400);
            }
        }else{
                 $from = Carbon::now()->startOfMonth();
                 $to = Carbon::now()->endOfMonth();
        }
        
        $query->whereBetween('created_at', [$from, $to]);
        $invoices = $query->get();
        // Log::info("Filtered invoices count: " . $invoices->count());

        // Initialize transactions as an empty collection
        $transactions = collect();

        // Populate transactions only if invoices exist
        if (!$invoices->isEmpty()) {
            foreach ($invoices as $invoice) {
                $transactions->push([
                    'date' => $invoice->created_at->format('d-m-Y'),
                    'details' => 'Invoice #' . $invoice->workshop_id,
                    'type' => 'Invoice',
                    'amount' => $invoice->grandTotal,
                    'paid_price' => $invoice->paid_price,
                    'discountPrice' => $invoice->discount_price,
                    'balance_price' => $invoice->balance_price
                ]);
            }
        } else {
            // Log::warning("No invoices found for customer_id: $id in the given date range.");
        }

        $totalInvoiced = $invoices->sum('grandTotal');
        $discountPrice = $invoices->sum('discount_price');
        $totalPaid = $invoices->sum('paid_price')+ $discountPrice;
        $balanceDue = $totalInvoiced - $totalPaid;

        if ($request->ajax()) {
            return response()->json([
                'totalInvoiced' => $totalInvoiced,
                'totalPaid' => $totalPaid,
                'balanceDue' => $balanceDue,
                'discountPrice' => $discountPrice,
                'transactionsHtml' => $transactions
            ]);
        }

        return view('AutoCare.customer.statements', compact(
            'customer',
            'totalInvoiced',
            'totalPaid',
            'balanceDue',
            'discountPrice',
            'transactions'
        ));
    }


    // Fetch and display customer vehicles
    public function vehicles(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $vehicles = $customer->vehicles()->paginate(10); // Fetch vehicles linked to this customer
        // dd($vehicles);
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();

        return view('AutoCare.customer.vehicles', compact('customer', 'vehicles', 'viewData'));
    }

    // Show form to create a new vehicle for a customer
    public function createVehicle($id)
    {
        $customer = Customer::findOrFail($id);
        return view('AutoCare.customer.vehicle.vehicle-form', compact('customer'));
    }


    // Store a new vehicle and associate it with a customer
    public function updateVehicle(Request $request, $id, $vehicleId)
    {
        $customer = Customer::findOrFail($id);
        $vehicle = $customer->vehicles()->findOrFail($vehicleId);

        // Validation
        $request->validate([
            'vehicle_reg_number' => 'required|string|max:20|unique:vehicle_details,vehicle_reg_number,' . $vehicle->id,
            'vehicle_category' => 'required|string|max:255',
            'vehicle_make' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'nullable|string|max:10',
            'vehicle_cc' => 'nullable|string|max:10',
            'vehicle_fuel_type' => 'nullable|string|max:50',
            'vehicle_body_type' => 'nullable|string|max:50',
            'vehicle_bhp' => 'nullable|string|max:50',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_engine_size' => 'nullable|string|max:50',
            'vehicle_engine_code' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
            'vehicle_front_tyre_size' => 'nullable|string|max:50',
            'vehicle_rear_tyre_size' => 'nullable|string|max:50',
            'vehicle_colour' => 'nullable|string|max:50',
            'vehicle_first_registered' => 'nullable|date',
            'vehicle_chassis_no' => 'nullable|string|max:50',
            'vehicle_oil_capacity' => 'nullable|string|max:50',
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_battery_type' => 'nullable|string|max:50',
            'vehicle_service_book_stamped' => 'nullable|integer|in:1,2,3',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);

        // Update Vehicle
        $vehicle->update($request->all());

        return redirect()->route('AutoCare.customer.vehicles', ['id' => $id])
            ->with('success', 'Vehicle updated successfully!');
    }

    

    public function storeVehicle(Request $request, $id)
    {
        // dd($request);
        // Log the start of the function
        Log::info('storeVehicle function started.');
    
        // Find the customer by ID
        $customer = Customer::findOrFail($id);
        Log::info('Customer retrieved successfully.', ['customer_id' => $customer->id]);
    
        // Validation
        $validatedData = $request->validate([
            'vehicle_reg_number' => 'required|string|max:20',
            'vehicle_make' => 'required|string|max:255',
            'vehicle_model' => 'required|string|max:255',
            'vehicle_year' => 'nullable|string|max:10',
            'vehicle_cc' => 'nullable|string|max:10',
            'vehicle_fuel_type' => 'nullable|string|max:50',
            'vehicle_body_type' => 'nullable|string|max:50',
            'vehicle_bhp' => 'nullable|string|max:50',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_engine_size' => 'nullable|string|max:50',
            'vehicle_engine_code' => 'nullable|string|max:50',
            'vehicle_vin' => 'nullable|string|max:50',
            'vehicle_front_tyre_size' => 'nullable|string|max:50',
            'vehicle_rear_tyre_size' => 'nullable|string|max:50',
            'vehicle_colour' => 'nullable|string|max:50',
            'vehicle_first_registered' => 'nullable|date',
            'vehicle_chassis_no' => 'nullable|string|max:50',
            'vehicle_oil_capacity' => 'nullable|string|max:50',
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_battery_type' => 'nullable|string|max:50',
            'vehicle_service_book_stamped' => 'nullable|integer|in:1,2,3',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);
        Log::info('Validation passed successfully.', ['validated_data' => $validatedData]);
    
        // Create or Get Vehicle
        try {
            $vehicle = VehicleDetail::firstOrCreate(
                ['vehicle_reg_number' => $request->vehicle_reg_number],
                $request->all()
            );
            Log::info('Vehicle retrieved or created successfully.', [
                'vehicle_id' => $vehicle->id,
                'vehicle_reg_number' => $vehicle->vehicle_reg_number,
                'vehicle_make' => $vehicle->make,
                'vehicle_model' => $vehicle->model,
            ]);
        } catch (\Exception $e) {
            Log::error('Error occurred while creating or retrieving vehicle.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while processing the vehicle.');
        }
    
        // Attach vehicle to customer if not already attached
        try {
            if (!$customer->vehicles()->where('vehicle_detail_id', $vehicle->id)->exists()) {
                $customer->vehicles()->attach($vehicle->id);
                Log::info('Vehicle attached to customer successfully.', [
                    'customer_id' => $customer->id,
                    'vehicle_id' => $vehicle->id,
                ]);
            } else {
                Log::info('Vehicle is already attached to the customer.', [
                    'customer_id' => $customer->id,
                    'vehicle_id' => $vehicle->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error occurred while attaching vehicle to customer.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while attaching the vehicle to the customer.');
        }
    
        Log::info('storeVehicle function completed successfully.');
        return redirect()->route('AutoCare.customer.vehicles', ['id' => $id])
            ->with('success', 'Vehicle added successfully!');
    }



    // Show form to edit an existing vehicle (only for the linked customer)
    public function editVehicle($id, $vehicleId)
    {
        $customer = Customer::findOrFail($id);
        $vehicle = $customer->vehicles()->findOrFail($vehicleId); // Correct vehicle ID

        return view('AutoCare.customer.vehicle.vehicle-form', compact('customer', 'vehicle'));
    }


    // Remove the association of a vehicle from a customer (but do not delete the vehicle itself)
    public function deleteVehicle($id, $vehicleId)
    {
        $customer = Customer::findOrFail($id);

        // Ensure the vehicle exists and is linked to the customer before detaching
        if ($customer->vehicles()->where('vehicle_detail_id', $vehicleId)->exists()) {
            $customer->vehicles()->detach($vehicleId);

            return redirect()->route('AutoCare.customer.vehicles', ['id' => $id])
                ->with('success', 'Vehicle unlinked from customer successfully!');
        }

        return redirect()->route('AutoCare.customer.vehicles', ['id' => $id])
            ->with('error', 'Vehicle not found or not linked to this customer.');
    }
    public function sendStatementEmail(Request $request)
    {
        // Validate request
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'email_to' => 'required|email',
            'email_cc' => 'nullable|email',
            'attach_pdf' => 'boolean',
            'email_body' => 'nullable|string',
        ]);

        // Get customer
        $customer = Customer::findOrFail($request->customer_id);

        // Generate PDF
        $this->generateStatementPdf($customer->id);

        // Build PDF path
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');
        $pdfPath = "statements/{$safeGarageName}/STAT-{$customer->id}.pdf";
        $pdfFullPath = storage_path("app/public/{$pdfPath}");

        // Prepare data for email
        $data = [
            'customer' => $customer,
            'totalInvoiced' => $request->totalInvoiced ?? 0,
            'totalPaid' => $request->totalPaid ?? 0,
            'balanceDue' => $request->balanceDue ?? 0,
            'discountPrice' => $request->discountPrice ?? 0,
            'body' => $request->email_body
        ];

        // Send email with optional attachment
        Mail::to($request->email_to)
            ->cc($request->email_cc)
            ->send(new StatementEmail($data, $request->attach_pdf ? $pdfFullPath : null));

        return redirect()->back()->with('success', 'Statement sent successfully!');
    }
    public function previewStatementPdf($id)
{
    $this->generateStatementPdf($id); // Always regenerate latest version

    $garageName = getGarageDetails()->garage_name;
    $safeGarageName = Str::slug($garageName, '_');
    $pdfPath = "statements/{$safeGarageName}/STAT-{$id}.pdf";

    if (!Storage::disk('public')->exists($pdfPath)) {
        abort(404, 'Statement PDF not found.');
    }

    return response()->file(storage_path("app/public/{$pdfPath}"), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="statement.pdf"'
    ]);
    }
    public function downloadStatementPdf($id)
    {
    $this->generateStatementPdf($id); // Regenerate fresh statement

    $garageName = getGarageDetails()->garage_name;
    $safeGarageName = Str::slug($garageName, '_');
    $pdfPath = "statements/{$safeGarageName}/STAT-{$id}.pdf";

    if (!Storage::disk('public')->exists($pdfPath)) {
        abort(404, 'Statement PDF not found.');
    }

    return response()->download(storage_path("app/public/{$pdfPath}"), "Statement-{$id}.pdf", [
        'Content-Type' => 'application/pdf',
    ]);
    }
    public function generateStatementPdf($id)
    {
        // Fetch customer and statement data
        $customer = Customer::findOrFail($id);
        $query = Invoice::where('customer_id', $id)->where('is_void', false)->orderBy('created_at', 'asc');

        if (request()->filled('from') && request()->filled('to')) {
            try {
                $from = \Carbon\Carbon::createFromFormat('d-m-Y', request('from'))->startOfDay();
                $to = \Carbon\Carbon::createFromFormat('d-m-Y', request('to'))->endOfDay();
                $query->whereBetween('created_at', [$from, $to]);
            } catch (\Exception $e) {
                abort(400, 'Invalid date format. Use DD-MM-YYYY.');
            }
        }

        $invoices = $query->get();
        // Build transactions list
        $transactions = collect();
        foreach ($invoices as $invoice) {
            $transactions->push([
                'date' => $invoice->created_at->format('d-m-Y'),
                'details' => 'Invoice #' . $invoice->workshop_id,
                'type' => 'Invoice',
                'amount' => $invoice->grandTotal,
                'paid_price' => $invoice->paid_price,
                'discountPrice' => $invoice->discount_price,
                'balance_price' => $invoice->balance_price
            ]);
        }

        $totalInvoiced = $invoices->sum('grandTotal');
        $discountPrice = $invoices->sum('discount_price');
        $totalPaid = $invoices->sum('paid_price')+ $discountPrice;
        $balanceDue = $totalInvoiced - $totalPaid;

        // Format discount for each invoice
        foreach ($invoices as &$invoice) {
            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $invoice->formatted_discount = '(' . $invoice->discount_value . '%)';
            } else {
                $invoice->formatted_discount = '';
            }
        }

        // Generate PDF content
        $pdf = PDF::loadView('AutoCare.customer.statement-pdf', compact(
            'customer',
            'invoices',
            'transactions',
            'totalInvoiced',
            'totalPaid',
            'discountPrice',
            'balanceDue'
        ));

        // Define folder name based on garage
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');

        // Define path
        $pdfPath = "statements/{$safeGarageName}/STAT-{$customer->id}.pdf";

        // Save PDF
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return storage_path("app/public/{$pdfPath}");
    }

}
