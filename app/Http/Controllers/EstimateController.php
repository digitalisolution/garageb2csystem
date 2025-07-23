<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use DB;
use PDF;
use App\Models\WorkshopTyre;
use App\Models\tyre_brands;
use App\Models\TyresProduct;
use App\Models\Estimate;
use App\Models\Workshop;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\HeaderLink;
use App\Mail\InvoiceEmail;
use App\Models\WorkshopService;
use App\Models\Customer;
use App\Models\CustomerDebitLog;
use App\Models\VehicleDetail;
use App\Models\CarService;
use App\Models\RegionCounty;
use App\Models\CustomerVehicle;
use App\Models\PaymentHistory;
use App\Services\PaymentHistoryService;

class EstimateController extends Controller
{
    protected $paymentHistoryService;
    public function __construct(PaymentHistoryService $paymentHistoryService)
    {
        $this->middleware('auth');
        $this->paymentHistoryService = $paymentHistoryService;
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
    }

    public function save(Request $request, $id = null)
    {   
        // Prepare initial view data
        $viewData['pageTitle'] = 'Add Estimate';
        // $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['tyre_width'] = TyresProduct::pluck('tyre_width', 'product_id');
        $viewData['tyre_profile'] = TyresProduct::pluck('tyre_profile', 'product_id');
        $viewData['service_data'] = CarService::pluck('name', 'service_id');
        $viewData['tyre_brand_name'] = tyre_brands::pluck('name', 'brand_id');
        // $viewData['ServiceType'] = ServiceType::pluck('service_type_name', 'id');
        $viewData['registered_vehicle_select'] = VehicleDetail::pluck('vehicle_reg_number', 'vehicle_reg_number');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['counties'] = RegionCounty::where('status', 1)->get();
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $viewData['brands'] = tyre_brands::where('status', 1)->get();

        // For editing an existing workshop
        if (isset($id) && $id != null) {
            $estimate = Estimate::findOrFail($id);
            $getFormAutoFillup = $estimate->toArray();

            // Get vehicle registration number from workshop data
            $vehicleRegNumber = $estimate->vehicle_reg_number;

            $viewData = [
                'workshopTyreData' => WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'estimate')->get(),
                'workshopServiceData' => WorkshopService::where('workshop_id', $id)->where('ref_type', 'estimate')->get(),
                'workshopVehicleData' => VehicleDetail::where('vehicle_reg_number', $vehicleRegNumber)->get()
            ];
            $viewData['counties'] = RegionCounty::where('status', 1)->get();
            $viewData['brands'] = tyre_brands::where('status', 1)->get();
            // dd($viewData);
            return view('AutoCare.estimate.add', $viewData)->with($getFormAutoFillup);
        }

