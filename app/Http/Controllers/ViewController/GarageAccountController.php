<?php

namespace App\Http\Controllers\ViewController;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\garage;
use Carbon\Carbon;

use App\Models\Invoice;
use App\Models\VehicleDetail;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMailToCustomer;
use App\Models\WorkshopTyre;
use App\Models\WorkshopService;
use DB;
use Illuminate\Support\Str;
use App\Models\RegionCounty;
use App\Http\Controllers\Controller;



class GarageAccountController extends Controller
{

    public function myAccount()
    {
        // Get the authenticated garage user
        $garages = Auth::guard('garage')->user();
        $counties = RegionCounty::where('status', 1)->get();
        
        return view('garage.myaccount', compact('garages', 'counties'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'garage_name' => 'required|string|min:2|max:255',
            'garage_email' => 'required|email|unique:garages,garage_email,' . Auth::guard('garage')->id(),
            'garage_mobile' => 'required|string|min:10|max:15',
            'garage_phone' => 'nullable|string|min:10|max:15',
            'garage_street' => 'nullable|string|max:255',
            'garage_city' => 'nullable|string|max:100',
            'garage_zone' => 'nullable|string|max:100',
            'garage_country' => 'nullable|string|max:100',
            'garage_company_number' => 'nullable|string|max:50',
            'garage_vat_number' => 'nullable|string|max:50',
            'garage_eori_number' => 'nullable|string|max:20',
            'garage_order_types' => 'nullable|string|max:500',
        ]);

        $garage = Auth::guard('garage')->user();
        $garage->update($request->only([
            'garage_name',
            'garage_email',
            'garage_mobile',
            'garage_phone',
            'garage_street',
            'garage_city',
            'garage_zone',
            'garage_country',
            'garage_company_number',
            'garage_vat_number',
            'garage_eori_number',
            'garage_garage_opening_time',
            'garage_social_facebook',
            'garage_social_instagram',
            'garage_social_twitter',
            'garage_social_youtube',
            'garage_google_map_link',
            'garage_longitude',
            'garage_latitude',
            'garage_google_reviews_link',
            'garage_google_reviews_stars',
            'garage_google_reviews_count',
            'garage_website_url',
            'garage_description',
            'garage_notes',
            'garage_order_types',
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $garage = Auth::guard('garage')->user();

        if (!Hash::check($request->current_password, $garage->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $garage->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    // Garage Dashboard
    public function orders()
    {
        $garage = Auth::guard('garage')->user();

       $workshops = Workshop::where('garage_id', $garage->id)
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

        return view('garage.orders', compact('workshops'));
    }

    public function viewOrder($id)
    {
        $garage = Auth::guard('garage')->user();

        $workshop = Workshop::where('garage_id', $garage->id)
    ->where(function ($query) {
        $query->where('is_void', 0)
              ->orWhere(function ($q) {
                  $q->where('is_void', 1)
                    ->where('status', 'cancelled');
              });
    })
    ->findOrFail($id);
        if (!$workshop) {
            return redirect()->route('garage.orders')->with('error', 'You are not authorized to view this order.');
        }

        $vehicle = $workshop->vehicle;
        $WorkshopTyre = WorkshopTyre::where('workshop_id', $workshop->id)->where('is_void', 0)->get();
        $WorkshopService = WorkshopService::where('workshop_id', $workshop->id)->where('is_void', 0)->get();
        $WorkshopVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $workshop->vehicle_reg_number)
                ->get();
        // Calculate totals
        $total_service_price = $WorkshopService->sum('service_price');
        $total_product_price = $WorkshopTyre->sum('price');
        $total_Tax_Amount = $workshop->tax_amount;
        $grandTotal = $workshop->grand_total;
        $discount_price = $workshop->discount_price;
        $paid_price = $workshop->paid_price;
        $balancePrice = $grandTotal - ($workshop->installment_payment + $paid_price + $discount_price);

        return view('garage.order.order-details', compact(
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

public function verifyJob(Request $request, Workshop $order)
{
    $request->validate([
        'code' => 'required|string',
    ]);

    if ($order->verification_code === strtoupper($request->code)) {
        $order->status = 'completed';
        $order->verified_at = now();
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order verified successfully!'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid verification code.'
    ]);
}

public function resendCode(Request $request, Workshop $order)
{
    $order->verification_code = Str::upper(Str::random(4));
    $order->save();
    $customer = [
        'customer_name' => $order->name,
        'email' => $order->email,
    ];

    Mail::to($customer['email'])->send(new VerifyMailToCustomer($order, $customer, null));

    return response()->json([
        'success' => true,
        'code' => $order->verification_code,
        'message' => 'New verification code sent to customer.'
    ]);
}

    public function invoices()
    {
        $garage = Auth::guard('garage')->user();
        $invoices = $garage->invoices()->where('is_void', 0)->paginate(10);
        // Calculate invoice counts for each status
        $unpaidCount = $garage->invoices()->where('payment_status', '0')->where('is_void', 0)->count();
        $paidCount = $garage->invoices()->where('payment_status', '1')->where('is_void', 0)->count();
        $overdueCount = $garage->invoices()->where('payment_status', '4')->where('is_void', 0)->count();
        $partiallyPaidCount = $garage->invoices()->where('payment_status', '3')->where('is_void', 0)->count();

        return view('garage.invoices', compact('invoices', 'unpaidCount', 'paidCount', 'overdueCount', 'partiallyPaidCount'));
    }

    public function viewInvoice($id)
    {
        $garage = Auth::guard('garage')->user();

        $invoices = Invoice::where('garage_id', $garage->id)
            ->where('workshop_id', $id)
            ->where('is_void', 0)
            ->firstOrFail();
        if (!$invoices) {
            return redirect()->route('garage.orders')->with('error', 'You are not authorized to view this order.');
        }

        // Fetch related data
        $vehicle = $invoices->vehicle;
        $WorkshopTyre = WorkshopTyre::where('workshop_id', $invoices->workshop_id)->where('is_void', 0)->get();
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

        return view('garage.invoice.invoice-details', compact(
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
    $garages = Auth::guard('garage')->user();

    $query = Invoice::where('garage_id', $garages->id)
        ->where('is_void', 0)
        ->orderBy('created_at', 'asc');

    if ($request->filled('from') && $request->filled('to')) {
        try {
            $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format. Please use DD-MM-YYYY.'], 400);
        }
    }

    $invoices = $query->get();
    $transactions = $invoices->map(function ($invoice) {
        return [
            'date'          => $invoice->created_at->format('d-m-Y'),
            'details'       => 'Invoice #' . $invoice->workshop_id,
            'type'          => 'Invoice',
            'amount'        => $invoice->grandTotal ?? 0,
            'paid_price'    => $invoice->paid_price ?? 0,
            'balance_price' => $invoice->balance_price ?? 0,
        ];
    });

    $totalInvoiced = $invoices->sum('grandTotal');
    $totalPaid     = $invoices->sum('paid_price');
    $balanceDue    = $totalInvoiced - $totalPaid;
    if ($request->ajax()) {
        return response()->json([
            'totalInvoiced'   => $totalInvoiced,
            'totalPaid'       => $totalPaid,
            'balanceDue'      => $balanceDue,
            'transactions'    => $transactions,
        ]);
    }

    return view('garage.statement', compact(
        'garages',
        'totalInvoiced',
        'totalPaid',
        'balanceDue',
        'transactions'
    ));
}

}