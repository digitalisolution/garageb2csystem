<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\Service;
use App\Models\Brand;
use App\Models\Modal;
use App\Models\ServiceName;
use App\Models\SupplierDebitLog;
use App\Models\Customer;
use App\Models\CustomerDebitLog;
use App\Models\VehicleDetail;
use App\PurchaseReturn;
use Auth;
use App\Models\TyresProduct;
use App\Models\CustomerVehicle;
use App\Models\Workshop;
use App\Models\Invoice;
use App\Models\WorkshopProduct;
use App\Models\ReturnSpareLog;
use App\Models\PaymentHistory;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function getProduct(Request $request)
    {
        $productId = $request->productId;
        return Product::whereId($productId)->first()->toArray();
    }

    // public function getTyreProducts(Request $request)
    // {
    //     // Fetch tyre products from the database
    //     $tyreProducts = TyresProduct::all(); // You can filter this based on search, or categories

    //     // Return the tyre products as a JSON response
    //     return response()->json(['tyre_products' => $tyreProducts]);
    // }

    public function getProductForworkshop(Request $request)
    {
        $product_id = $request->product_id;
        // DB::enableQueryLog();
        $purchase = Product::where('id', $product_id)->where('stock_in', '>', 0)
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take(1)
            ->get();
        // $laQuery = DB::getQueryLog();
        // DB::disableQueryLog();
        $purchase = json_decode(json_encode($purchase), true);
        return $purchase;
    }

    // public function getCustomerForWorkshop(Request $request)
    // {
    //     $customer_id = $request->customer_id;
    //     DB::enableQueryLog();
    //     $purchase = Customer::where('id', $customer_id)
    //         ->orderBy('id', 'DESC')
    //         ->skip(0)
    //         ->take(1)
    //         ->get();
    //     $laQuery = DB::getQueryLog();
    //     DB::disableQueryLog();
    //     $purchase = json_decode(json_encode($purchase), true);
    //     return $purchase;
    // }
    public function getCustomerForWorkshop(Request $request)
    {
        $customer_id = $request->customer_id;

        // Enable Query Log (for debugging)
        DB::enableQueryLog();

        // Fetch customer with county and country names
        $customer = Customer::select(
            'customers.*',
            'region_county.name as shipping_address_county',
            'countries.name as shipping_address_country'
        )
            ->leftJoin('region_county', 'region_county.zone_id', '=', 'customers.shipping_address_county')
            ->leftJoin('countries', 'countries.country_id', '=', 'customers.shipping_address_country')
            ->where('customers.id', $customer_id)
            ->orderBy('customers.id', 'DESC')
            ->first(); // Fetch only one record

        // Log the executed query (optional for debugging)
        // Log::info('Executed Query: ', DB::getQueryLog());

        // Disable Query Log
        DB::disableQueryLog();

        // Return as an array
        return $customer ? $customer->toArray() : [];
    }


    public function markAsRead()
    {
        Workshop::where('is_read', false)->update(['is_read' => true]);
        return redirect()->back()->with('success', 'All bookings marked as read.');
    }
    public function markAsReadSingle($id)
    {
        $booking = Workshop::findOrFail($id);
        $booking->is_read = true;
        $booking->save();

        return response()->json(['success' => true]);
    }


    public function createCustomerForWorkshop(Request $request)
    {

        // Handle POST request for creating a new customer
        if ($request->isMethod('post')) {
            try {
                // Validate the request (name is required)
                $validated = $request->validate([
                    // Customer Details
                    'customer_name' => 'required|string|min:2|max:50', // First Name (Required)
                    'customer_last_name' => 'nullable|string|min:2|max:50', // Last Name
                    'customer_email' => 'nullable|email', // Email Address
                    'customer_contact_number' => 'nullable|string|min:10|max:15', // Telephone
                    'customer_alt_number' => 'nullable|string|min:10|max:15', // Alternate Telephone
                    'company_name' => 'nullable|string|max:100', // Company Name
                    'company_website' => 'nullable|url', // Company Website

                    // Billing Address
                    'billing_address_street' => 'nullable|string|min:3|max:100', // Street
                    'billing_address_city' => 'nullable|string|min:3|max:50', // City
                    'billing_address_postcode' => 'nullable|string|min:3|max:10', // Postcode
                    'billing_address_county' => 'nullable|string', // County
                    'billing_address_country' => 'nullable|string', // Country

                    // Shipping Address
                    'shipping_address_street' => 'nullable|string|min:3|max:100', // Street
                    'shipping_address_city' => 'nullable|string|min:3|max:50', // City
                    'shipping_address_postcode' => 'nullable|string|min:3|max:10', // Postcode
                    'shipping_address_county' => 'nullable|string', // County
                    'shipping_address_country' => 'nullable|string', // Country
                ]);

                // Create a new customer instance
                $customer = new Customer();
                $customer->fill($validated); // Fill the model with validated data
                $customer->created_by = Auth::user()->id; // Set the creator

                // Save the customer to the database
                if ($customer->save()) {
                    // Set success message
                    $request->session()->flash('message.level', 'success');
                    $request->session()->flash('message.content', 'Customer created successfully!');
                    $request->session()->flash('message.icon', 'check');

                    // Optionally, return the customer ID in the response
                    return response()->json([
                        'success' => true,
                        'message' => 'Customer created successfully!',
                        'customer' => $customer,
                    ]);
                } else {
                    // Set error message
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Something went wrong while creating the customer.');
                    $request->session()->flash('message.icon', 'times');

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create customer.',
                    ], 500);
                }
            } catch (\Exception $e) {
                // Log the error and return an error message
                \Log::error('Error creating customer: ' . $e->getMessage());

                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'An error occurred while creating the customer.');
                $request->session()->flash('message.icon', 'times');

                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }
        }

        // If not a POST request, return an error response
        return response()->json([
            'success' => false,
            'message' => 'Invalid request method.',
        ], 400);
    }


    // public function getPurchase(Request $request)
    // {
    //     $product_id = $request->product_id;
    //     DB::enableQueryLog();
    //     $purchase = Purchase::where('product_id', $product_id)
    //         ->orderBy('id', 'DESC')
    //         ->skip(0)
    //         ->take(1)
    //         ->get();
    //     $laQuery = DB::getQueryLog();
    //     //  print_r($laQuery);
    //     DB::disableQueryLog();
    //     $purchase = json_decode(json_encode($purchase), true);
    //     return $purchase;
    //     // ->first()->toArray();
    // }
    // public function getService(Request $request)
    // {
    //     $service_id = $request->service_id;
    //     $serviceDetail = Service::whereId($service_id)
    //         ->orderBy('id', 'DESC')
    //         ->skip(0)
    //         ->take(1)
    //         ->get();
    //     $serviceDetail = json_decode(json_encode($serviceDetail), true);
    //     return $serviceDetail;
    // }
    // public function getModal(Request $request)
    // {
    //     $brandId = $request->brand;
    //     $allModalList = Modal::where('brand_id', $brandId)->get();
    //     return json_encode($allModalList);
    // }
    // public function getServiceThroughServiceId(Request $request)
    // {
    //     $service_type_id = $request->service_type_id;
    //     $allServiceList = ServiceName::where('service_type_id', $service_type_id)->get();
    //     return json_encode($allServiceList);
    // }
    // public function getServiceTypeForWorkshop(Request $request)
    // {
    //     $service_type = $request->service_type;
    //     $brand = $request->brand;
    //     $model = $request->model;
    //     $SericeName = DB::table('services')
    //         ->where('service_type', '=', $service_type)
    //         ->where('brand_name', '=', $brand)
    //         ->where('model_name', '=', $model)
    //         ->select('services.*')->get();
    //     return json_encode($SericeName);
    // }
    // public function getServiceTypeForWorkshopThroughModel(Request $request)
    // {
    //     $model_number = $request->model_number;
    //     $brand = $request->brand;
    //     $SericeTypeName = DB::table('services')->join("service_types", "service_types.id", "=", "services.service_type")
    //         ->where('brand_name', '=', $brand)
    //         ->where('model_name', '=', $model_number)
    //         ->select('service_types.id', 'service_types.service_type_name')
    //         ->distinct()
    //         ->get();
    //     return json_encode($SericeTypeName);
    // }
    // public function getProductThroughModelAndBrand(Request $request)
    // {
    //     $model_number = $request->model_number;
    //     $brand = $request->brand;
    //     $Product = DB::table('products')
    //         ->where('company_name', '=', $brand)
    //         ->where('model_number', '=', $model_number)
    //         ->where('products.deleted_at', '=', null)
    //         ->distinct()
    //         ->get();
    //     return json_encode($Product);

    // }
    public function submitSupplierDetail(Request $request)
    {
        $supplierId = $request->supplierId;
        $creditDebit = $request->creditDebit;
        $amount = $request->amount;
        $payment_type = $request->payment_type;
        $comments = $request->comments;
        $saveSupplierDebitLog = new SupplierDebitLog;
        $saveSupplierDebitLog->purchase_invoice_id = 0;
        $saveSupplierDebitLog->supplier_id = $supplierId;
        $saveSupplierDebitLog->purchase_id = 0;
        $saveSupplierDebitLog->created_at = $request->payment_date;
        $saveSupplierDebitLog->is_debit = $creditDebit;
        if ($request->creditDebit == 0) {
            $saveSupplierDebitLog->credit = $amount;
        } else {
            $saveSupplierDebitLog->debit_amount = $amount;
        }
        // $saveSupplierDebitLog->credit = $amount;
        $saveSupplierDebitLog->comments = $comments;
        $saveSupplierDebitLog->payment_type = $payment_type;
        if ($saveSupplierDebitLog->save()) {
            return 1;
        } else {
            return 0;
        }

    }
    public function submitCustomerPaymentDetail(Request $request)
    {
        // dd($request);
        $customerId = $request->customerId;
        $creditDebit = $request->creditDebit;
        $amount = $request->amount;
        $payment_type = $request->payment_type;
        $comments = $request->comments;
        $saveSupplierDebitLog = new CustomerDebitLog;
        $saveSupplierDebitLog->customer_id = $customerId;
        $saveSupplierDebitLog->workshop_id = 0;
        $saveSupplierDebitLog->payment_date = $request->payment_date;
        if ($request->creditDebit == 0) {
            $saveSupplierDebitLog->credit = $amount;
        } else {
            $saveSupplierDebitLog->debit_amount = $amount;
        }
        $saveSupplierDebitLog->is_debit = $creditDebit;

        $saveSupplierDebitLog->comments = $comments;
        $saveSupplierDebitLog->payment_type = $payment_type;
        if ($saveSupplierDebitLog->save()) {
            //  Mail::to($request->email)->send(new SendMailToCustomer($PartyManage->id));
            return 1;
        } else {
            return 0;
        }

    }

    public function getCustomersByVehicle(Request $request)
    {
        // Validate the request
        $request->validate([
            'vehicle_reg_number' => 'required|string',
        ]);

        // Find the vehicle by registration number
        $vehicle = VehicleDetail::where('vehicle_reg_number', $request->vehicle_reg_number)->first();

        if (!$vehicle) {
            return response()->json(['error' => 'Vehicle not found'], 404);
        }

        // Get customers related to the vehicle through the customer_vehicle table
        $customers = CustomerVehicle::where('vehicle_detail_id', $vehicle->id)
            ->with('customer') // Assuming you have a relationship defined in the model
            ->get()
            ->pluck('customer');

        // Return the list of customers
        return response()->json($customers);
    }

    public function GetVehicleDetailFromWorkshop(Request $request)
    {
        $registered_vehicle = $request->registered_vehicle;
        return VehicleDetail::where('vehicle_reg_number', '=', $registered_vehicle)->get();
    }
    public function GetVehicleRegFromWorkshop(Request $request)
    {
        // Step 1: Validate the request to ensure customer_id is present
        if (!$request->has('customer_id') || empty($request->customer_id)) {
            return response()->json(['error' => 'Customer ID is required'], 400);
        }

        // Step 2: Retrieve vehicle_detail_ids associated with the customer_id from the customer_vehicle table
        $vehicleDetailIds = DB::table('customer_vehicle')
            ->where('customer_id', $request->customer_id)
            ->pluck('vehicle_detail_id');

        // Step 3: Retrieve vehicle details from the vehicle_details table using the retrieved vehicle_detail_ids
        $vehicleDetails = DB::table('vehicle_details')
            ->whereIn('id', $vehicleDetailIds)
            ->get();

        // Step 4: Return the vehicle details as a JSON response
        return response()->json($vehicleDetails);
    }

    public function getTyreProducts(Request $request)
    {
        // Fetch tyre products from the database
        $tyreProducts = TyresProduct::all(); // You can filter this based on search, or categories

        // Return the tyre products as a JSON response
        return response()->json(['tyre_products' => $tyreProducts]);
    }

    public function submitPurchaseReturn(Request $request)
    {
        $purchaseDetail = Purchase::whereId($request->PurchaseId)->first()->toArray();
        $unit_price = $purchaseDetail['unit_price'];
        $quantity = $purchaseDetail['quantity'];
        $total_amount = $purchaseDetail['total_amount'];
        $gst = $purchaseDetail['gst'];
        $product_id = $purchaseDetail['product_id'];
        $getProductDetail = Product::whereId($product_id)->first()->toArray();
        $stock_in1 = $getProductDetail['stock_in'];
        $stock_out1 = $getProductDetail['stock_out'];
        $available = $stock_in1 - $stock_out1;
        if ($available >= $request->quantity) {
            if ($quantity >= $request->quantity) {
                $purchaseManame['quantity'] = $quantity - $request->quantity;
                $purchaseManame['total_amount'] = $total_amount - ($total_amount / $quantity) * $request->quantity;
                $purchaseManame['is_returned'] = 1;
                Purchase::where([['id', '=', $request->PurchaseId]])->update($purchaseManame);

                $getProductDetail = Product::whereId($product_id)->first()->toArray();
                $stock_in = $getProductDetail['stock_in'];
                $stock_available = $getProductDetail['stock_available'];

                $productManage['stock_in'] = $stock_in - $request->quantity;
                $productManage['stock_available'] = $stock_available - $request->quantity;
                Product::where([['id', '=', $product_id]])->update($productManage);

                $PurchaseReturn = new PurchaseReturn();
                $PurchaseReturn->user_id = Auth::user()->id;
                $PurchaseReturn->comments = $request->comments;
                $PurchaseReturn->purchase_id = $request->PurchaseId;
                $PurchaseReturn->quantity = $request->quantity;
                if ($PurchaseReturn->save()) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }


    }
    public function getWorkshopReport(Request $request)
    {
        $workshopId = $request->workshopId;
        $WorkshopProduct = DB::table('workshop_products')
            ->join('products', 'products.id', '=', 'workshop_products.product_id')
            ->select('workshop_products.id as WorkshopProId', 'workshop_products.workshop_id', 'products.product_name', 'workshop_products.product_quantity', 'workshop_products.product_price as ProductWorkshopPrice', 'products.hsn as ProductHsn', 'products.unit_price_exit as UnitExitPrice', 'products.gst as ProductGst')
            ->where('workshop_id', $workshopId)->get();
        return json_encode($WorkshopProduct);
    }
    public function submitSaleReturn(Request $request)
    {
        $WorkshopDetail = WorkshopProduct::where('id', '=', $request->saleId)->first()->toArray();
        $workshop_id = $WorkshopDetail['workshop_id'];
        $product_quantity = $WorkshopDetail['product_quantity'];
        // $product_price=$WorkshopDetail['product_price'];
        $product_id = $WorkshopDetail['product_id'];
        if ($product_quantity > $request->quantity) {
            $purchaseManame['product_quantity'] = $product_quantity - $request->quantity;
            // $purchaseManame['total_amount']=$total_amount-($total_amount/$quantity)*$request->quantity;
            $purchaseManame['is_returned'] = 1;
            WorkshopProduct::where([['id', '=', $request->saleId]])->update($purchaseManame);

            $getProductDetail = Product::whereId($product_id)->first()->toArray();
            $stock_out = $getProductDetail['stock_out'];
            $stock_available = $getProductDetail['stock_available'];
            $productManage['stock_out'] = $stock_out - $request->quantity;
            $productManage['stock_available'] = $stock_available - $request->quantity;
            Product::where([['id', '=', $product_id]])->update($productManage);
            $SaledDetail = new ReturnSpareLog();
            $SaledDetail->user_id = Auth::user()->id;
            $SaledDetail->comments = $request->comments;
            $SaledDetail->job_id = $workshop_id;
            $SaledDetail->job_id = $workshop_id;
            $SaledDetail->quantity = $request->quantity;
            if ($SaledDetail->save()) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }

    }
    public function paymentForWorkshop(Request $request)
    {
        // Extract input data
        $creditDebitForWorkshop = $request->creditDebitForWorkshop;
        $workshopIdForPayment = $request->workshopIdForPayment;
        $amountForWorkshop = $request->amountForWorkshop;
        $payment_dateForWorkshop = $request->payment_dateForWorkshop;
        $payment_typeForWorkshop = $request->payment_typeForWorkshop;
        $commentsForWorkshop = $request->commentsForWorkshop;

        // Fetch workshop details
        $workshopDetail = DB::table('workshops')
            ->leftJoin('customers', 'workshops.customer_id', '=', 'customers.id')
            ->where('workshops.id', '=', $workshopIdForPayment)
            ->select('customers.id as customer_id', 'installmentPayment', 'grandTotal', 'paid_price', 'discount_price', 'balance_price')
            ->first();

        if (!$workshopDetail) {
            return "Workshop not found.";
        }

        // Validate payment amount
        if ($amountForWorkshop <= 0) {
            return "Payment Already Paid or Incorrect Amount";
        }
        if ($workshopDetail->grandTotal < $amountForWorkshop) {
            return "Payment amount cannot exceed Grand Total.";
        }
        if ($workshopDetail->balance_price < $amountForWorkshop) {
            return "Payment amount cannot exceed Balance.";
        }

        // Store old values for logging
        $oldBalancePrice = $workshopDetail->balance_price;
        $oldPaidPrice = $workshopDetail->paid_price;
        $oldPaymentStatus = DB::table('workshops')->where('id', $workshopIdForPayment)->value('payment_status');
        $oldWorkshopStatus = DB::table('workshops')->where('id', $workshopIdForPayment)->value('status');

        // Determine payment status
        $paymentStatus = 0; // Default: Unpaid
        $workshopStatus = 'pending';
        if ($amountForWorkshop == $workshopDetail->grandTotal || $amountForWorkshop == $workshopDetail->balance_price) {
            $paymentStatus = 1; // Fully Paid
            $workshopStatus = 'completed';
        } elseif ($amountForWorkshop > 0 && $amountForWorkshop < $workshopDetail->grandTotal) {
            $paymentStatus = 3; // Partially Paid
            $workshopStatus = 'booked';
        }

        // Update workshop installment payment and payment status
        Workshop::where('id', $workshopIdForPayment)
            ->update([
                'balance_price' => $workshopDetail->balance_price - $amountForWorkshop,
                'paid_price' => $amountForWorkshop + $workshopDetail->paid_price,
                'payment_status' => $paymentStatus,
                'status' => $workshopStatus,
            ]);
        
        // Get updated workshop details for logging
        $updatedWorkshop = Workshop::find($workshopIdForPayment);

        Invoice::where('workshop_id', $workshopIdForPayment)
            ->update([
                'balance_price' => $workshopDetail->balance_price - $amountForWorkshop,
                'paid_price' => $amountForWorkshop + $workshopDetail->paid_price,
                'payment_status' => $paymentStatus,
                'status' => $workshopStatus,
            ]);

        // Log payment history
        $PaymentHistory = new PaymentHistory();
        $PaymentHistory->job_id = $workshopIdForPayment;
        $PaymentHistory->payment_date = $payment_dateForWorkshop;
        $PaymentHistory->payment_amount = $amountForWorkshop;
        $PaymentHistory->save();

        // Capture the payment history ID
        $paymentHistoryId = $PaymentHistory->id;

        // Log customer debit/credit
        $saveSupplierDebitLog = new CustomerDebitLog();
        $saveSupplierDebitLog->customer_id = $workshopDetail->customer_id;
        $saveSupplierDebitLog->workshop_id = $workshopIdForPayment;
        $saveSupplierDebitLog->payment_history_id = $paymentHistoryId;
        $saveSupplierDebitLog->payment_date = $payment_dateForWorkshop;

        if ($creditDebitForWorkshop == 0) {
            $saveSupplierDebitLog->credit = $amountForWorkshop;
        } else {
            $saveSupplierDebitLog->debit_amount = $amountForWorkshop;
        }

        $saveSupplierDebitLog->is_debit = $creditDebitForWorkshop;
        $saveSupplierDebitLog->comments = $commentsForWorkshop;
        $saveSupplierDebitLog->payment_type = $payment_typeForWorkshop;

        if ($saveSupplierDebitLog->save()) {
            // Activity log for successful payment
            ActivityLogger::log(
                workshopId: $workshopIdForPayment,
                action: 'Workshop Payment Added',
                description: 'Payment recorded for workshop ID: ' . $workshopIdForPayment,
                changes: [
                    'amount' => $amountForWorkshop,
                    'payment_date' => $payment_dateForWorkshop,
                    'payment_type' => $payment_typeForWorkshop,
                    'old_balance_price' => $oldBalancePrice,
                    'new_balance_price' => $updatedWorkshop->balance_price,
                ]
            );

            return 1; // Success
        } else {
            // Activity log for failed payment
            ActivityLogger::log(
                workshopId: $workshopIdForPayment,
                action: 'Workshop Payment Failed',
                description: 'Failed to record payment for workshop ID: ' . $workshopIdForPayment,
                changes: [
                    'amount' => $amountForWorkshop,
                    'payment_date' => $payment_dateForWorkshop,
                    'payment_type' => $payment_typeForWorkshop,
                    'error' => 'Failed to save customer debit/credit log',
                ]
            );

            return 0; // Failure
        }
    }
    public function updateWorkshopBalance(Request $request)
    {
        if (Workshop::where('id', $request->workshop_id)->update(['balance_price' => $request->balance, 'grandTotal' => $request->grandTotal])) {
            return 1;
        } else {
            return 0;
        }

    }
    // public function discountForWorkshop(Request $request)
    // {
    //     dd($request);
    //     if (Workshop::where('id', $request->workshopIdForDiscount)->update(['discount_price' => $request->amountForWorkshopDiscount])) {
    //         return 1;
    //     } else {
    //         return 0;
    //     }

    // }


    // public function discountForWorkshop(Request $request)
    // {

    //     // Fetch the workshop record to calculate the updated balance_price
    //     $workshop = Workshop::find($request->workshopIdForDiscount);

    //     if ($workshop) {
    //         // Update the discount_price
    //         $workshop->discount_price = $request->amountForWorkshopDiscount;

    //         // Subtract the discount from the balance_price
    //         $workshop->balance_price = $workshop->balance_price - $request->amountForWorkshopDiscount;

    //         // Save the updated values
    //         if ($workshop->save()) {
    //             return 1; // Success
    //         }
    //     }

    //     return 0; // Failure
    // }

    public function discountForWorkshop(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'workshopIdForDiscount' => 'required|integer',
            'discount_type' => 'required|in:amount,percentage',
            'discount_value' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        // Fetch the workshop record
        $workshop = Workshop::find($request->workshopIdForDiscount);

        if (!$workshop) {
            return response()->json(['success' => false, 'message' => 'Workshop not found.'], 404);
        }

        // Store old values for logging
        $oldDiscountType = $workshop->discount_type;
        $oldDiscountValue = $workshop->discount_value;
        $oldDiscountPrice = $workshop->discount_price;
        $oldBalancePrice = $workshop->balance_price;

        // Fetch the corresponding invoice (if it exists)
        $invoice = Invoice::where('workshop_id', $request->workshopIdForDiscount)->first();

        // Step 1: Rollback the previous discount (if any)
        if ($workshop->discount_price > 0) {
            $workshop->balance_price += $workshop->discount_price; // Restore the balance price
            if ($invoice) {
                $invoice->balance_price += $workshop->discount_price; // Restore the invoice balance amount
            }
        }

        // Step 2: Apply the new discount
        $newBalancePrice = max(0, $workshop->balance_price - $request->discount_amount);

        // Ensure the new discount does not reduce the balance price below zero
        if ($newBalancePrice < 0) {
            return response()->json(['success' => false, 'message' => 'Discount exceeds the remaining balance.'], 400);
        }

        // Update the discount fields in the workshop
        $workshop->discount_type = $request->discount_type;
        $workshop->discount_value = $request->discount_value;
        $workshop->discount_price = $request->discount_amount;

        // Update the balance price in the workshop
        $workshop->balance_price = $newBalancePrice;

        // Save the updated workshop
        if (!$workshop->save()) {
            return response()->json(['success' => false, 'message' => 'Failed to apply discount to the workshop.'], 500);
        }

        // Update the corresponding fields in the invoice (if it exists)
        if ($invoice) {
            $invoice->discount_type = $request->discount_type;
            $invoice->discount_value = $request->discount_value;
            $invoice->discount_price = $request->discount_amount;
            $invoice->balance_price = $newBalancePrice;

            if (!$invoice->save()) {
                return response()->json(['success' => false, 'message' => 'Failed to apply discount to the invoice.'], 500);
            }
        }

        // Activity log for discount application
        ActivityLogger::log(
            workshopId: $request->workshopIdForDiscount,
            action: 'Apply Discount',
            description: 'Discount applied to workshop ID: ' . $request->workshopIdForDiscount,
            changes: [
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_amount' => $request->discount_amount,
                'old_discount_type' => $oldDiscountType,
                'old_discount_value' => $oldDiscountValue,
                'old_discount_price' => $oldDiscountPrice,
                'old_workshop_balance_price' => $oldBalancePrice,
                'new_workshop_balance_price' => $workshop->balance_price,
            ]
        );

        // Return success response
        if ($invoice) {
            return response()->json(['success' => true, 'message' => 'Discount applied successfully to both workshop and invoice.']);
        } else {
            return response()->json(['success' => true, 'message' => 'Discount applied successfully to the workshop. Invoice not found.']);
        }
    }
    public function fetchDiscount($id)
    {
        $workshop = Workshop::find($id);

        if (!$workshop) {
            return response()->json(['success' => false, 'message' => 'Workshop not found.'], 404);
        }

        return response()->json([
            'discount_type' => $workshop->discount_type,
            'discount_value' => $workshop->discount_value,
        ]);
    }
    public function getPaymentLogs($workshopId)
    {
        // Fetch payment logs with related data
        $paymentLogs = DB::table('customer_debit_logs')
            ->where('customer_debit_logs.workshop_id', '=', $workshopId)
            ->whereNull('customer_debit_logs.deleted_at') // Exclude soft-deleted entries
            ->select(
                'customer_debit_logs.*',
                'customer_debit_logs.debit_amount',
                'customer_debit_logs.credit',
                'customer_debit_logs.payment_type',
                'customer_debit_logs.comments'
            )
            ->get();

        // Map payment_type to human-readable labels
        $paymentLogs = $paymentLogs->map(function ($log) {
            $log->payment_type_label = match ((int) $log->payment_type) {
                1 => 'By Cash',
                2 => 'By Card',
                3 => 'By Cheque',
                default => 'Unknown',
            };
            return $log;
        });

        return response()->json($paymentLogs);
    }
    public function getPaymentLog($paymentId)
    {
        // Fetch single payment log by ID
        $paymentLog = DB::table('customer_debit_logs')
            ->where('id', '=', $paymentId)
            ->select(
                'debit_amount',
                'credit',
                'payment_type',
                'comments'
            )
            ->first(); // This returns a single object

        if (!$paymentLog) {
            return response()->json(['error' => 'Payment log not found'], 404);
        }

        return response()->json($paymentLog);
    }
    public function updatePaymentLog(Request $request, $paymentLogId)
    {
        // dd($request);
        // Validate input
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        // Fetch the existing payment log
        $paymentLog = CustomerDebitLog::find($paymentLogId);

        if (!$paymentLog) {
            return response()->json(['success' => false, 'message' => 'Payment log not found.'], 404);
        }

        // Fetch the corresponding workshop
        $workshop = Workshop::find($paymentLog->workshop_id);

        if (!$workshop) {
            return response()->json(['success' => false, 'message' => 'Workshop not found.'], 404);
        }

        // Fetch the corresponding invoice (if it exists)
        $invoice = Invoice::where('workshop_id', $workshop->id)->first();

        // Calculate old and new amounts
         // Calculate old and new amounts
        $oldAmount = $paymentLog->debit_amount ?? $paymentLog->credit;
        $newAmount = $request->amount;

        // Update workshop balance and paid price
        $workshop->paid_price = $workshop->paid_price - $oldAmount + $newAmount;
        $workshop->balance_price = $workshop->balance_price + $oldAmount - $newAmount;
        $workshop->balance_price = min($workshop->balance_price, $workshop->grandTotal); // Ensure balance doesn't exceed grand total
        // Set payment status based on paid vs grandTotal
        if ($workshop->paid_price == $workshop->grandTotal) {
            $workshop->payment_status = 1; // Fully Paid
            $workshop->status = 'completed';
        } elseif ($workshop->balance_price < $workshop->grandTotal) {
            $workshop->payment_status = 3; // Partially Paid
            $workshop->status = 'booked';
        } elseif ($workshop->balance_price == $workshop->grandTotal) {
            $workshop->payment_status = 0; // Partially Paid
            $workshop->status = 'booked';
        }
        // dd($workshop);
        $workshop->save();

        // Update the invoice if it exists
        if ($invoice) {
            $invoice->grandTotal = $workshop->grandTotal; // Sync total amount
            $invoice->paid_price = $workshop->paid_price;  // Sync paid amount
            $invoice->balance_price = $workshop->balance_price; // Sync balance amount
            if ($invoice->paid_price == $invoice->grandTotal) {
                $invoice->payment_status = 1; // Fully Paid
                $invoice->status = 'completed';
            } elseif ($invoice->balance_price < $invoice->grandTotal) {
                $invoice->payment_status = 3; // Partially Paid
                $invoice->status = 'booked';
            } elseif ($invoice->balance_price == $invoice->grandTotal) {
                $invoice->payment_status = 0; // Partially Paid
                $invoice->status = 'booked';
            }
            $invoice->save();
        } else {
            \Log::info("Invoice not found for workshop ID: {$workshop->id}. Skipping invoice update.");
        }

        // Update the payment log
        $paymentLog->debit_amount = $newAmount;
        $paymentLog->payment_type = $request->payment_type;
        $paymentLog->payment_date = $request->payment_date;
        $paymentLog->comments = $request->comments;
        $paymentLog->save();

        ActivityLogger::log(
        workshopId: $workshop->id,
        action: 'Update Payment Log',
        description: 'Payment log updated for workshop ID: ' . $workshop->id,
        changes: [
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
        ]
    );


        return response()->json([
            'success' => true,
            'message' => 'Payment log and workshop updated successfully.' . ($invoice ? '' : ' Invoice does not exist and was skipped.'),
        ]);
    }
    public function deletePaymentLog($paymentLogId)
    {
        // Fetch the payment log details
        $paymentLog = CustomerDebitLog::find($paymentLogId);

        if (!$paymentLog) {
            return response()->json(['success' => false, 'message' => 'Payment log not found.'], 404);
        }
        
        $paymentAmount = $paymentLog->debit_amount ?? $paymentLog->credit;
        $workshopId = $paymentLog->workshop_id;
        $workshop = Workshop::find($workshopId);
        $invoice = Invoice::where('workshop_id', $workshopId)->first();
        
        if (!$workshop) {
            return response()->json(['success' => false, 'message' => 'Workshop not found.'], 404);
        }

        // Update workshop balances
        $workshop->paid_price -= $paymentAmount;
        $workshop->balance_price += $paymentAmount;
        $workshop->balance_price = min($workshop->balance_price, $workshop->grandTotal);
        
        if ($workshop->paid_price >= $workshop->grandTotal) {
            $workshop->payment_status = 1; // Fully Paid
            $workshop->status = 'completed';
        } elseif ($workshop->paid_price > 0) {
            $workshop->payment_status = 3; // Partially Paid
            $workshop->status = 'booked';
        } else {
            $workshop->payment_status = 0; // Unpaid
            $workshop->status = 'booked';
        }

        $workshop->save();

        // Update invoice if it exists
        if ($invoice) {
            $invoice->paid_price -= $paymentAmount;
            $invoice->balance_price += $paymentAmount;
            $invoice->balance_price = min($invoice->balance_price, $invoice->grandTotal);
            
            if ($invoice->paid_price >= $invoice->grandTotal) {
                $invoice->payment_status = 1; // Fully Paid
                $invoice->status = 'completed';
            } elseif ($invoice->paid_price > 0) {
                $invoice->payment_status = 3; // Partially Paid
                $invoice->status = 'booked';
            } else {
                $invoice->payment_status = 0; // Unpaid
                $invoice->status = 'booked';
            }
            $invoice->save();
        }

        // Store payment log details before deletion for logging
        $paymentLogDetails = [
            'id' => $paymentLog->id,
            'amount' => $paymentAmount,
            'payment_type' => $paymentLog->payment_type,
            'payment_date' => $paymentLog->payment_date,
            'comments' => $paymentLog->comments,
        ];

        // Delete the payment log
        $paymentLog->forceDelete();

        // Activity log for the deletion
        ActivityLogger::log(
            workshopId: $workshopId,
            action: 'Delete Payment Log',
            description: 'Payment log deleted for workshop ID: ' . $workshopId,
            changes: [
            'amount' => $paymentAmount,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Payment log deleted successfully.']);
    }
    public function getStockHistory(Request $request)
    {
        try {
            $request->validate([
                'ean' => 'required|string|max:255',
                'page' => 'nullable|integer|min:1',
            ]);

            $ean = $request->input('ean');
            $page = $request->input('page', 1);
            $perPage = 10;

            $stockHistory = DB::table('stock_history')
                ->leftJoin('users', 'stock_history.user_id', '=', 'users.id')
                ->where('ean', $ean)
                // ->orderBy('id', 'desc')
                ->orderBy('created_at', 'desc')
                ->select(
                    'stock_history.*',
                    'users.name as user_name'
                )
                ->paginate($perPage, ['*'], 'page', $page);
            return response()->json($stockHistory);
        } catch (\Exception $e) {
            \Log::error('Error fetching stock history:', ['error_message' => $e->getMessage()]);
            return response()->json([]);
        }
    }

}
