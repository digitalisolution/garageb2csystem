<?php

namespace App\Http\Controllers;

use App\Models\WorkshopTyre;
use App\Models\tyre_brands;
use App\Models\TyresProduct;
use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Service;
use DB;
use App\Models\WorkshopProduct;
use App\Models\WorkshopService;
use App\Models\Modal;
// use App\Models\ServiceType;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\HeaderLink;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\CustomerDebitLog;
use App\Models\VehicleDetail;
use App\Models\CarService;
use App\Models\RegionCounty;
use App\Models\CustomerVehicle;
use Illuminate\Support\Str;
use Auth;
use App\Helpers\ActivityLogger;
use App\Models\ActivityLog;
use App\Models\PaymentHistory;
use App\Services\PaymentHistoryService;

class WorkshopController extends Controller
{
    protected $paymentHistoryService;
    public function __construct(PaymentHistoryService $paymentHistoryService)
    {
        $this->middleware('auth');
        $this->paymentHistoryService = $paymentHistoryService;
    }

    public function save(Request $request, $id = null)
    {

        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        // Prepare initial view data
        $viewData['pageTitle'] = 'Add Workshop';
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
            $workshop = Workshop::findOrFail($id);
            $getFormAutoFillup = $workshop->toArray();

            // Get vehicle registration number from workshop data
            $vehicleRegNumber = $workshop->vehicle_reg_number;

            $viewData = [
                'workshopTyreData' => WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->get(),
                'workshopServiceData' => WorkshopService::where('workshop_id', $id)->get(),
                'workshopVehicleData' => VehicleDetail::where('vehicle_reg_number', $vehicleRegNumber)->get()
            ];
            $viewData['counties'] = RegionCounty::where('status', 1)->get();
            $viewData['brands'] = tyre_brands::where('status', 1)->get();
            // dd($viewData);
            return view('AutoCare.workshop.add', $viewData)->with($getFormAutoFillup);
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
                    $request->session()->flash('message.content', 'Please add Due in and Due out before saving the workshop.');
                    return redirect()->back()->withInput();
                }
                // $addressComponents = [
                //     $request->input('shipping_address_street'),
                //     $request->input('shipping_address_city'),
                //     $request->input('shipping_address_postcode'),
                //     $request->input('shipping_address_county'),
                //     $request->input('shipping_address_country'),
                // ];

                // Filter out empty values and join with commas
                // $fullAddress = implode(", ", array_filter($addressComponents));
                $saveAndSyncInvoice = $request->has('save_and_sync_invoice');
                // Data for updating an existing workshop
                if ($request->has('id') && $request->id != null) {
                    $existingWorkshop = Workshop::find($request->id);

                    if (!$existingWorkshop) {
                        return redirect()->back()->with('message.level', 'danger')->with('message.content', 'Workshop not found.');
                    }

                    // Get the paid price from the existing workshop
                    $paidPrice = $existingWorkshop->paid_price + $existingWorkshop->discount_price;
                    $OnlypaidPrice = $existingWorkshop->paid_price;

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
                        'workshop_date',
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
                    } elseif ($PartyManage['balance_price'] == $request->grand_total || $PartyManage['balance_price'] == $request->balance_price + $existingWorkshop->discount_price) {
                        // If no payment has been made, set payment_status to 0 (Unpaid)
                        $PartyManage['payment_status'] = 0;
                    }

                    // Check if payment_status is "1" (paid)
                    if ($PartyManage['payment_status'] == 1) {
                        $PartyManage['is_complete'] = 1;
                        $PartyManage['status'] = 'completed';
                    }

                    $PartyManage['is_read'] = 1;

