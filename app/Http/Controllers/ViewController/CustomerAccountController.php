<?php

namespace App\Http\Controllers\ViewController;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TyresProduct;
use Carbon\Carbon;

use App\Models\Invoice;
use App\Models\VehicleDetail;
use App\Models\WorkshopTyre;
use App\Models\WorkshopService;
use DB;
use App\Rules\NoTestCustomer;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;
use App\Models\GarageDetails;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ActivityLogger;
use App\Models\RegionCounty;
use App\Http\Controllers\Controller;



class CustomerAccountController extends Controller
{

    public function myAccount()
    {
        $counties = RegionCounty::where('status', 1)->get();
        return view('customer.myaccount', compact('counties'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|min:2|max:50',
            'customer_last_name' => 'required|string|min:2|max:50',
            'customer_email' => 'required|email|unique:customers,customer_email,' . Auth::guard('customer')->user()->id,
            'customer_contact_number' => 'required|string|min:10|max:15',
            'customer_alt_number' => 'nullable|string|min:10|max:15',
            'company_name' => 'nullable|string|max:100',
            'company_website' => 'nullable|string|max:100',
        ]);

        $customer = Auth::guard('customer')->user();
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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $customer->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    public function updateBillingAddress(Request $request)
    {
        $request->validate([
            'billing_address_street' => 'required|string|min:3|max:100',
            'billing_address_city' => 'required|string|min:3|max:50',
            'billing_address_postcode' => 'required|string|min:3|max:10',
            'billing_address_county' => 'required|exists:region_county,zone_id',
            'billing_address_country' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $customer->update($request->only([
            'billing_address_street',
            'billing_address_city',
            'billing_address_postcode',
            'billing_address_county',
            'billing_address_country',
        ]));

        return redirect()->back()->with('success', 'Billing address updated successfully!');
    }

    public function updateShippingAddress(Request $request)
    {
        $request->validate([
            'shipping_address_street' => 'required|string|min:3',
            'shipping_address_city' => 'required|string|min:3',
            'shipping_address_postcode' => 'required|string|min:3',
            'shipping_address_county' => 'required|exists:region_county,zone_id',
            'shipping_address_country' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();

        $customer->update($request->only([
            'shipping_address_street',
            'shipping_address_city',
            'shipping_address_postcode',
            'shipping_address_county',
            'shipping_address_country'
        ]));

        return redirect()->back()->with('success', 'Shipping address updated successfully!');
    }
    // Customer Dashboard
   public function orders()
{
    $customer = Auth::guard('customer')->user();

    $workshops = Workshop::where('customer_id', $customer->id)
        ->with('items')
        ->where(function ($query) {
            $query->where('is_void', 0)
                  ->orWhere(function ($q) {
                      $q->where('is_void', 1)
                        ->where('status', 'cancelled');
                  });
        })
        ->orderBy('id', 'desc')
        ->paginate(15)
        ->onEachSide(2);

    return view('customer.orders', compact('workshops'));
}

    public function viewOrder($id)
    {
        $customer = Auth::guard('customer')->user();
        $workshop = Workshop::where('customer_id', $customer->id)
    ->where(function ($query) {
        $query->where('is_void', 0)
              ->orWhere(function ($q) {
                  $q->where('is_void', 1)
                    ->where('status', 'cancelled');
              });
    })
    ->findOrFail($id);

        if (!$workshop) {
            return redirect()->route('customer.orders')->with('error', 'You are not authorized to view this order.');
        }

        // Fetch related data
        $vehicle = $workshop->vehicle;
        $WorkshopTyre = WorkshopTyre::where('workshop_id', $workshop->id)->where('is_void', 0)->get();
        $WorkshopService = WorkshopService::where('workshop_id', $workshop->id)->where('is_void', 0)->get();
        $WorkshopVehicle = DB::table('vehicle_details')
            ->where('vehicle_reg_number', $workshop->vehicle_reg_number)
            ->get();
        $total_service_price = $WorkshopService->sum('service_price');
        $total_product_price = $WorkshopTyre->sum('price');
        $total_Tax_Amount = $workshop->tax_amount;
        $grandTotal = $workshop->grand_total;
        $discount_price = $workshop->discount_price;
        $paid_price = $workshop->paid_price;
        $balancePrice = $grandTotal - ($workshop->installment_payment + $paid_price + $discount_price);

        return view('customer.order.order-details', compact(
            'workshop',
            'vehicle',
            'WorkshopTyre',
            'WorkshopVehicle',
            'WorkshopService',
            'total_service_price',
            'total_product_price',
            'total_Tax_Amount',
            'grandTotal',
            'discount_price',
            'paid_price',
            'balancePrice'
        ));
    }
    public function voidWorkshop(Request $request, $workshopId)
    {

            $workshop = Workshop::findOrFail($workshopId);
            $cancelGracePeriod = (int) get_option('workshop_cancel_hours', 1);
            $bookingTime = \Carbon\Carbon::parse($workshop->created_at);
            $expiryTime = $bookingTime->copy()->addHours($cancelGracePeriod);
            if (now()->greaterThanOrEqualTo($expiryTime)) {
                if ($workshop->status === 'booked' || $workshop->status === 'processing') {
                    $workshop->status = 'processing';
                    $workshop->save();
                }
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash(
                    'message.content',
                    'Cancellation period expired. This booking has moved to Processing.'
                );
                // dd( $workshop);
                return redirect()->back();
            }elseif($workshop->status === 'processing'){
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash(
                    'message.content',
                    'Cancellation period expired. This booking has moved to Processing.'
                );
                // dd( $workshop);
                return redirect()->back();
            }

        DB::beginTransaction();

        try {
            
            // Mark workshop as void
            $workshop->is_void = true;
            $workshop->status = 'cancelled';
            $workshop->save();
            // Void related invoice, tyres, and services
            $invoice = Invoice::where('workshop_id', $workshopId)->first();
            if ($invoice) {
                $invoice->is_void = true;
                $invoice->status = 'cancelled';
                $invoice->save();
                \Log::info("Invoice voided successfully", ['invoice_id' => $invoice->id]);
                }
                WorkshopTyre::where('workshop_id', $workshop->id)
                    ->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);

                WorkshopService::where('workshop_id', $workshop->id)
                    ->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);
            

            // Restore stock for tyres
            $workshopTyres = WorkshopTyre::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->where('is_void', true)
                ->get();

            foreach ($workshopTyres as $workshopTyre) {
                $tyreProduct = TyresProduct::where(function ($query) use ($workshopTyre) {
                    $query->where('product_id', $workshopTyre->product_id)
                        ->where('tyre_supplier_name', $workshopTyre->supplier)
                        ->where('tyre_ean', $workshopTyre->product_ean);
                })->first();

                if ($tyreProduct) {
                    $tyreProduct->tyre_quantity += $workshopTyre->quantity;
                    $tyreProduct->save();

                    ActivityLogger::log(
                        workshopId: $workshop->id,
                        action: 'Restored Tyre Quantity',
                        description: 'Restored tyre quantity for product: ' . $tyreProduct->tyre_description . ' ' . $tyreProduct->tyre_ean,
                        changes: [
                            'quantity_restored' => $workshopTyre->quantity,
                            'reason' => 'Cancelled Workshop'
                        ]
                    );
                } else {
                    \Log::warning("Tyre product not found", [
                        'product_id' => $workshopTyre->product_id,
                        'supplier' => $workshopTyre->supplier,
                        'ean' => $workshopTyre->product_ean
                    ]);
                }
            }

            // Delete stock history entries
            DB::table('stock_history')
                ->where('ref_type', 'INV')
                ->where('ref_id', $workshop->id)
                ->delete();
            DB::commit();

            ActivityLogger::log(
                workshopId: $workshop->id,
                action: 'Cancelled Workshop',
                description: 'Cancelled Workshop ID: ' . $workshop->id,
                changes: [
                    'workshop_id' => $workshop->id,
                    'workshop_status' => $workshop->status,
                    'balance amount' => $workshop->balance_price
                ]
            );

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Order Cancelled Successfully!');
            $this->sendOrderConfirmationEmail($workshopId);
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error("Error voiding workshop/invoice", [
                'workshop_id' => $workshopId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'An error occurred while voiding: ' . $e->getMessage());
            return redirect()->back();
        }
    }

       protected function sendOrderConfirmationEmail($orderId)
    {
        $nameRule = new NoTestCustomer();
        $emailRule = new NoTestCustomer();
        
        $workshop = Workshop::findOrFail($orderId);
        $customer_name = $workshop->name . ' ' . $workshop->last_name;
        $email = $workshop->email;
        if (
            !$nameRule->passes('customer_name', $customer_name) ||
            !$emailRule->passes('email',  $email)
        ) {
            Log::info('Skipping email due to spam-like customer details.', [
                'customer_name' => $customer_name,
                'email' => $email,
            ]);
            return true;
        }

        try {
            $customer = [
                'customer_name' => $customer_name,
                'email' => $email,
            ];

            $garage = GarageDetails::first();
            $garageEmail = $garage?->email;

            Mail::to($customer['email'])->send(new SendMailToCustomer($orderId, $customer, $garage));

            $ownerEmail = 'info@digitalideasltd.co.uk';
            Mail::to($ownerEmail)->send(new OrderToGarage($orderId, $customer, $garage));

            if ($garageEmail) {
                Mail::to($garageEmail)->send(new OrderToGarage($orderId, $customer, $garage));
            } else {
                Log::warning('Garage email not found.', ['email' => $garageEmail]);
            }

        } catch (\Exception $e) {
            Log::error('Error during email submission.', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function vehicles()
    {
        $customer = Auth::guard('customer')->user();
        $vehicles = $customer->vehicles()->paginate(10);
        return view('customer.vehicles', compact('vehicles'));
    }

    public function createVehicle()
    {
        return view('customer.vehicle.vehicle-form');
    }

    public function editVehicle($id)
    {
        $customer = Auth::guard('customer')->user();
        $vehicle = $customer->vehicles()->findOrFail($id);

        return view('customer.vehicle.vehicle-form', compact('vehicle'));
    }

    // Store a new vehicle
    public function storeVehicle(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validatedData = $request->validate([
            'vehicle_vehicle_reg_number' => 'required|string|max:20',
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
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);

        $vehicle = VehicleDetail::where('vehicle_reg_number', $validatedData['vehicle_reg_number'])->first();

        if ($vehicle) {
            $vehicle->update($validatedData);
        } else {
            $vehicle = VehicleDetail::create($validatedData);
        }
        if (!$customer->vehicles()->where('vehicle_detail_id', $vehicle->id)->exists()) {
            $customer->vehicles()->attach($vehicle->id);
        }

        return redirect()->route('customer.vehicles')->with('success', 'Vehicle added/updated successfully!');
    }


    // Update the vehicle details
    public function updateVehicle(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();

        $vehicle = VehicleDetail::where('id', $id)->first();
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
            'vehicle_torque_settings' => 'nullable|string|max:50',
            'vehicle_mot_expiry_date' => 'nullable|date',
        ]);


        // Update the vehicle details
        $vehicle->update($validatedData);

        return redirect()->route('customer.vehicles')->with('success', 'Vehicle updated successfully!');
    }

    public function deleteVehicle($id)
    {
        $customer = Auth::guard('customer')->user();
        $vehicle = $customer->vehicleDetails()->findOrFail($id);
        $customer->vehicles()->detach($vehicle->id);

        return redirect()->route('customer.vehicles')->with('success', 'Vehicle removed successfully!');
    }

    public function invoices()
    {
        $customer = Auth::guard('customer')->user();
        $invoices = $customer->invoices()->where('is_void', 0)->orderBy('id', 'desc')->paginate(10);
        $unpaidCount = $customer->invoices()->where('status', 'Unpaid')->where('is_void', 0)->count();
        $paidCount = $customer->invoices()->where('status', 'Paid')->where('is_void', 0)->count();
        $overdueCount = $customer->invoices()->where('status', 'Overdue')->where('is_void', 0)->count();
        $partiallyPaidCount = $customer->invoices()->where('status', 'Partially Paid')->where('is_void', 0)->count();

        return view('customer.invoices', compact('invoices', 'unpaidCount', 'paidCount', 'overdueCount', 'partiallyPaidCount'));
    }

    public function viewInvoice($id)
    {
        // dd($id);
        $customer = Auth::guard('customer')->user();

        // Fetch the workshop (acting as the order) created by the logged-in customer
        $invoices = Invoice::where('customer_id', $customer->id)
            ->where('workshop_id', $id)
            ->where('is_void', 0)
            ->firstOrFail();
        // dd($invoices);
        // If the workshop is not found, show an error
        if (!$invoices) {
            return redirect()->route('customer.orders')->with('error', 'You are not authorized to view this order.');
        }

        // Fetch related data
        $vehicle = $invoices->vehicle; // Vehicle associated with the workshop

        // Fetch products from WorkshopTyre table using workshop_id
        $WorkshopTyre = WorkshopTyre::where('workshop_id', $invoices->workshop_id)->where('is_void', 0)->get();
        // dd($items);
        // Fetch services from WorkshopService table using workshop_id
        $WorkshopService = WorkshopService::where('workshop_id', $invoices->workshop_id)->where('is_void', 0)->get();
        $WorkshopVehicle = DB::table('vehicle_details')
            ->where('vehicle_reg_number', $invoices->vehicle_reg_number)
            ->get();
        $paymentHistory = DB::table('customer_debit_logs')
            ->where('workshop_id', $invoices->workshop_id)->get();

        // Calculate totals
        $total_service_price = $WorkshopService->sum('service_price');
        $total_product_price = $WorkshopTyre->sum('price');
        $total_Tax_Amount = $invoices->tax_amount;
        $grandTotal = $invoices->grand_total;
        $discount_price = $invoices->discount_price;
        $paid_price = $invoices->paid_price;
        $balancePrice = $grandTotal - ($invoices->installment_payment + $paid_price + $discount_price);

        return view('customer.invoice.invoice-details', compact(
            'invoices',
            'vehicle',
            'WorkshopTyre',
            'paymentHistory',
            'WorkshopVehicle',
            'WorkshopService',
            'total_service_price',
            'total_product_price',
            'total_Tax_Amount',
            'grandTotal',
            'discount_price',
            'paid_price',
            'balancePrice'
        ));
    }

    public function statements(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $query = Invoice::where('customer_id', $customer->id)->where('is_void', 0)->orderBy('created_at', 'asc');

        if ($request->filled('from') && $request->filled('to')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay()->toDateString();
                $to = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay()->toDateString();
                $query->whereBetween('created_at', [$from, $to]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format. Please use DD-MM-YYYY.'], 400);
            }
        }

        $invoices = $query->get();
        $transactions = collect();
        if (!$invoices->isEmpty()) {
            foreach ($invoices as $invoice) {
                $transactions->push([
                    'date' => $invoice->created_at->format('d-m-Y'),
                    'details' => 'Invoice #' . $invoice->workshop_id,
                    'type' => 'Invoice',
                    'amount' => $invoice->grandTotal,
                    'paid_price' => $invoice->paid_price,
                    'balance_price' => $invoice->balance_price
                ]);
            }
        } else {
        }

        $totalInvoiced = $invoices->sum('grandTotal');
        $totalPaid = $invoices->sum('paid_price');
        $balanceDue = $totalInvoiced - $totalPaid;

        if ($request->ajax()) {
            return response()->json([
                'totalInvoiced' => $totalInvoiced,
                'totalPaid' => $totalPaid,
                'balanceDue' => $balanceDue,
                'transactionsHtml' => $transactions
            ]);
        }

        return view('customer.statement', compact(
            'customer',
            'totalInvoiced',
            'totalPaid',
            'balanceDue',
            'transactions'
        ));
    }
}