        // For adding new data or updating existing 3
        if ($request->isMethod('post')) {
            // dd($request);
            try {
                // \Log::info("Starting save operation...");

                // Validate tyre data
                if (empty($request->input('due_in')) && empty($request->input('due_out'))) {
                    \Log::warning("booking date missing. Workshop not created.");
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Please add Due in and Due out before saving the estimate.');
                    return redirect()->back()->withInput();
                }
                $addressComponents = [
                    $request->input('shipping_address_street'),
                    $request->input('shipping_address_city'),
                    $request->input('shipping_address_postcode'),
                    $request->input('shipping_address_county'),
                    $request->input('shipping_address_country'),
                ];

                // Filter out empty values and join with commas
                $fullAddress = implode(", ", array_filter($addressComponents));
                $saveAndSyncWorkshop = $request->has('save_and_sync_Workshop');
                // Data for updating an existing workshop
                if ($request->has('id') && $request->id != null) {
                    $existingEstimate = Estimate::find($request->id);

                    if (!$existingEstimate) {
                        return redirect()->back()->with('message.level', 'danger')->with('message.content', 'Workshop not found.');
                    }

                    // Get the paid price from the existing workshop
                    $paidPrice = $existingEstimate->paid_price + $existingEstimate->discount_price;
                    $OnlypaidPrice = $existingEstimate->paid_price;

                    // Update existing workshop
                    $PartyManage = $request->only([
                        'name',
                        'mobile',
                        'email',
                        'status',
                        'company_name',
                        'reference',
                        'payment_status',
                        'is_complete',
                        'estimate_date',
                        'vehicle_reg_number',
                        'make',
                        'model',
                        'customer_id',
                        'mileage',
                        'payment_method',
                        'notes',
                        'due_in',
                        'due_out'
                    ]);
                    // $PartyManage['address'] = $fullAddress;
                    $PartyManage['address'] = $request->shipping_address_street;
                    $PartyManage['city'] = $request->shipping_address_city;
                    $PartyManage['zone'] = $request->shipping_address_postcode;
                    $PartyManage['county'] = $request->shipping_address_county;
                    $PartyManage['country'] = $request->shipping_address_country;
                    $PartyManage['grandTotal'] = $request->grand_total;
                    $PartyManage['year'] = $request->first_registered;
                    $PartyManage['balance_price'] = $request->grand_total - $paidPrice;
                    $PartyManage['fitting_type'] = $request->fitting_type ?? 'fully_fitted';

                    // Logic to determine payment_status
                    if ($PartyManage['balance_price'] == 0 && $paidPrice == $request->grand_total) {
                        // If balance is 0 and paid price equals grand total, set payment_status to 1 (Paid)
                        $PartyManage['payment_status'] = 1;
                    } elseif ($PartyManage['balance_price'] > 0 && $PartyManage['balance_price'] < $request->grand_total && $OnlypaidPrice > 0) {
                        // If balance is not 0 but some amount has been paid, set payment_status to 3 (Partially Paid)
                        $PartyManage['payment_status'] = 3;
                    } elseif ($PartyManage['balance_price'] == $request->grand_total || $PartyManage['balance_price'] == $request->balance_price+$existingEstimate->discount_price) {
                        // If no payment has been made, set payment_status to 0 (Unpaid)
                        $PartyManage['payment_status'] = 0;
                    }

                    // Check if payment_status is "1" (paid)
                    if ($PartyManage['payment_status'] == 1) {
                        $PartyManage['is_complete'] = 1;
                        $PartyManage['status'] = 'completed';
                    }
                    $PartyManage['is_read'] = 1;
                    
                    // Update the workshop record
                    if (Estimate::whereId($request->id)->update($PartyManage)) {
                        Booking::where('workshop_id', $request->id)->forceDelete();
                        WorkshopService::where('workshop_id', $request->id)->where('ref_type', 'estimate')->forceDelete();
                        $this->saveWorkshopData($request, $request->id);
                        if ($saveAndSyncWorkshop) {
                            $this->convertToWorkshop($request->id);
                        }
                    }
                } else {
                    // Add a new workshop
                    $PartyManage = $request->only([
                        'name',
                        'mobile',
                        'email',
                        'status',
                        'company_name',
                        'reference',
                        'payment_status',
                        'is_complete',
                        'estimate_date',
                        'vehicle_reg_number',
                        'make',
                        'model',
                        'year',
                        'customer_id',
                        'mileage',
                        'payment_method',
                        'notes',
                        'due_in',
                        'due_out'
                    ]);
                    // $PartyManage['address'] = $fullAddress;
                    $PartyManage['address'] = $request->shipping_address_street;
                    $PartyManage['city'] = $request->shipping_address_city;
                    $PartyManage['zone'] = $request->shipping_address_postcode;
                    $PartyManage['county'] = $request->shipping_address_county;
                    $PartyManage['country'] = $request->shipping_address_country;
                    $PartyManage['grandTotal'] = $request->grand_total;
                    $PartyManage['year'] = $request->first_registered;
                    $PartyManage['estimate_origin'] = 'Admin';
                    $PartyManage['fitting_type'] = $request->fitting_type ?? 'fully_fitted';

                    // Check if payment_status is "1" (paid)
                    if ($request->payment_status == 1) {
                        // $PartyManage['paid_price'] = $request->grand_total; // Move balanceprice to paid_price
                        // $PartyManage['balance_price'] = $request->grand_total - $request->paid_price; // Reset balanceprice to 0
                        $PartyManage['is_complete'] = 1;
                        $PartyManage['status'] = 'completed';

                    } else {
                        $PartyManage['balance_price'] = $request->grand_total;
                    }
                    
                    $PartyManage['is_read'] = 1;

                    $newWorkshop = Estimate::create($PartyManage);

                    if ($newWorkshop) {
                        // Save new product and service data
                        $this->saveWorkshopData($request, $newWorkshop->id);
                        if ($saveAndSyncWorkshop) {
                            $this->convertToWorkshop($newWorkshop->id);
                        }
                    }
                }

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Estimate saved successfully!');
                return redirect('/AutoCare/estimate/search');
            } catch (\Exception $e) {
                \Log::error("Error saving Estimate: " . $e->getMessage());
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'An error occurred while saving the Estimate! Please fill all mandatory fields');
            }
        }

        return view('AutoCare.estimate.add', $viewData);
    }
    private function saveWorkshopData($request, $estimateId)
    {
       // Save Tire Data
       if ($request->has('product_id') && $request->product_id[0] != null) {
        // Get all existing tyre IDs for the workshop
        $existingTyreIds = WorkshopTyre::where('workshop_id', $estimateId)->where('ref_type', 'estimate')->pluck('id')->toArray();
    
        // Track tyre IDs that are part of the current request
        $updatedTyreIds = [];
    
        foreach ($request->product_id as $index => $productId) {
            $itemId = $request->item_id[$index] ?? null;
            $tyreProduct = TyresProduct::find($productId);
    
            if (!$tyreProduct) {
                \Log::warning("Tyre product with ID $productId not found.");
                continue; // Skip if tyre product is not found
            }
    
            $requestedQuantity = $request->tyre_quantity[$index] ?? 1;
    
            // Add new item or update existing item
            $estimateTyre = $itemId ? WorkshopTyre::where('ref_type', 'estimate')->find($itemId) : new WorkshopTyre();
            if (!$estimateTyre) {
                $estimateTyre = new WorkshopTyre();
                $estimateTyre->ref_type = 'estimate';
            }

            if ($itemId && $estimateTyre->exists) {
                $oldQuantity = $estimateTyre->quantity;
                $tyreProduct->tyre_quantity += $oldQuantity;
            }
    
            // Update tyre details
            $estimateTyre->workshop_id = $estimateId;
            $estimateTyre->product_id = $productId;
            $estimateTyre->product_ean = $request->tyre_ean[$index] ?? null;
            $estimateTyre->product_sku = $request->tyre_sku[$index] ?? null;
            $estimateTyre->supplier = $request->tyre_supplier_name ?? null;
            $estimateTyre->product_type = $request->product_type ?? null;
            $estimateTyre->description = $request->tyre_description[$index] ?? null;
            $estimateTyre->quantity = $requestedQuantity;
            $estimateTyre->cost_price = $request->cost_price[$index] ?? 0;
            $estimateTyre->shipping_postcode = $request->callout_postcode ?? null;
            $estimateTyre->shipping_price = $request->callout_charges ?? 0;
            $estimateTyre->shipping_tax_id = $request->callout_vat ?? 0;
            $estimateTyre->fitting_type = $request->fitting_type ?? 'fully_fitted';
            $estimateTyre->ref_type = 'estimate';
            $estimateTyre->margin_rate = $request->margin_rate[$index] ?? 0;
            $estimateTyre->tax_class_id = $request->tyre_vat[$index] ?? 0;
            $estimateTyre->price = $request->tyre_amount[$index] ?? 0;
            $estimateTyre->save();
            // Deduct the requested quantity from the tyre's stock
            $tyreProduct->tyre_quantity -= $requestedQuantity;
            $tyreProduct->save();
    
            // Track the tyre ID as part of the current request
            if ($itemId) {
                $updatedTyreIds[] = $itemId;
            }
        }
    
        // Remove tyres that are no longer part of the request
        $tyresToRemove = array_diff($existingTyreIds, $updatedTyreIds);
        if (!empty($tyresToRemove)) {
            $removedTyres = WorkshopTyre::whereIn('id', $tyresToRemove)->where('ref_type', 'estimate')->get();
    
            foreach ($removedTyres as $removedTyre) {
                // Find the tyre product in the tyres_product table using product_id, ean, and sku for perfect matching
                    $tyreProduct = TyresProduct::where(function ($query) use ($removedTyre) {
                        $query->where('product_id', $removedTyre->product_id)
                            ->Where('tyre_supplier_name', $removedTyre->supplier)
                            ->Where('tyre_ean', $removedTyre->product_ean);
                })->first();
    
                if ($tyreProduct) {
                    // Restore the quantity of the removed tyre in the tyres_product table
                    $tyreProduct->tyre_quantity += $removedTyre->quantity;
                    $tyreProduct->save();
                }
            }
    
            // Delete the removed tyres from the WorkshopTyre table
            WorkshopTyre::whereIn('id', $tyresToRemove)->where('ref_type', 'estimate')->forceDelete();
        }
    } else {
        // If no tyres are selected, clear all existing tyres

        $removedTyres = WorkshopTyre::where('workshop_id', $estimateId)->where('ref_type', 'estimate')->get();

        foreach ($removedTyres as $removedTyre) {
            // Find the tyre product in the tyres_product table using product_id, ean, and sku for perfect matching
                $tyreProduct = TyresProduct::where(function ($query) use ($removedTyre) {
                    $query->where('product_id', $removedTyre->product_id)
                        ->Where('tyre_supplier_name', $removedTyre->supplier)
                        ->Where('tyre_ean', $removedTyre->product_ean);
            })->first();

            if ($tyreProduct) {
                // Restore the quantity of the removed tyre in the tyres_product table
                $tyreProduct->tyre_quantity += $removedTyre->quantity;
                $tyreProduct->save();
            }
        }

        // Delete all tyres from the WorkshopTyre table
        WorkshopTyre::where('workshop_id', $estimateId)->where('ref_type', 'estimate')->forceDelete();
    }

        // **Save or Update Service Data**
        if ($request->has('service_id') && $request->service_id[0] != null) {
            foreach ($request->service_id as $index => $serviceId) {
                $serviceItemId = $request->service_item_id[$index] ?? null; // Hidden field for existing services
                if ($serviceItemId) {
                    // Update existing service
                    $serviceItem = WorkshopService::where('ref_type', 'estimate')->find($serviceItemId);
                    if ($serviceItem) {
                        $serviceItem->service_id = $serviceId;
                        $serviceItem->service_name = $request->service_name[$index] ?? 'Unknown Service';
                        $serviceItem->fitting_type = $request->fitting_type[$index] ?? 'fully_fitted';
                        $serviceItem->service_quantity = $request->service_quantity[$index] ?? 1;
                        $serviceItem->service_price = $request->service_price[$index] ?? 0;
                        $serviceItem->tax_class_id = $request->service_vat[$index] ?? 0;
                        $serviceItem->save();
                        // \Log::info("Updated service: " . json_encode($serviceItem));
                    }
                } else {
                    // Add new service
                    $serviceItem = new WorkshopService();
                    $serviceItem->workshop_id = $estimateId;
                    $serviceItem->service_id = $serviceId;
                    $serviceItem->service_name = $request->service_name[$index] ?? 'Unknown Service';
                    $serviceItem->fitting_type = $request->fitting_type[$index] ?? 'fully_fitted';
                    $serviceItem->product_type = 'service';
                    $serviceItem->ref_type = 'estimate';
                    $serviceItem->service_quantity = $request->service_quantity[$index] ?? 1;
                    $serviceItem->service_price = $request->service_price[$index] ?? 0;
                    $serviceItem->tax_class_id = $request->service_vat[$index] ?? 0;
                    $serviceItem->save();
                    // \Log::info("Added new service: " . json_encode($serviceItem));
                }
            }
        }

        // **Save Booking Data**
        if ($request->has('due_in') && $request->has('due_out')) {
            // Check if a booking already exists for this workshop
            $booking = Booking::where('workshop_id', $estimateId)->first();
            if ($booking) {
                // Update existing booking
                $booking->title = $request->name ?? 'Workshop Booking';
                $booking->start = $request->due_in;
                $booking->end = $request->due_out;
                $booking->save();
                // \Log::info("Updated Booking: " . json_encode($booking));
            } else {
                // Create new booking
                $booking = new Booking();
                $booking->workshop_id = $estimateId;
                $booking->title = $request->name ?? 'Workshop Booking';
                $booking->start = $request->due_in;
                $booking->end = $request->due_out;
                $booking->save();
                // \Log::info("Created new Booking: " . json_encode($booking));
            }
        }



// Save VehicleDetail Data and manage customer_vehicle relationship
if ($request->has('vehicle_reg_number') && $request->vehicle_reg_number != null) {
    // Check if a vehicle with the same registration number already exists
    $vehicledetails = VehicleDetail::where('vehicle_reg_number', $request->vehicle_reg_number)->first();
    if ($vehicledetails) {
        // Update existing VehicleDetail
        $vehicledetails->vehicle_make = $request->vehicle_make;
        $vehicledetails->vehicle_model = $request->vehicle_model;
        $vehicledetails->vehicle_year = $request->vehicle_first_registered;
        $vehicledetails->vehicle_front_tyre_size = $request->vehicle_front_tyre_size;
        $vehicledetails->vehicle_rear_tyre_size = $request->vehicle_rear_tyre_size;
        $vehicledetails->vehicle_vin = $request->vehicle_vin;
        $vehicledetails->vehicle_cc = $request->vehicle_cc;
        $vehicledetails->vehicle_engine_number = $request->vehicle_engine_number;
        $vehicledetails->vehicle_engine_size = $request->vehicle_engine_size;
        $vehicledetails->vehicle_axle = $request->vehicle_axle;
        $vehicledetails->vehicle_fuel_type = $request->vehicle_fuel_type;
        $vehicledetails->vehicle_mot_expiry_date = $request->vehicle_mot_expiry_date;
        $vehicledetails->save();
        // \Log::info("Updated vehicledetails: " . json_encode($vehicledetails));
    } else {
        // Create new VehicleDetail if not found
        $vehicledetails = new VehicleDetail();
        $vehicledetails->vehicle_reg_number = $request->vehicle_reg_number;
        $vehicledetails->vehicle_make = $request->vehicle_make;
        $vehicledetails->vehicle_model = $request->vehicle_model;
        $vehicledetails->vehicle_year = $request->vehicle_first_registered;
        $vehicledetails->vehicle_front_tyre_size = $request->vehicle_front_tyre_size;
        $vehicledetails->vehicle_rear_tyre_size = $request->vehicle_rear_tyre_size;
        $vehicledetails->vehicle_vin = $request->vehicle_vin;
        $vehicledetails->vehicle_cc = $request->vehicle_cc;
        $vehicledetails->vehicle_engine_number = $request->vehicle_engine_number;
        $vehicledetails->vehicle_engine_size = $request->vehicle_engine_size;
        $vehicledetails->vehicle_axle = $request->vehicle_axle;
        $vehicledetails->vehicle_fuel_type = $request->vehicle_fuel_type;
        $vehicledetails->vehicle_mot_expiry_date = $request->vehicle_mot_expiry_date;
        $vehicledetails->save();
        // \Log::info("Created new vehicledetails: " . json_encode($vehicledetails));
    }

    // Save customer_vehicle relationship if customer_id is present
    if ($request->has('customer_id') && $request->customer_id != null) {
        $customerId = $request->customer_id;
        $vehicleDetailId = $vehicledetails->id; // Get the ID of the vehicle detail

        // Check if the relationship already exists
        $existingRelation = CustomerVehicle::where('customer_id', $customerId)
            ->where('vehicle_detail_id', $vehicleDetailId)
            ->first();

        if (!$existingRelation) {
            // Create a new customer_vehicle entry
            $customerVehicle = new CustomerVehicle();
            $customerVehicle->customer_id = $customerId;
            $customerVehicle->vehicle_detail_id = $vehicleDetailId;
            $customerVehicle->save();
            // \Log::info("Created new customer_vehicle relationship: " . json_encode($customerVehicle));
        }
    }
}
        // \Log::info("Workshop data saved successfully for ID: $estimateId.");
    }
    public function searchCustomers(Request $request)
    {
        $query = $request->input('q'); // Get the search term
    
        // Search for customers by name or company name
        $customers = Customer::where('customer_name', 'like', '%' . $query . '%')
            ->orWhere('company_name', 'like', '%' . $query . '%')
            ->select('id', 'customer_name', 'company_name') // Select only the required fields
            ->get();
    
        // Format the response
        $results = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->customer_name . ' - ' . $customer->company_name, // Combine name and company
            ];
        });
    
        return response()->json($results);
    }
    public function validateTyreStock($productId)
    {
        try {
            // Fetch the tyre product from the database
            $tyreProduct = TyresProduct::find($productId);

            if (!$tyreProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tyre product not found.',
                ]);
            }

            // Return the available stock
            return response()->json([
                'success' => true,
                'message' => 'Stock is sufficient.',
                'available' => $tyreProduct->tyre_quantity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating stock.',
            ]);
        }
    }
    public function convertToWorkshop($id)
{
    try {
        $estimate = Estimate::findOrFail($id);

        // If already converted and workshop exists, update it
        if ($estimate->is_converted_to_workshop && $estimate->workshop_id) {
            $existingWorkshop = Workshop::find($estimate->workshop_id);
            if ($existingWorkshop) {
                $estimateData = $estimate->toArray();
                unset($estimateData['id'], $estimateData['created_at'], $estimateData['updated_at']);
                $existingWorkshop->update(array_merge(
                    $estimateData,
                    [
                        'estimate_origin' => 'Estimate',
                        'updated_at' => now(),
                    ]
                ));
                 $estimate->update([
                    'status' => 'completed',
                ]);
                return redirect()->back()->with('success', 'Workshop updated from estimate.');
            }
        }

        // Create new Workshop
        $estimateData = $estimate->toArray();
        unset($estimateData['id'], $estimateData['created_at'], $estimateData['updated_at']);

        $newWorkshop = Workshop::create(array_merge(
            $estimateData,
            [
                'estimate_origin' => 'Estimate', // static value
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ));

        // Update estimate with workshop_id and flag
        $estimate->update([
            'workshop_id' => $newWorkshop->id,
            'is_converted_to_workshop' => 1,
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Estimate successfully converted to workshop.');

    } catch (\Exception $e) {
        \Log::error("Error converting estimate to workshop (ID: $id): " . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to convert: ' . $e->getMessage());
    }
    }
    public function view(Request $request)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();
    
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
    
        // Always build query based on request query parameters
        $estimateQuery = Estimate::whereNull('deleted_at');
    
        // Apply filters from request (both GET and POST)
        if ($request->filled('id')) {
            $estimateQuery->where('id', $request->id);
        }
        if ($request->filled('customer_id')) {
            $estimateQuery->where('customer_id', $request->customer_id);
        }
        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $estimateQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                      ->orWhere('company_name', 'like', $searchTerm);
            });
        }
        if ($request->filled('created_at_from')) {
            $estimateQuery->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $estimateQuery->whereDate('created_at', '<=', $request->created_at_to);
        }
        if ($request->filled('mobile')) {
            $estimateQuery->where('mobile', 'like', '%' . $request->mobile . '%');
        }
        if ($request->filled('email')) {
            $estimateQuery->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('origin')) {
            $estimateQuery->where('estimate_origin', 'like', '%' . $request->origin . '%');
        }
        if ($request->filled('status')) {
            $estimateQuery->where('status', 'like', '%' . $request->status . '%');
        }
        if ($request->filled('payment_method')) {
            $estimateQuery->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }
        if ($request->filled('payment_status')) {
            $estimateQuery->where('payment_status', 'like', '%' . $request->payment_status . '%');
        }
        if ($request->filled('vehicle_reg_number_for_search')) {
            $estimateQuery->where('vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
        }
        if ($request->filled('year')) {
            $estimateQuery->where('year', $request->year);
        }
    
        // Sorting
        $estimateQuery->orderBy('id', 'desc');
    
        // Paginate
        $estimateResults = $estimateQuery->paginate(10)->appends($request->except('page'));
    
        // Pass data to view
        $viewData['estimate'] = $estimateResults;
    
        // Set title
        $viewData['pageTitle'] = 'Estimate Details';
    
        // If POST, get form input for repopulating fields
        $formAutoFillup = $request->isMethod('post') ? $request->all() : $request->query();
    
        return view('AutoCare.estimate.search', $viewData, $formAutoFillup);
    }
    public function viewpaymenthistory($id)
    {
    // Check if the job_id exists in payment_histories
    $check = DB::table('payment_histories')
        ->where('job_id', '=', $id)
        ->exists();

    if (!$check) {
        return redirect()->back()->with('error', 'No payment history found for this estimate.');
    }

    // Fetch payment history with customer details and debit logs
    $all_view = DB::table('payment_histories')
        ->join('workshops', 'workshops.id', '=', 'payment_histories.job_id') // Join workshops table
        ->leftJoin('customers', 'customers.id', '=', 'workshops.customer_id') // Left join customers table
        ->leftJoin('customer_debit_logs', 'customer_debit_logs.payment_history_id', '=', 'payment_histories.id') // Directly join debit logs
        ->where('payment_histories.job_id', '=', $id) // Filter by job_id
        ->select(
            'payment_histories.*',
            'customers.*',
            'workshops.*',
            'workshops.id as workshop_id',
            'workshops.name as workshop_name',
            'customers.id as customer_id',
            DB::raw('COALESCE(customers.customer_name, workshops.name) as customer_name'),
            DB::raw('COALESCE(customers.customer_address, workshops.address) as customer_address'),
            DB::raw('COALESCE(customers.customer_contact_number, workshops.mobile) as customer_contact_number'),
            DB::raw('COALESCE(customers.customer_email, workshops.email) as customer_email'),
            'customer_debit_logs.id as debit_log_id',
            'customer_debit_logs.debit_amount',
            'customer_debit_logs.payment_type'
        )
        ->get();

    // Debug the output
    // dd($all_view);

    // Convert to an array
    $viewData['AdminSaleView'] = json_decode(json_encode($all_view), true);
    return view('AutoCare.estimate.payment_history', $viewData);
    }

    public function trash(Request $request, $id)
    {
        // Fetch header links for the view
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        // Start a database transaction
        DB::beginTransaction();
        try {
            // Find the workshop by ID
            $estimate = Estimate::findOrFail($id);

            if (!$estimate) {
                throw new \Exception("Workshop not found.");
            }

            // Roll back tyre quantities in the tyres_product table
            $estimateTyre = WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'estimate')->get();

            foreach ($estimateTyre as $item) {
                // First, try to find the tyre product using product_id
                $tyreProduct = TyresProduct::find($item->product_id);

                // If product_id does not match, try matching by ean, sku, or both
                if (!$tyreProduct) {
                    $tyreProduct = TyresProduct::where(function ($query) use ($item) {
                        $query->where('tyre_ean', $item->product_ean)
                            ->where('tyre_supplier_name',$item->supplier)
                            ->orWhere('tyre_sku', $item->product_sku)
                            ->orWhere(function ($subQuery) use ($item) {
                                $subQuery->where('tyre_ean', $item->product_ean)
                                        ->where('tyre_sku', $item->product_sku);
                            });
                    })->first();
                }

                // If a matching tyre product is found, roll back the quantity
                if ($tyreProduct) {
                    $tyreProduct->tyre_quantity += $item->quantity; // Add back the quantity
                    $tyreProduct->save();
                } else {
                    // Log a warning if no matching tyre product is found
                    \Log::warning("Tyre product not found for item: " . json_encode($item));
                }
            }

            // Delete related data
            Booking::where('workshop_id', $id)->delete(); // Delete booking data
            WorkshopService::where('workshop_id', $id)->where('ref_type', 'estimate')->delete();
            WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'estimate')->delete();

            // Delete the invoice if it exists
            // $estimate = Invoice::where('workshop_id', $id)->first();
            // if ($estimate) {
            //     $estimate->delete();
            // }

            // Delete customer debit logs associated with the workshop
            CustomerDebitLog::where('workshop_id', $id)->delete();

            // Delete payment history associated with the workshop
            PaymentHistory::where('job_id', $id)->delete();

            // Finally, delete the workshop itself
            $estimate->delete();

            // Commit the transaction
            DB::commit();

            // Flash success message
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Workshop was Deleted!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error
            \Log::error("Error trashing workshop ID: $id", ['error' => $e->getMessage()]);

            // Flash error message
            session()->flash('status', ['danger', 'Operation Failed!']);
        }

        // Prepare view data
        $viewData['pageTitle'] = 'Workshop';
        $viewData['workshop'] = Estimate::paginate(10);

        // Return the search view
        return redirect()->back();
    }
    public function trashedList()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        $TrashedParty = Estimate::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.estimate.delete', compact('TrashedParty', 'TrashedParty'));

    }
    public function permanemetDelete(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        if (($id != null) && (Estimate::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', "Workshop was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }

        $TrashedParty = Estimate::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.estimate.delete', compact('TrashedParty', 'TrashedParty'));
    }
    public function viewIndivisual($id)
    {
        // Fetch header links
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();
    
        // Fetch workshop details
        $estimate = Estimate::whereId($id)->first(); // Keep as an object
    
        if ($estimate) {
            // Format discount based on type
            if ($estimate->discount_type === 'percentage' && $estimate->discount_value > 0 ) {
                $formattedDiscount = '('.$estimate->discount_value . '%)';
            } elseif ($estimate->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }
    
            // Add formatted discount to the workshop object
            $estimate->formatted_discount = $formattedDiscount;
    
            // Fetch related data
            $estimateTyre = DB::table('workshop_tyres')
                ->select(
                    'workshop_tyres.*',
                    'workshop_tyres.description',
                    'workshop_tyres.quantity',
                    'workshop_tyres.tax_class_id',
                    'workshop_tyres.fitting_type as orderType',
                    'workshop_tyres.price as TyreWorkshopPrice',
                    'workshop_tyres.product_ean as product_ean',
                    'workshop_tyres.supplier as tyre_source',
                    'workshop_tyres.price as UnitExitPrice',
                    'workshop_tyres.tax_class_id as ProductVat'
                )
                ->where('workshop_id', $estimate->id)
                ->get();
    
            $estimateService = DB::table('workshop_services')
                ->where('workshop_id', $estimate->id)
                ->get();
            $paymentHistory = $this->paymentHistoryService->getPaymentHistory($id);
            $estimateVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $estimate->vehicle_reg_number)
                ->get();
            // Pass data to the view
            $viewData['estimateTyre'] = $estimateTyre;
            $viewData['estimateService'] = $estimateService;
            $viewData['estimateVehicle'] = $estimateVehicle;
            $viewData['estimate'] = $estimate; // Pass the workshop object
            $viewData['estimateId'] = $estimate->id;
            $viewData['paymentHistory'] = $paymentHistory;
            return view('AutoCare.estimate.view', $viewData);
        } else {
            // Handle case where no workshop is found
            return redirect()->back()->with('error', 'Workshop not found.');
        }
    }
    public function viewByWorkshop($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $getIndivisualWorkshopDetail = Estimate::whereId($id)->first()->toArray();
        // $estimateProduct = DB::table('workshop_products')
        //     ->join('products', 'products.id', '=', 'workshop_products.product_id')
        // ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->get();
        $estimateTyre = DB::table('workshop_tyres')
            ->join('tyres_product', 'tyres_product.product_id', '=', 'workshop_tyres.product_id')
            ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->get();

        // $estimateService = DB::table('workshop_services')
        //     ->join('services', 'services.id', '=', 'workshop_services.service_id')
        //     ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->get();
        // $viewData['WorkshopProduct'] = $estimateProduct;
        $viewData['WorkshopTyre'] = $estimateTyre;
        // $viewData['WorkshopService'] = $estimateService;
        $viewData['workshopId'] = "";
        return view('AutoCare.estimate.view', $viewData)->with($getIndivisualWorkshopDetail);
    }
    public function sendEstimateEmail(Request $request)
    {
        // Validate the request
        $request->validate([
            'estimate_id' => 'required',
            'email_to' => 'required|email',
            'email_cc' => 'nullable|email',
            'attach_pdf' => 'nullable|boolean',
            'email_body' => 'nullable|string',
        ]);

        // Fetch invoice details
        $estimate = Estimate::where('id', $request->id)->firstOrFail();
        $estimateTyreData = WorkshopTyre::where('workshop_id', '=', $estimate->id)->where('ref_type', 'estimate')->get();
        $estimateServiceData = WorkshopService::where('workshop_id', '=', $estimate->id)->where('ref_type', 'estimate')->get();
        $estimateVehicleData = VehicleDetail::where('vehicle_reg_number','=', $estimate->vehicle_reg_number)->get();
        $paymentHistory = DB::table('customer_debit_logs')->where('workshop_id',  $estimate->id)->get();
        if ($estimate) {
            // Format discount based on type
            if ($estimate->discount_type === 'percentage' && $estimate->discount_value > 0) {
                $formattedDiscount = '(' . $estimate->discount_value . '%)';
            } elseif ($estimate->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the estimate object
            $estimate->formatted_discount = $formattedDiscount;
        }else{
            $estimate->formatted_discount = "No Data Found";
        }
        // Fetch and sanitize garage name for folder path
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_'); // Convert to a safe folder name

        // Define the PDF path dynamically
        $pdfPath = "estimates/{$safeGarageName}/EST-{$estimate->id}.pdf";

        // Generate the PDF dynamically and save it
        $pdfContent = PDF::loadView('emails.estimate-pdf', compact('estimate', 'workshopServiceData','workshopVehicleData', 'workshopTyreData','paymentHistory'))->output();
        Storage::disk('public')->put($pdfPath, $pdfContent);

        // Full path for attachment
        $pdfFullPath = storage_path("app/public/{$pdfPath}");

        // Ensure PDF exists before attaching
        if ($request->attach_pdf && !Storage::disk('public')->exists($pdfPath)) {
            return redirect()->back()->withErrors('PDF generation failed.');
        }

        // Send the email with or without PDF attachment
        Mail::to($request->email_to)
            ->cc($request->email_cc)
            ->send(new InvoiceEmail(
                $estimate,
                $request->email_body,
                $request->attach_pdf ? $pdfFullPath : null
            ));
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Estimate email sent successfully!');

        return redirect()->back();
    }
    public function previewEstimatePdf($id)
    {
        // Fetch the estimate details
        $estimate = Estimate::where('id', $id)->firstOrFail();
        if ($estimate) {
            // Format discount based on type
            if ($estimate->discount_type === 'percentage' && $estimate->discount_value > 0) {
                $formattedDiscount = '(' . $estimate->discount_value . '%)';
            } elseif ($estimate->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the estimate object
            $estimate->formatted_discount = $formattedDiscount;
        }else{
            $estimate->formatted_discount = "No Data Found";
        }
        // Fetch and sanitize garage name
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_'); // Make it a safe folder name

        // Define the correct PDF path dynamically
        $pdfPath = "estimates/{$safeGarageName}/EST-{$estimate->id}.pdf";

        // Ensure the PDF exists, if not, regenerate it
        if (!Storage::disk('public')->exists($pdfPath)) {
            Log::error("PDF not found at path: {$pdfPath}, generating new PDF.");
            $this->generateEstimatePdf($id); // Regenerate PDF if missing
        }

        // Final check: If PDF still doesn't exist, return 404
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Estimate PDF not found.');
        }

        // Return the PDF as an inline response
        return response()->file(storage_path("app/public/{$pdfPath}"), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Estimate.pdf"',
        ]);
    }
    public function downloadEstimatePdf($id)
    {
        // Fetch the estimate details
        $estimate = Estimate::where('id', $id)->firstOrFail();

        // Define the file path
        $pdfPath = "estimates/{$estimate->workshop_id}.pdf";

        // Check if the file exists
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'PDF not found.');
        }

        // Return the PDF as a downloadable response
        return response()->download(storage_path("app/public/{$pdfPath}"), 'estimate.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
    public function generateEstimatePdf($estimateId)
    {
        // Fetch the Estimate details
        $estimate = Estimate::where('id', $estimateId)->firstOrFail();
        $estimateTyreData = WorkshopTyre::where('workshop_id', '=', $estimateId)->where('ref_type', 'estimate')->get();
        $estimateServiceData = WorkshopService::where('workshop_id', '=', $estimateId)->where('ref_type', 'estimate')->get();
        $estimateVehicleData = VehicleDetail::where('vehicle_reg_number','=', $estimate->vehicle_reg_number)->get();
        $paymentHistory = DB::table('customer_debit_logs')->where('workshop_id',   $estimateId)->get();
        if ($estimate) {
            // Format discount based on type
            if ($estimate->discount_type === 'percentage' && $estimate->discount_value > 0) {
                $formattedDiscount = '(' . $estimate->discount_value . '%)';
            } elseif ($estimate->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the Estimate object
            $estimate->formatted_discount = $formattedDiscount;
        }else{
            $estimate->formatted_discount = "No Data Found";
        }
        // Fetch garage details and sanitize the garage name for safe file paths
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_'); // Convert to a safe folder name

        // Generate the PDF content
        $pdfContent = PDF::loadView('emails.estimate-pdf', compact('estimate', 'estimateServiceData','estimateVehicleData', 'estimateTyreData','paymentHistory'))->output();

        // Define the file path dynamically based on garage name
        $pdfPath = "estimates/{$safeGarageName}/EST-{$estimate->id}.pdf";

        // Ensure the directory exists before saving
        Storage::disk('public')->makeDirectory("estimates/{$safeGarageName}");

        // Save the PDF to the storage/app/public directory
        Storage::disk('public')->put($pdfPath, $pdfContent);

        // Return the full path to the saved PDF
        return storage_path("app/public/{$pdfPath}");
    }

}