                    // dd($PartyManage);
                    // Update the workshop record
                    if (Workshop::whereId($request->id)->update($PartyManage)) {
                        $this->saveWorkshopData($request, $request->id);
                        if ($saveAndSyncInvoice) {
                            $this->convertToInvoice($request->id);
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
                        'workshop_date',
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
                    $PartyManage['workshop_origin'] = 'Admin';
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

                    $newWorkshop = Workshop::create($PartyManage);

                    if ($newWorkshop) {
                        // Save new product and service data
                        $this->saveWorkshopData($request, $newWorkshop->id);
                        if ($saveAndSyncInvoice) {
                            $this->convertToInvoice($newWorkshop->id);
                        }
                    }
                }

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', 'Workshop saved successfully!');
                return redirect('/AutoCare/workshop/search');
            } catch (\Exception $e) {
                \Log::error("Error saving workshop: " . $e->getMessage());
                $request->session()->flash('message.level', 'danger');
                $request->session()->flash('message.content', 'An error occurred while saving the workshop! Please fill all mandatory fields');
            }
        }

        return view('AutoCare.workshop.add', $viewData);
    }
    /**
     * Save workshop product, service, and tire data.
     */
    private function saveWorkshopData($request, $workshopId)
    {

        if ($request->has('product_id') && $request->product_id[0] != null) {

            $existingTyreIds = WorkshopTyre::where('workshop_id', $workshopId)->where('ref_type', 'workshop')->pluck('id')->toArray();

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
                $WorkshopTyre = $itemId ? WorkshopTyre::where('ref_type', 'workshop')->find($itemId) : new WorkshopTyre();
                if (!$WorkshopTyre) {
                    $WorkshopTyre = new WorkshopTyre();
                    $WorkshopTyre->ref_type = 'workshop';
                }
                // Adjust inventory: Add back the old quantity before updating (only for existing tyres)
                if ($itemId && $WorkshopTyre->exists) {
                    $oldQuantity = $WorkshopTyre->quantity;
                    $tyreProduct->tyre_quantity += $oldQuantity;
                }

                // Update tyre details
                $WorkshopTyre->workshop_id = $workshopId;
                $WorkshopTyre->product_id = $productId;
                $WorkshopTyre->ref_type = 'workshop';
                $WorkshopTyre->product_ean = $request->tyre_ean[$index] ?? null;
                $WorkshopTyre->product_sku = $request->tyre_sku[$index] ?? null;
                $WorkshopTyre->supplier = $request->tyre_supplier_name ?? null;
                $WorkshopTyre->product_type = $request->product_type ?? null;
                $WorkshopTyre->description = $request->tyre_description[$index] ?? null;
                $WorkshopTyre->quantity = $requestedQuantity;
                $WorkshopTyre->cost_price = $request->cost_price[$index] ?? 0;
                $WorkshopTyre->shipping_postcode = $request->callout_postcode ?? null;
                $WorkshopTyre->shipping_price = $request->callout_charges ?? 0;
                $WorkshopTyre->shipping_tax_id = $request->callout_vat ?? 0;
                $WorkshopTyre->fitting_type = $request->fitting_type ?? 'fully_fitted';
                $WorkshopTyre->margin_rate = $request->margin_rate[$index] ?? 0;
                $WorkshopTyre->tax_class_id = $request->tyre_vat[$index] ?? 0;
                $WorkshopTyre->price = $request->tyre_amount[$index] ?? 0;
                $WorkshopTyre->save();
                // Deduct the requested quantity from the tyre's stock
                $tyreProduct->tyre_quantity -= $requestedQuantity;
                $tyreProduct->save();

                // Track the tyre ID as part of the current request
                if ($itemId) {
                    $updatedTyreIds[] = $itemId;
                }
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: $itemId ? 'Updated Tyre' : 'Added Tyre',
                    description: 'Tyre item ' . ($itemId ? 'updated' : 'added') . ': ' . $tyreProduct->product_name,
                    changes: ['product_id' => $productId, 'quantity' => $requestedQuantity]
                );

            }

            // Remove tyres that are no longer part of the request
            $tyresToRemove = array_diff($existingTyreIds, $updatedTyreIds);
            if (!empty($tyresToRemove)) {
                $removedTyres = WorkshopTyre::whereIn('id', $tyresToRemove)->where('ref_type', 'workshop')->get();

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
                    ActivityLogger::log(
                        workshopId: $workshopId,
                        action: 'Removed Tyre',
                        description: 'Removed tyre product: ' . $removedTyre->product_ean,
                        changes: ['quantity' => $removedTyre->quantity]
                    );

                }

                // Delete the removed tyres from the WorkshopTyre table
                WorkshopTyre::whereIn('id', $tyresToRemove)->where('ref_type', 'workshop')->forceDelete();
            }
        } else {
            // If no tyres are selected, clear all existing tyres

            $removedTyres = WorkshopTyre::where('workshop_id', $workshopId)->where('ref_type', 'workshop')->get();

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
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed Tyre',
                    description: 'Removed tyre product: ' . $removedTyre->product_ean,
                    changes: ['quantity' => $removedTyre->quantity]
                );
            }

            // Delete all tyres from the WorkshopTyre table
            WorkshopTyre::where('workshop_id', $workshopId)->where('ref_type', 'workshop')->forceDelete();
        }

        // **Save or Update Service Data**
            if ($request->has('service_id') && $request->service_id[0] != null) {
                foreach ($request->service_id as $index => $serviceId) {
                     $serviceItemId = $serviceId ?? null;

                    $serviceItem = WorkshopService::where('workshop_id', $workshopId)->where('service_id', $serviceItemId)->where('ref_type', 'workshop')->first();
                // dd( $serviceItem);
                    if ($serviceItem) {
                        $original = $serviceItem->toArray();

                        $serviceItem->service_id = $serviceId;
                        $serviceItem->service_name = $request->service_name[$index] ?? 'Unknown Service';
                        $serviceItem->fitting_type = $request->fitting_type ?? 'fully_fitted';
                        $serviceItem->service_quantity = $request->service_quantity[$index] ?? 1;
                        $serviceItem->service_price = $request->service_price[$index] ?? 0;
                        $serviceItem->tax_class_id = $request->service_vat[$index] ?? 0;

                        $dirty = $serviceItem->getDirty();

                        if (!empty($dirty)) {
                            $changes = [];
                            foreach ($dirty as $field => $newValue) {
                                $changes[$field] = [
                                    'old' => $original[$field] ?? null,
                                    'new' => $newValue
                                ];
                            }

                            $serviceItem->save();

                            ActivityLogger::log(
                                workshopId: $workshopId,
                                action: 'Updated Service',
                                description: 'Service updated: ' . $serviceItem->service_name,
                                changes: $changes
                            );
                        }
                    } else {
                        // Add new service
                        $serviceItem = new WorkshopService();
                        $serviceItem->workshop_id = $workshopId;
                        $serviceItem->ref_type = 'workshop';
                        $serviceItem->product_type = 'service';
                        $serviceItem->service_id = $serviceId;
                        $serviceItem->service_name = $request->service_name[$index] ?? 'Unknown Service';
                        $serviceItem->fitting_type = $request->fitting_type ?? 'fully_fitted';
                        $serviceItem->service_quantity = $request->service_quantity[$index] ?? 1;
                        $serviceItem->service_price = $request->service_price[$index] ?? 0;
                        $serviceItem->tax_class_id = $request->service_vat[$index] ?? 0;
                        $serviceItem->save();

                        ActivityLogger::log(
                            workshopId: $workshopId,
                            action: 'Added Service',
                            description: 'Service added: ' . $serviceItem->service_name,
                            changes: [
                                'service_id' => ['old' => null, 'new' => $serviceId],
                                'quantity' => ['old' => null, 'new' => $serviceItem->service_quantity],
                                'price' => ['old' => null, 'new' => $serviceItem->service_price],
                                'vat' => ['old' => null, 'new' => $serviceItem->tax_class_id],
                            ]
                        );
                    }
                }
            }

        // **Save Booking Data**
            if ($request->has('due_in') && $request->has('due_out')) {
                $booking = Booking::where('workshop_id', $workshopId)->first();
                
                if ($booking) {
                    $original = $booking->toArray();

                    $booking->title = $request->name ?? 'Workshop Booking';
                    $booking->start = $request->due_in;
                    $booking->end = $request->due_out;

                    $dirty = $booking->getDirty();

                    if (!empty($dirty)) {
                        $changes = [];
                        foreach ($dirty as $field => $newValue) {
                            $changes[$field] = [
                                'old' => $original[$field] ?? null,
                                'new' => $newValue
                            ];
                        }

                        $booking->save();

                        ActivityLogger::log(
                            workshopId: $workshopId,
                            action: 'Updated Booking',
                            description: 'Booking updated from ' . ($original['start'] ?? '-') . ' to ' . ($original['end'] ?? '-'),
                            changes: $changes
                        );
                    }

                } else {
                    $booking = new Booking();
                    $booking->workshop_id = $workshopId;
                    $booking->title = $request->name ?? 'Workshop Booking';
                    $booking->start = $request->due_in;
                    $booking->end = $request->due_out;
                    $booking->save();

                    ActivityLogger::log(
                        workshopId: $workshopId,
                        action: 'Created Booking',
                        description: 'Booking from ' . $request->due_in . ' to ' . $request->due_out,
                        changes: [
                            'start' => ['old' => null, 'new' => $request->due_in],
                            'end' => ['old' => null, 'new' => $request->due_out],
                            'title' => ['old' => null, 'new' => $booking->title],
                        ]
                    );
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
                // ActivityLogger::log(
                //     workshopId: $workshopId,
                //     action: isset($vehicledetails->id) ? 'Updated Vehicle' : 'Added Vehicle',
                //     description: 'Vehicle registration: ' . $request->vehicle_reg_number,
                //     changes: ['make' => $request->vehicle_make, 'model' => $request->vehicle_model]
                // );
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
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: isset($vehicledetails->id) ? 'Updated Vehicle' : 'Added Vehicle',
                    description: 'Vehicle registration: ' . $request->vehicle_reg_number,
                    changes: ['make' => $request->vehicle_make, 'model' => $request->vehicle_model]
                );
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
                // ActivityLogger::log(
                //     workshopId: $workshopId,
                //     action: 'Linked Vehicle to Customer',
                //     description: 'Linked customer ID ' . $customerId . ' with vehicle ' . $request->vehicle_reg_number
                // );



            }
        }
        // \Log::info("Workshop data saved successfully for ID: $workshopId.");
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
    public function convertToInvoice($id)
    {
        try {
            $workshop = Workshop::findOrFail($id);
            $existingInvoice = Invoice::where('workshop_id', $workshop->id)->first();

            $workshopData = $workshop->toArray();
            $invoiceData = array_merge($workshopData, [
                'workshop_id' => $workshop->id,
                'updated_at' => now(),
            ]);
            if ($existingInvoice) {
                $existingInvoice->update($invoiceData);
                $workshop->update(['is_converted_to_invoice' => 1]);
                // return redirect()->back();
            } else {
                $invoiceData = array_merge($workshopData, [
                    'workshop_id' => $workshop->id,
                    'created_at' => now(),
                ]);
                $newInvoice = Invoice::create($invoiceData);
                $workshop->update(['is_converted_to_invoice' => 1]);
                // return redirect()->back();
            }

            $items = WorkshopTyre::where('workshop_id', $workshop->id)->where('ref_type', 'workshop')->where('supplier', 'ownstock')->get();
            // Insert or update stock history records for each item
            foreach ($items as $item) {
                $tyreProduct = TyresProduct::find($item->product_id);
                DB::table('stock_history')->updateOrInsert(
                    [
                        'ean' => $item->product_ean,
                        'ref_type' => 'INV',
                        'ref_id' => $workshop->id,
                    ],
                    [
                        'sku' => $item->product_sku,
                        'product_type' => $item->product_type,
                        'supplier' => $item->supplier,
                        'qty' => $item->quantity,
                        'available_qty' => $tyreProduct->tyre_quantity,
                        'cost_price' => $item->margin_rate,
                        'product_id' => $item->product_id,
                        'user_id' => auth()->id(),
                        'reason' => 'Invoice Created',
                        'stock_type' => 'Decrease',
                        'stock_date' => now()->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Remove stock history entries for items that no longer exist in WorkshopTyre
            $existingEans = $items->pluck('product_ean')->toArray();
            DB::table('stock_history')
                ->where('ref_type', 'INV')
                ->where('ref_id', $workshop->id)
                ->whereNotIn('ean', $existingEans)
                ->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::error("Error occurred while processing invoice for workshop ID: $id", ['error' => $e->getMessage()]);

            // Handle any errors and redirect back with an error message
            return redirect()->back()->with('error', 'Failed to process the invoice: ' . $e->getMessage());
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
        $workshopQuery = DB::table('workshops')->whereNull('deleted_at');

        // Apply filters from request (both GET and POST)
        if ($request->filled('id')) {
            $workshopQuery->where('id', $request->id);
        }
        if ($request->filled('customer_id')) {
            $workshopQuery->where('customer_id', $request->customer_id);
        }
        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $workshopQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                    ->orWhere('company_name', 'like', $searchTerm);
            });
        }
        if ($request->filled('created_at_from')) {
            $workshopQuery->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $workshopQuery->whereDate('created_at', '<=', $request->created_at_to);
        }
        if ($request->filled('mobile')) {
            $workshopQuery->where('mobile', 'like', '%' . $request->mobile . '%');
        }
        if ($request->filled('email')) {
            $workshopQuery->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('origin')) {
            $workshopQuery->where('workshop_origin', 'like', '%' . $request->origin . '%');
        }
        if ($request->filled('convert_to_invoice')) {
            $workshopQuery->where('workshops.is_converted_to_invoice', $request->convert_to_invoice);
        }
        if ($request->filled('status')) {
            $workshopQuery->where('status', 'like', '%' . $request->status . '%');
        }
        if ($request->filled('payment_method')) {
            $workshopQuery->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }
        if ($request->filled('is_void')) {
            $workshopQuery->where('is_void', $request->is_void);
        }
        if ($request->filled('payment_status')) {
            $workshopQuery->where('payment_status', 'like', '%' . $request->payment_status . '%');
        }
        if ($request->filled('vehicle_reg_number_for_search')) {
            $workshopQuery->where('vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
        }
        if ($request->filled('year')) {
            $workshopQuery->where('year', $request->year);
        }

        // Sorting
        $workshopQuery->orderBy('id', 'desc');

        // Paginate
        $workshopResults = $workshopQuery->paginate(10)->appends($request->except('page'));

        // Pass data to view
        $viewData['workshop'] = $workshopResults;

        // Set title
        $viewData['pageTitle'] = 'Workshop Details';

        // If POST, get form input for repopulating fields
        $formAutoFillup = $request->isMethod('post') ? $request->all() : $request->query();

        return view('AutoCare.workshop.search', $viewData, $formAutoFillup);
    }
    public function getActivityLog($id)
    {
        try {
            $logs = ActivityLog::where('workshop_id', $id)
                ->with('user')
                ->latest()
                ->get()
                ->map(function ($log) {
                    // Handle changes field properly
                    if (is_string($log->changes)) {
                        $log->changes_array = json_decode($log->changes, true);
                    } elseif (is_array($log->changes)) {
                        $log->changes_array = $log->changes;
                    } else {
                        $log->changes_array = [];
                    }
                    return $log;
                });

            return view('AutoCare.workshop.modal.activity-log', compact('logs'))->render();

        } catch (\Exception $e) {
            \Log::error('Error fetching activity logs for workshop ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load activity logs'], 500);
        }
    }
    public function viewSearchInvoice(Request $request)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Add Party';
            $workshop = DB::table('invoices');
            // $workshop->leftJoin('modals', 'modals.id', '=', 'invoices.year');
            $getFormAutoFillup = $request->all();
            $workshop->where('invoices.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $workshop->where('invoices.id', '=', $request->id);
            }
            if ($request->filled('name')) {
                $workshop->where('name', 'like', '%' . $request->name . '%'); // Partial match for name
            }
            if ($request->has('customer_id') && $request->customer_id != '') {
                $workshop->where('invoices.customer_id', 'like', '%' . $request->customer_id . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $workshop->whereDate('invoices.created_at', '<=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $workshop->whereDate('invoices.created_at', '>=', $request->created_at_to);
            }
            if ($request->has('mobile') && $request->mobile != '') {
                $workshop->where('invoices.mobile', 'like', '%' . $request->mobile . '%');
            }
            if ($request->has('email') && $request->email != '') {
                $workshop->where('invoices.email', 'like', '%' . $request->email . '%');
            }
            if ($request->has('vehicle_reg_number_for_search') && $request->vehicle_reg_number_for_search != '') {
                $workshop->where('invoices.vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
            }

            if ($request->has('year') && $request->year != '') {
                $workshop->where('invoices.year', '=', $request->year);
            }
            // $workshop->select('invoices.*', 'modals.model_name as modelNumber');
            $workshop->orderBy('id', 'desc');
            $workshop = $workshop->get();
            $viewData['workshop'] = json_decode(json_encode($workshop), true);
            return view('AutoCare.workshop.search-invoice', $viewData)->with($getFormAutoFillup);

        } else {
            $viewData['pageTitle'] = 'Workshop Details';
            $workshop = DB::table('invoices');
            $workshop->where('invoices.deleted_at', '=', null);
            $workshop->orderBy('invoices.id', 'asc');
            $workshop = $workshop->get();
            $viewData['workshop'] = json_decode(json_encode($workshop), true);
            //  $workshop= DB::table('workshops');
            //$workshop->orderBy('id','desc');
            //$workshop= $workshop->get();
            //$viewData['workshop']=json_decode(json_encode($workshop), true);
            return view('AutoCare.workshop.search-invoice', $viewData);
        }

    }
    public function viewpaymenthistory($id)
    {
        // Check if the job_id exists in payment_histories
        $check = DB::table('payment_histories')
            ->where('job_id', '=', $id)
            ->exists();

        if (!$check) {
            return redirect()->back()->with('error', 'No payment history found for this workshop.');
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
        return view('AutoCare.workshop.payment_history', $viewData);
    }
    public function voidInvoice(Request $request, $invoiceId)
    {
        DB::beginTransaction();

        try {
            // Void the workshop
            $workshop = Workshop::findOrFail($invoiceId);
            $workshop->is_void = true;
            $workshop->save();

            // Void the related invoice
            $invoice = Invoice::where('workshop_id', $invoiceId)->first();
            if ($invoice) {
                $invoice->is_void = true;
                $invoice->save();

                // Void related workshop tyres
                WorkshopTyre::where('workshop_id', $workshop->id)->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);

                // Void related workshop services
                WorkshopService::where('workshop_id', $workshop->id)->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);
            }

            // Restore tyre quantities to inventory ===
            $workshopTyres = WorkshopTyre::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->where('is_void', true) // Only restore those that were voided
                ->get();

            foreach ($workshopTyres as $workshopTyre) {
                // Find the tyre product using product_id, supplier, and EAN for matching
                $tyreProduct = TyresProduct::where(function ($query) use ($workshopTyre) {
                    $query->where('product_id', $workshopTyre->product_id)
                        ->where('tyre_supplier_name', $workshopTyre->supplier)
                        ->where('tyre_ean', $workshopTyre->product_ean);
                })->first();

                if ($tyreProduct) {
                    // Restore quantity back to inventory
                    $tyreProduct->tyre_quantity += $workshopTyre->quantity;
                    $tyreProduct->save();

                    ActivityLogger::log(
                        workshopId: $workshop->id,
                        action: 'Restored Tyre Quantity',
                        description: 'Restored tyre quantity for product: ' . $tyreProduct->tyre_description. ' '. $tyreProduct->tyre_ean,
                        changes: ['quantity_restored' => $workshopTyre->quantity,'reason' => 'Void Invoice']
                    );
                }
            }

            DB::commit();

            ActivityLogger::log(
                workshopId: $workshop->id,
                action: 'Void Invoice',
                description: 'Void Invoice, Invoice ID: ' . $workshop->id,
                changes: [
                'invoice_id' => $workshop->id,
                'invoice_status'=>  $workshop->status,
                'balance amount' => $workshop->balance_price]
            );

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'Workshop and Invoice voided successfully!');
            return redirect('/AutoCare/workshop/search');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error("Error voiding workshop/invoice: " . $e->getMessage());

            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'An error occurred while voiding: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function trash(Request $request, $id)
    {
        // Fetch header links for the view
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        // Start a database transaction
        DB::beginTransaction();
        try {
            // Find the workshop by ID
            $workshop = Workshop::findOrFail($id);

            if (!$workshop) {
                throw new \Exception("Workshop not found.");
            }

            // Roll back tyre quantities in the tyres_product table
            $WorkshopTyre = WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->get();

            foreach ($WorkshopTyre as $item) {
                // First, try to find the tyre product using product_id
                $tyreProduct = TyresProduct::find($item->product_id);

                // If product_id does not match, try matching by ean, sku, or both
                if (!$tyreProduct) {
                    $tyreProduct = TyresProduct::where(function ($query) use ($item) {
                        $query->where('tyre_ean', $item->product_ean)
                            ->where('tyre_supplier_name', $item->supplier)
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
            WorkshopService::where('workshop_id', $id)->where('ref_type', 'workshop')->delete(); // Delete workshop service data
            WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->delete(); // Delete WorkshopTyre data

            // Delete the invoice if it exists
            $invoice = Invoice::where('workshop_id', $id)->first();
            if ($invoice) {
                $invoice->delete();
            }

            // Delete customer debit logs associated with the workshop
            CustomerDebitLog::where('workshop_id', $id)->delete();

            // Delete payment history associated with the workshop
            PaymentHistory::where('job_id', $id)->delete();

            // Finally, delete the workshop itself
            $workshop->delete();

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
            session()->flash('status', ['danger', 'Operation Failed!'.$e->getMessage()]);
        }

        // Prepare view data
        $viewData['pageTitle'] = 'Workshop';
        $viewData['workshop'] = Workshop::paginate(10);

        // Return the search view
        return redirect()->back();
    }
    public function trashedList()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        $TrashedParty = Workshop::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.workshop.delete', compact('TrashedParty', 'TrashedParty'));

    }
    public function permanemetDelete(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        if (($id != null) && (Workshop::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', "Workshop was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }

        $TrashedParty = Workshop::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.workshop.delete', compact('TrashedParty', 'TrashedParty'));
    }
    public function viewIndivisual($id)
    {
        // Fetch header links
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();

        // Fetch workshop details
        $workshop = Workshop::whereId($id)->first(); // Keep as an object

        if ($workshop) {
            // Format discount based on type
            if ($workshop->discount_type === 'percentage' && $workshop->discount_value > 0) {
                $formattedDiscount = '(' . $workshop->discount_value . '%)';
            } elseif ($workshop->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the workshop object
            $workshop->formatted_discount = $formattedDiscount;

            // Fetch related data
            $WorkshopTyre = WorkshopTyre::select(
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
                ->where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();

            $WorkshopService = WorkshopService::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();
            $paymentHistory = $this->paymentHistoryService->getPaymentHistory($id);
            $WorkshopVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $workshop->vehicle_reg_number)
                ->get();
            // Pass data to the view
            $viewData['WorkshopTyre'] = $WorkshopTyre;
            $viewData['WorkshopService'] = $WorkshopService;
            $viewData['WorkshopVehicle'] = $WorkshopVehicle;
            $viewData['workshop'] = $workshop; // Pass the workshop object
            $viewData['workshopId'] = $workshop->id;
            $viewData['paymentHistory'] = $paymentHistory;
            return view('AutoCare.workshop.view', $viewData);
        } else {
            // Handle case where no workshop is found
            return redirect()->back()->with('error', 'Workshop not found.');
        }
    }
    public function viewByWorkshop($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $getIndivisualWorkshopDetail = Workshop::whereId($id)->first()->toArray();
        // $WorkshopProduct = DB::table('workshop_products')
        //     ->join('products', 'products.id', '=', 'workshop_products.product_id')
        // ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->get();
        $WorkshopTyre = WorkshopTyre::join('tyres_product', 'tyres_product.product_id', '=', 'workshop_tyres.product_id')
            ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->where('ref_type', 'workshop')->get();

        // $WorkshopService = DB::table('workshop_services')
        //     ->join('services', 'services.id', '=', 'workshop_services.service_id')
        //     ->where('workshop_id', $getIndivisualWorkshopDetail['id'])->get();
        // $viewData['WorkshopProduct'] = $WorkshopProduct;
        $viewData['WorkshopTyre'] = $WorkshopTyre;
        // $viewData['WorkshopService'] = $WorkshopService;
        $viewData['workshopId'] = "";
        return view('AutoCare.workshop.view', $viewData)->with($getIndivisualWorkshopDetail);
    }
    public function viewInvoice($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $workshop = Invoice::where('workshop_id', $id)->first();
        if ($workshop) {
            // Format discount based on type
            if ($workshop->discount_type === 'percentage' && $workshop->discount_value > 0) {
                $formattedDiscount = '(' . $workshop->discount_value . '%)';
            } elseif ($workshop->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the workshop object
            $workshop->formatted_discount = $formattedDiscount;

            $WorkshopTyre = WorkshopTyre::select('workshop_tyres.*', 'workshop_tyres.description', 'workshop_tyres.quantity', 'workshop_tyres.tax_class_id', 'workshop_tyres.fitting_type as orderType', 'workshop_tyres.price as TyreWorkshopPrice', 'workshop_tyres.product_ean as product_ean', 'workshop_tyres.supplier as tyre_source', 'workshop_tyres.price as UnitExitPrice', 'workshop_tyres.tax_class_id as ProductVat')
                ->where('workshop_id', $workshop['workshop_id'])->where('ref_type', 'workshop')->get();
            $WorkshopService = DB::table('workshop_services')
                ->where('workshop_id', $workshop['workshop_id'])
                ->where('ref_type', 'workshop')->get();
            $WorkshopVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $workshop['vehicle_reg_number'])->get();
            $paymentHistory = DB::table('customer_debit_logs')
                ->where('workshop_id', $workshop['workshop_id'])->get();
            $viewData['WorkshopTyre'] = $WorkshopTyre;
            $viewData['WorkshopService'] = $WorkshopService;
            $viewData['WorkshopVehicle'] = $WorkshopVehicle;
            $viewData['workshop'] = $workshop;
            $viewData['workshopId'] = $workshop->id;
            $viewData['paymentHistory'] = $paymentHistory;
            $viewData['workshopId'] = "";
            // dd($viewData);
            return view('AutoCare.workshop.invoice', $viewData);
        } else {
            // Handle case where no workshop is found
            return redirect()->back()->with('error', 'Workshop not found.');
        }
    }
    public function sendInvoiceEmail(Request $request)
    {
        // Validate the request
        $request->validate([
            'invoice_id' => 'required|exists:invoices,workshop_id',
            'email_to' => 'required|email',
            'email_cc' => 'nullable|email',
            'attach_pdf' => 'nullable|boolean',
            'email_body' => 'nullable|string',
        ]);

        // Fetch invoice details
        $invoice = Invoice::where('workshop_id', $request->invoice_id)->firstOrFail();
        $workshopTyreData = WorkshopTyre::where('workshop_id', '=', $request->invoice_id)->where('ref_type', 'workshop')->get();
        $workshopServiceData = WorkshopService::where('workshop_id', '=', $request->invoice_id)->where('ref_type', 'workshop')->get();
        $workshopVehicleData = VehicleDetail::where('vehicle_reg_number', '=', $invoice->vehicle_reg_number)->get();
        $paymentHistory = DB::table('customer_debit_logs')->where('workshop_id', $request->invoice_id)->get();
        if ($invoice) {
            // Format discount based on type
            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $formattedDiscount = '(' . $invoice->discount_value . '%)';
            } elseif ($invoice->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = '';
            }

            // Add formatted discount to the invoice object
            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }
        // Fetch and sanitize garage name for folder path
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');

        // Define the PDF path dynamically
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";

        // Generate the PDF dynamically and save it
        $pdfContent = PDF::loadView('emails.invoice-pdf', compact('invoice', 'workshopServiceData', 'workshopVehicleData', 'workshopTyreData', 'paymentHistory'))->output();
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
                $invoice,
                $request->email_body,
                $request->attach_pdf ? $pdfFullPath : null
            ));
        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'Invoice email sent successfully!');

        return redirect()->back();
    }

    public function previewInvoicePdf($id)
    {
        // Fetch the invoice details
        $invoice = Invoice::where('workshop_id', $id)->firstOrFail();

        if ($invoice) {
            // Format discount based on type
            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $formattedDiscount = '(' . $invoice->discount_value . '%)';
            } elseif ($invoice->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the invoice object
            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }
        // Fetch and sanitize garage name
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_'); // Make it a safe folder name

        // Define the correct PDF path dynamically
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";

        // Ensure the PDF exists, if not, regenerate it
        if (!Storage::disk('public')->exists($pdfPath)) {
            Log::error("PDF not found at path: {$pdfPath}, generating new PDF.");
            $this->generateInvoicePdf($id); // Regenerate PDF if missing
        }

        // Final check: If PDF still doesn't exist, return 404
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Invoice PDF not found.');
        }

        // Return the PDF as an inline response
        return response()->file(storage_path("app/public/{$pdfPath}"), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }

    public function downloadInvoicePdf($id)
    {
        // Fetch the invoice details
        $invoice = Invoice::where('workshop_id', $id)->firstOrFail();

        // Define the file path
        $pdfPath = "invoices/{$invoice->workshop_id}.pdf";

        // Check if the file exists
        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'PDF not found.');
        }

        // Return the PDF as a downloadable response
        return response()->download(storage_path("app/public/{$pdfPath}"), 'invoice.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function generateInvoicePdf($invoiceId)
    {
        // Fetch the invoice details
        $invoice = Invoice::where('workshop_id', $invoiceId)->firstOrFail();
        $workshopTyreData = WorkshopTyre::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopServiceData = WorkshopService::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopVehicleData = VehicleDetail::where('vehicle_reg_number', '=', $invoice->vehicle_reg_number)->get();
        $paymentHistory = DB::table('customer_debit_logs')->where('workshop_id', $invoiceId)->get();
        if ($invoice) {
            // Format discount based on type
            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $formattedDiscount = '(' . $invoice->discount_value . '%)';
            } elseif ($invoice->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            // Add formatted discount to the invoice object
            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }
        // Fetch garage details and sanitize the garage name for safe file paths
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_'); // Convert to a safe folder name

        // Generate the PDF content
        $pdfContent = PDF::loadView('emails.invoice-pdf', compact('invoice', 'workshopServiceData', 'workshopVehicleData', 'workshopTyreData', 'paymentHistory'))->output();

        // Define the file path dynamically based on garage name
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";

        // Ensure the directory exists before saving
        Storage::disk('public')->makeDirectory("invoices/{$safeGarageName}");

        // Save the PDF to the storage/app/public directory
        Storage::disk('public')->put($pdfPath, $pdfContent);

        // Return the full path to the saved PDF
        return storage_path("app/public/{$pdfPath}");
    }

}
