<?php

namespace App\Http\Controllers;

use App\Models\WorkshopPart;
use App\Models\WorkshopLabour;
use App\Models\WorkshopTyre;
use App\Models\tyre_brands;
use App\Models\TyresProduct;
use Illuminate\Http\Request;
use App\Models\Workshop;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\WorkshopService;
use App\Models\WorkshopConsumable;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\HeaderLink;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Traits\HasPermissionCheck;
use App\Models\Customer;
use App\Models\CustomerDebitLog;
use App\Models\VehicleDetail;
use App\Models\CarService;
use App\Models\RegionCounty;
use App\Models\CustomerVehicle;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;
use App\Models\Garage;
use App\Models\ActivityLog;
use App\Models\PaymentHistory;
use App\Services\PaymentHistoryService;
use App\Services\WorkshopDiscountService;

class WorkshopController extends Controller
{
    use HasPermissionCheck;
    protected $paymentHistoryService;
    protected $discountService;
    public function __construct(PaymentHistoryService $paymentHistoryService,WorkshopDiscountService $discountService)
    {
        $this->middleware('auth');
        $this->paymentHistoryService = $paymentHistoryService;
        $this->discountService = $discountService;
    }

    public function save(Request $request, $id = null)
    {
        $this->authorizePermission('workshop.create');
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['pageTitle'] = 'Add Workshop';
        $viewData['tyre_width'] = TyresProduct::pluck('tyre_width', 'product_id');
        $viewData['tyre_profile'] = TyresProduct::pluck('tyre_profile', 'product_id');
        $viewData['service_data'] = CarService::pluck('name', 'service_id');
        $viewData['tyre_brand_name'] = tyre_brands::pluck('name', 'brand_id');
        $viewData['registered_vehicle_select'] = VehicleDetail::pluck('vehicle_reg_number', 'vehicle_reg_number');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['counties'] = RegionCounty::where('status', 1)->get();
        $viewData['garagesData'] = Garage::where('garage_status', 1)->get();
        $viewData['brands'] = tyre_brands::where('status', 1)->get();
        $viewData['selectedDueIn'] = $request->query('due_in');
        $viewData['selectedDueOut'] = $request->query('due_out');

        if ( $viewData['selectedDueIn']) {
             $viewData['selectedDueIn'] = \Carbon\Carbon::parse( $viewData['selectedDueIn'])->format('Y-m-d\TH:i');
        }
        if ($viewData['selectedDueOut']) {
            $viewData['selectedDueOut'] = \Carbon\Carbon::parse($viewData['selectedDueOut'])->format('Y-m-d\TH:i');
        }

        // For editing an existing workshop
        if (isset($id) && $id != null) {
            $workshop = Workshop::findOrFail($id);
            $getFormAutoFillup = $workshop->toArray();

            $vehicleRegNumber = $workshop->vehicle_reg_number;

            $viewData = [
                'workshopTyreData' => WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->get(),
                'workshopServiceData' => WorkshopService::where('workshop_id', $id)->get(),
                'workshopConsumableData' => WorkshopConsumable::where('workshop_id', $id)->get(),
                'workshopPartData' => WorkshopPart::where('workshop_id', $id)->get(),
                'WorkshopLabourData' => WorkshopLabour::where('workshop_id', $id)->get(),
                'workshopVehicleData' => VehicleDetail::where('vehicle_reg_number', $vehicleRegNumber)->get()
            ];
            //dd($viewData);
            $viewData['counties'] = RegionCounty::where('status', 1)->get();
            $viewData['brands'] = tyre_brands::where('status', 1)->get();
            return view('AutoCare.workshop.add', $viewData)->with($getFormAutoFillup);
        }

        if ($request->isMethod('post')) {
            try {
                if (empty($request->input('due_in')) && empty($request->input('due_out'))) {
                    \Log::warning("booking date missing. Workshop not created.");
                    $request->session()->flash('message.level', 'danger');
                    $request->session()->flash('message.content', 'Please add Due in and Due out before saving the workshop.');
                    return redirect()->back()->withInput();
                }

                $saveAndSyncInvoice = $request->has('save_and_sync_invoice');
                if ($request->has('id') && $request->id != null) {
                    $existingWorkshop = Workshop::find($request->id);

                    if (!$existingWorkshop) {
                        return redirect()->back()->with('message.level', 'danger')->with('message.content', 'Workshop not found.');
                    }

                    $paidPrice = $existingWorkshop->paid_price + $existingWorkshop->discount_price;
                    $OnlypaidPrice = $existingWorkshop->paid_price;

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
                        'garage_id',
                        'mileage',
                        'payment_method',
                        'notes',
                        'due_in',
                        'due_out'
                    ]);
                    $PartyManage['address'] = $request->shipping_address_street;
                    $PartyManage['city'] = $request->shipping_address_city;
                    $PartyManage['zone'] = $request->shipping_address_postcode;
                    $PartyManage['county'] = $request->shipping_address_county;
                    $PartyManage['country'] = $request->shipping_address_country;
                    $PartyManage['grandTotal'] = $request->grand_total;
                    $PartyManage['year'] = $request->first_registered;
                    $PartyManage['balance_price'] = $request->grand_total - $paidPrice;
                    $PartyManage['fitting_type'] = $request->fitting_type ?? 'fully_fitted';

                    if ($PartyManage['balance_price'] == 0 && $paidPrice == $request->grand_total) {
                        $PartyManage['payment_status'] = 1;
                    } elseif ($PartyManage['balance_price'] > 0 && $PartyManage['balance_price'] < $request->grand_total && $OnlypaidPrice > 0) {
                        $PartyManage['payment_status'] = 3;
                    } elseif ($PartyManage['balance_price'] == $request->grand_total || $PartyManage['balance_price'] == $request->balance_price + $existingWorkshop->discount_price) {
                        $PartyManage['payment_status'] = 0;
                    }

                    if ($PartyManage['payment_status'] == 1) {
                        $PartyManage['is_complete'] = 1;
                        $PartyManage['status'] = 'completed';
                    }

                    $PartyManage['is_read'] = 1;

                    // Update the workshop record
                    if (Workshop::whereId($request->id)->update($PartyManage)) {
                        $this->saveWorkshopData($request, $request->id);
                        //$this->discountService->applyAutoDiscount($request->id);
                        if ($saveAndSyncInvoice) {
                            $this->convertToInvoice($request->id);
                        }
                    }
                } else {
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
                        'garage_id',
                        'mileage',
                        'payment_method',
                        'notes',
                        'due_in',
                        'due_out'
                    ]);
                    $PartyManage['address'] = $request->shipping_address_street;
                    $PartyManage['city'] = $request->shipping_address_city;
                    $PartyManage['zone'] = $request->shipping_address_postcode;
                    $PartyManage['county'] = $request->shipping_address_county;
                    $PartyManage['country'] = $request->shipping_address_country;
                    $PartyManage['grandTotal'] = $request->grand_total;
                    $PartyManage['year'] = $request->first_registered;
                    $PartyManage['workshop_origin'] = 'Admin';
                    $PartyManage['fitting_type'] = $request->fitting_type ?? 'fully_fitted';

                    if ($request->payment_status == 1) {
                        $PartyManage['is_complete'] = 1;
                        $PartyManage['status'] = 'completed';

                    } else {
                        $PartyManage['balance_price'] = $request->grand_total;
                    }

                    $PartyManage['is_read'] = 1;

                    $newWorkshop = Workshop::create($PartyManage);

                    if ($newWorkshop) {
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

    private function saveWorkshopData($request, $workshopId)
    {
        // **Save or Update Tyre Data**
        if ($request->has('tyre_ean') && !empty($request->tyre_ean[0])) {
            $existingTyres = WorkshopTyre::where('workshop_id', $workshopId)->where('ref_type', 'workshop')->get();
            $updatedTyreKeys = [];
            foreach ($request->tyre_ean as $index => $ean) {
                $supplierName = $request->tyre_supplier_name[$index] ?? null;
                $requestedQuantity = $request->tyre_quantity[$index] ?? 1;
                if (!$ean || !$supplierName)
                    continue;
                $tyreProduct = TyresProduct::where('tyre_ean', $ean)->where('tyre_supplier_name', $supplierName)->first();

                $WorkshopTyre = WorkshopTyre::where('workshop_id', $workshopId)->where('ref_type', 'workshop')->where('product_ean', $ean)->where('supplier', $supplierName)->first();

                if (!$WorkshopTyre) {
                    $WorkshopTyre = new WorkshopTyre();
                    $WorkshopTyre->workshop_id = $workshopId;
                    $WorkshopTyre->ref_type = 'workshop';
                    $WorkshopTyre->supplier = $supplierName;
                    $WorkshopTyre->product_ean = $ean;
                }

                if ($tyreProduct && $WorkshopTyre->exists) {
                    $tyreProduct->tyre_quantity += $WorkshopTyre->quantity;
                }

                $WorkshopTyre->fill([
                    'product_id' => $request->product_id[$index] ?? null,
                    'garage_id' => $request->garage_id ?? null,
                    'product_sku' => $request->tyre_sku[$index] ?? null,
                    'product_type' => $request->product_type ?? null,
                    'description' => $request->tyre_description[$index] ?? null,
                    'quantity' => $requestedQuantity,
                    'cost_price' => $request->tyre_cost_price[$index] ?? 0,
                    'shipping_postcode' => $request->callout_postcode ?? null,
                    'shipping_price' => $request->callout_charges ?? 0,
                    'shipping_tax_id' => $request->callout_vat ?? 0,
                    'garage_fitting_charges' => $request->garage_fitting_charges ?? null,
                    'garage_vat_class' => $request->garage_fitting_vat ?? null,
                    'fitting_type' => $request->fitting_type ?? 'fully_fitted',
                    'margin_rate' => $request->tyre_margin_rate[$index] ?? 0,
                    'tax_class_id' => $request->tyre_vat[$index] ?? 0,
                    'price' => $request->tyre_amount[$index] ?? 0,
                ]);

                $WorkshopTyre->save();

                if ($tyreProduct) {
                    $tyreProduct->tyre_quantity -= $requestedQuantity;
                    $tyreProduct->save();
                } else {
                    \Log::warning("Tyre not found in stock (EAN={$ean}, Supplier={$supplierName}) — stock skipped, only workshop fields updated.");
                }

                $updatedTyreKeys[] = $supplierName . '_' . $ean;

                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: $WorkshopTyre->wasRecentlyCreated ? 'Added Tyre' : 'Updated Tyre',
                    description: ($WorkshopTyre->wasRecentlyCreated ? 'Added' : 'Updated') . " tyre: {$ean}",
                    changes: ['quantity' => $requestedQuantity]
                );
            }

            $removedTyres = $existingTyres->filter(function ($tyre) use ($updatedTyreKeys) {
                return !in_array($tyre->supplier . '_' . $tyre->product_ean, $updatedTyreKeys);
            });

            foreach ($removedTyres as $removedTyre) {
                $tyreProduct = TyresProduct::where('tyre_ean', $removedTyre->product_ean)->where('tyre_supplier_name', $removedTyre->supplier)->first();
                if (!$tyreProduct) {
                    \Log::warning("Skipping removal stock restore for {$removedTyre->product_ean} ({$removedTyre->supplier}) — not found in tyres_products");
                } else {
                    $tyreProduct->tyre_quantity += $removedTyre->quantity;
                    $tyreProduct->save();
                }

                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed Tyre',
                    description: "Removed tyre: {$removedTyre->product_ean}",
                    changes: ['quantity' => $removedTyre->quantity]
                );
                $removedTyre->forceDelete();
            }
        }

        // **Save or Update Service Data**
        if ($request->has('service_id') && $request->service_id[0] != null) {
            $incomingServiceIds = $request->service_id;
            foreach ($incomingServiceIds as $index => $serviceId) {
                $serviceItem = WorkshopService::where('workshop_id', $workshopId)
                    ->where('service_id', $serviceId)
                    ->where('ref_type', 'workshop')
                    ->first();

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
            $removedServices = WorkshopService::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->whereNotIn('service_id', $incomingServiceIds)
                ->get();

            foreach ($removedServices as $removed) {
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed Service',
                    description: 'Service removed: ' . $removed->service_name,
                    changes: [
                        'service_id' => ['old' => $removed->service_id, 'new' => null],
                        'quantity' => ['old' => $removed->service_quantity, 'new' => null],
                        'price' => ['old' => $removed->service_price, 'new' => null],
                        'vat' => ['old' => $removed->tax_class_id, 'new' => null],
                    ]
                );
                $removed->delete();
            }

        }

        // **Save or Update consumable Data**
        if ($request->has('consumable_id') && $request->consumable_id[0] != null) {
            $incoming = [];
            foreach ($request->consumable_id as $index => $id) {
                if ($id === null || $id === '')
                    continue;

                $incoming[] = [
                    'id' => $id,
                    'consumable_name' => $request->consumable_name[$index] ?? 'Unknown consumable',
                    'content' => $request->consumable_content[$index] ?? '',
                    'quantity' => (int) ($request->consumable_quantity[$index] ?? 1),
                    'price' => (float) ($request->consumable_price[$index] ?? 0.0),
                    'vat' => $request->consumable_vat[$index] ?? 0,
                    'fitting_type' => $request->fitting_type ?? 'fully_fitted',
                ];
            }

            $grouped = [];
            foreach ($incoming as $item) {
                $id = $item['id'];
                if (!isset($grouped[$id])) {
                    $grouped[$id] = $item;
                } else {
                    $grouped[$id]['quantity'] += $item['quantity'];
                }
            }
            $grouped = array_values($grouped);

            $incomingConsumableIds = array_column($grouped, 'id');

            foreach ($grouped as $item) {
                $consumableId = $item['id'];

                $consumableItem = WorkshopConsumable::where('workshop_id', $workshopId)
                    ->where('consumable_id', $consumableId)
                    ->where('ref_type', 'workshop')
                    ->first();

                if ($consumableItem) {
                    $original = $consumableItem->toArray();

                    // Update with aggregated values
                    $consumableItem->consumable_id = $consumableId;
                    $consumableItem->consumable_name = $item['consumable_name'];
                    $consumableItem->consumable_content = $item['content'];
                    $consumableItem->fitting_type = $item['fitting_type'];
                    $consumableItem->consumable_quantity = $item['quantity'];
                    $consumableItem->consumable_price = $item['price'];
                    $consumableItem->tax_class_id = $item['vat'];

                    $dirty = $consumableItem->getDirty();

                    if (!empty($dirty)) {
                        $changes = [];
                        foreach ($dirty as $field => $newValue) {
                            $changes[$field] = [
                                'old' => $original[$field] ?? null,
                                'new' => $newValue
                            ];
                        }

                        $consumableItem->save();

                        ActivityLogger::log(
                            workshopId: $workshopId,
                            action: 'Updated consumable',
                            description: 'consumable updated: ' . $consumableItem->consumable_name,
                            changes: $changes
                        );
                    }
                } else {
                    // Create new
                    $consumableItem = new WorkshopConsumable();
                    $consumableItem->workshop_id = $workshopId;
                    $consumableItem->ref_type = 'workshop';
                    $consumableItem->product_type = 'consumable';
                    $consumableItem->consumable_id = $consumableId;
                    $consumableItem->consumable_name = $item['consumable_name'];
                    $consumableItem->consumable_content = $item['content'];
                    $consumableItem->fitting_type = $item['fitting_type'];
                    $consumableItem->consumable_quantity = $item['quantity'];
                    $consumableItem->consumable_price = $item['price'];
                    $consumableItem->tax_class_id = $item['vat'];
                    $consumableItem->save();

                    ActivityLogger::log(
                        workshopId: $workshopId,
                        action: 'Added consumable',
                        description: 'consumable added: ' . $consumableItem->consumable_name,
                        changes: [
                            'consumable_id' => ['old' => null, 'new' => $consumableId],
                            'quantity' => ['old' => null, 'new' => $item['quantity']],
                            'price' => ['old' => null, 'new' => $item['price']],
                            'vat' => ['old' => null, 'new' => $item['vat']],
                        ]
                    );
                }
            }

            $removedConsumables = WorkshopConsumable::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->whereNotIn('consumable_id', $incomingConsumableIds)
                ->get();

            foreach ($removedConsumables as $removed) {
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed consumable',
                    description: 'consumable removed: ' . $removed->consumable_name,
                    changes: [
                        'consumable_id' => ['old' => $removed->consumable_id, 'new' => null],
                        'quantity' => ['old' => $removed->consumable_quantity, 'new' => null],
                        'price' => ['old' => $removed->consumable_price, 'new' => null],
                        'vat' => ['old' => $removed->tax_class_id, 'new' => null],
                    ]
                );
                $removed->delete();
            }

        }

        // **Save or Update part Data**
        if ($request->has('part_id') && $request->part_id[0] != null) {
            $incoming = [];
            foreach ($request->part_id as $index => $id) {
                if ($id === null || $id === '')
                    continue;

                $incoming[] = [
                    'id' => $id,
                    'part_name' => $request->part_name[$index] ?? 'Unknown part',
                    'content' => $request->part_content[$index] ?? '',
                    'quantity' => (int) ($request->part_quantity[$index] ?? 1),
                    'price' => (float) ($request->part_price[$index] ?? 0.0),
                    'vat' => $request->part_vat[$index] ?? 0,
                    'fitting_type' => $request->fitting_type ?? 'fully_fitted',
                ];
            }

            $grouped = [];
            foreach ($incoming as $item) {
                $id = $item['id'];
                if (!isset($grouped[$id])) {
                    $grouped[$id] = $item;
                } else {
                    $grouped[$id]['quantity'] += $item['quantity'];
                }
            }
            $grouped = array_values($grouped);

            $incomingPartIds = array_column($grouped, 'id');

            foreach ($grouped as $item) {
                $partId = $item['id'];

                $partItem = WorkshopPart::where('workshop_id', $workshopId)
                    ->where('part_id', $partId)
                    ->where('ref_type', 'workshop')
                    ->first();

                if ($partItem) {
                    $original = $partItem->toArray();

                    // Update with aggregated values
                    $partItem->part_id = $partId;
                    $partItem->part_name = $item['part_name'];
                    $partItem->part_content = $item['content'];
                    $partItem->fitting_type = $item['fitting_type'];
                    $partItem->part_quantity = $item['quantity'];
                    $partItem->part_price = $item['price'];
                    $partItem->tax_class_id = $item['vat'];

                    $dirty = $partItem->getDirty();

                    if (!empty($dirty)) {
                        $changes = [];
                        foreach ($dirty as $field => $newValue) {
                            $changes[$field] = [
                                'old' => $original[$field] ?? null,
                                'new' => $newValue
                            ];
                        }

                        $partItem->save();

                        ActivityLogger::log(
                            workshopId: $workshopId,
                            action: 'Updated part',
                            description: 'part updated: ' . $partItem->part_name,
                            changes: $changes
                        );
                    }
                } else {
                    // Create new
                    $partItem = new WorkshopPart();
                    $partItem->workshop_id = $workshopId;
                    $partItem->ref_type = 'workshop';
                    $partItem->product_type = 'part';
                    $partItem->part_id = $partId;
                    $partItem->part_name = $item['part_name'];
                    $partItem->part_content = $item['content'];
                    $partItem->fitting_type = $item['fitting_type'];
                    $partItem->part_quantity = $item['quantity'];
                    $partItem->part_price = $item['price'];
                    $partItem->tax_class_id = $item['vat'];
                    $partItem->save();

                    ActivityLogger::log(
                        workshopId: $workshopId,
                        action: 'Added part',
                        description: 'part added: ' . $partItem->part_name,
                        changes: [
                            'part_id' => ['old' => null, 'new' => $partId],
                            'quantity' => ['old' => null, 'new' => $item['quantity']],
                            'price' => ['old' => null, 'new' => $item['price']],
                            'vat' => ['old' => null, 'new' => $item['vat']],
                        ]
                    );
                }
            }

            $removedParts = WorkshopPart::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->whereNotIn('part_id', $incomingPartIds)
                ->get();

            foreach ($removedParts as $removed) {
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed part',
                    description: 'part removed: ' . $removed->part_name,
                    changes: [
                        'part_id' => ['old' => $removed->part_id, 'new' => null],
                        'quantity' => ['old' => $removed->part_quantity, 'new' => null],
                        'price' => ['old' => $removed->part_price, 'new' => null],
                        'vat' => ['old' => $removed->tax_class_id, 'new' => null],
                    ]
                );
                $removed->delete();
            }

        }

        // **Save or Update Labour Data**
        if ($request->has('labour_id') && $request->labour_id[0] != null) {
            $incomingLabourIds = $request->labour_id;
            foreach ($incomingLabourIds as $index => $LabourId) {
                $labourItem = WorkshopLabour::where('workshop_id', $workshopId)
                    ->where('labour_id', $LabourId)
                    ->where('ref_type', 'workshop')
                    ->first();

                if ($labourItem) {
                    $original = $labourItem->toArray();
                    $labourItem->labour_id = $LabourId;
                    $labourItem->labour_name = $request->labour_name[$index] ?? 'Unknown labour';
                    $labourItem->labour_content = $request->labour_content[$index] ?? '';
                    $labourItem->fitting_type = $request->fitting_type ?? 'fully_fitted';
                    $labourItem->labour_quantity = $request->labour_quantity[$index] ?? 1;
                    $labourItem->labour_price = $request->labour_price[$index] ?? 0;
                    $labourItem->tax_class_id = $request->labour_vat[$index] ?? 0;

                    $dirty = $labourItem->getDirty();

                    if (!empty($dirty)) {
                        $changes = [];
                        foreach ($dirty as $field => $newValue) {
                            $changes[$field] = [
                                'old' => $original[$field] ?? null,
                                'new' => $newValue
                            ];
                        }

                        $labourItem->save();

                        ActivityLogger::log(
                            workshopId: $workshopId,
                            action: 'Updated labour',
                            description: 'labour updated: ' . $labourItem->labour_name,
                            changes: $changes
                        );
                    }
                } else {
                    $labourItem = new WorkshopLabour();
                    $labourItem->workshop_id = $workshopId;
                    $labourItem->ref_type = 'workshop';
                    $labourItem->product_type = 'labour';
                    $labourItem->labour_id = $LabourId;
                    $labourItem->labour_name = $request->labour_name[$index] ?? 'Unknown labour';
                    $labourItem->labour_content = $request->labour_content[$index] ?? '';
                    $labourItem->fitting_type = $request->fitting_type ?? 'fully_fitted';
                    $labourItem->labour_quantity = $request->labour_quantity[$index] ?? 1;
                    $labourItem->labour_price = $request->labour_price[$index] ?? 0;
                    $labourItem->tax_class_id = $request->labour_vat[$index] ?? 0;
                    $labourItem->save();

                    ActivityLogger::log(
                        workshopId: $workshopId,
                        action: 'Added labour',
                        description: 'labour added: ' . $labourItem->labour_name,
                        changes: [
                            'labour_id' => ['old' => null, 'new' => $LabourId],
                            'quantity' => ['old' => null, 'new' => $labourItem->labour_quantity],
                            'price' => ['old' => null, 'new' => $labourItem->labour_price],
                            'vat' => ['old' => null, 'new' => $labourItem->tax_class_id],
                        ]
                    );
                }
            }
            $removedLabours = WorkshopLabour::where('workshop_id', $workshopId)
                ->where('ref_type', 'workshop')
                ->whereNotIn('labour_id', $incomingLabourIds)
                ->get();

            foreach ($removedLabours as $removed) {
                ActivityLogger::log(
                    workshopId: $workshopId,
                    action: 'Removed Labour',
                    description: 'Labour removed: ' . $removed->labour_name,
                    changes: [
                        'labour_id' => ['old' => $removed->labour_id, 'new' => null],
                        'quantity' => ['old' => $removed->labour_quantity, 'new' => null],
                        'price' => ['old' => $removed->labour_price, 'new' => null],
                        'vat' => ['old' => $removed->tax_class_id, 'new' => null],
                    ]
                );
                $removed->delete();
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

        if ($request->has('vehicle_reg_number') && $request->vehicle_reg_number != null) {
            $vehicledetails = VehicleDetail::where('vehicle_reg_number', $request->vehicle_reg_number)->first();
            if ($vehicledetails) {
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
            } else {
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
            }

            // Save customer_vehicle relationship if customer_id is present
            if ($request->has('customer_id') && $request->customer_id != null) {
                $customerId = $request->customer_id;
                $vehicleDetailId = $vehicledetails->id;
                $existingRelation = CustomerVehicle::where('customer_id', $customerId)
                    ->where('vehicle_detail_id', $vehicleDetailId)
                    ->first();

                if (!$existingRelation) {
                    $customerVehicle = new CustomerVehicle();
                    $customerVehicle->customer_id = $customerId;
                    $customerVehicle->vehicle_detail_id = $vehicleDetailId;
                    $customerVehicle->save();
                }
            }
        }
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->input('q');

        $customers = Customer::with('group:id,name,discount_type,discount_value,due_date_option,manual_due_date')
            ->where(function ($q) use ($query) {
                $q->where('customer_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%");
            })
            ->select('id', 'customer_name', 'company_name', 'customer_group_id')
            ->get();

        $results = $customers->map(function ($customer) {
            $group = $customer->group;

            return [
                'id' => $customer->id,
                'text' => trim($customer->customer_name . ' - ' . $customer->company_name),
                'group' => $group ? [
                    'name' => $group->name,
                    'discount_type' => $group->discount_type,
                    'discount_value' => $group->discount_value,
                    'due_date_option' => $group->due_date_option,
                    'manual_due_date' => $group->manual_due_date
                        ? \Carbon\Carbon::parse($group->manual_due_date)->format('Y-m-d')
                        : null,
                ] : null,
            ];
        });

        return response()->json($results);
    }


    public function validateTyreStockByEan(string $ean, string $supplier, Request $request)
    {
        try {
            $ean = urldecode($ean);
            $supplier = urldecode($supplier);
            $workshopId = $request->query('workshop_id');
            $tyreProduct = TyresProduct::where('tyre_ean', $ean)->where('tyre_supplier_name', $supplier)->first();

            // If neither found
            if (!$tyreProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found for EAN: ' . $ean . ', Supplier: ' . $supplier,
                    'available' => 0,
                    'main_stock' => 0,
                    'workshop_stock' => 0,
                ]);
            }
            // Determine product type and stock
            if ($tyreProduct) {
                $mainQty = (int) $tyreProduct->tyre_quantity;
                $productType = 'tyre';
            }
            $workshopQty = 0;

            if ($workshopId) {

            if ($productType === 'tyre') {

                $existingItem = WorkshopTyre::where('workshop_id', $workshopId)
                    ->where('product_ean', $ean)
                    ->where('supplier', $supplier)
                    ->first();

                }

                $workshopQty = $existingItem ? (int) $existingItem->quantity : 0;
            }


            $totalAvailable = $mainQty + $workshopQty;

            return response()->json([
                'success' => true,
                'message' => 'Stock fetched successfully.',
                'product_type' => $productType,
                'available' => $totalAvailable,
                'main_stock' => $mainQty,
                'workshop_stock' => $workshopQty,
            ]);
        } catch (\Exception $e) {
            \Log::error('Stock validation by EAN failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating stock.',
                'available' => 0,
            ], 500);
        }
    }

    public function convertToInvoice($id)
    {
        $this->authorizePermission('invoice.create');
        try {
            DB::beginTransaction();
            $workshop = Workshop::findOrFail($id);
            $existingInvoice = Invoice::where('workshop_id', $workshop->id)->first();

            $workshopData = $workshop->toArray();
            $invoiceData = array_merge($workshopData, [
                'workshop_id' => $workshop->id,
                'updated_at' => now(),
            ]);

            $successMessage = '';

            if ($existingInvoice) {
                $existingInvoice->update($invoiceData);
                $workshop->update(['is_converted_to_invoice' => 1]);

                ActivityLogger::log(
                    workshopId: $workshop->id,
                    action: 'Update Invoice',
                    description: 'Updated Invoice: ' . $workshop->id,
                    changes: [
                        'workshop' => $workshop->id,
                        'balance_amount' => $workshop->balance_price,
                        'payment_status' => $workshop->payment_status
                    ]
                );

                $successMessage = 'Invoice updated successfully.';

            } else {
                $invoiceData = array_merge($workshopData, [
                    'workshop_id' => $workshop->id,
                    'created_at' => now(),
                ]);

                $newInvoice = Invoice::create($invoiceData);
                $workshop->update(['is_converted_to_invoice' => 1]);

                ActivityLogger::log(
                    workshopId: $workshop->id,
                    action: 'Create Invoice',
                    description: 'Created New Invoice: ' . $workshop->id,
                    changes: [
                        'workshop' => $workshop->id,
                        'balance_amount' => $workshop->balance_price,
                        'payment_status' => $workshop->payment_status
                    ]
                );

                $successMessage = 'Invoice created successfully.';
            }

            $tyres = WorkshopTyre::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();
            $tyres->each(function ($item) {
                $item->resolved_type = 'tyre';
            });

            $items = $tyres;
//dd($items);
            foreach ($items as $item) {
                $product = null;
                $availableQty = 0;
                $stockColumn = null;
                $type = $item->resolved_type ?? $item->product_type;
                if ($type === 'tyre') {

                    $product = TyresProduct::where('tyre_ean', $item->product_ean)
                        ->where('tyre_supplier_name', $item->supplier)->lockForUpdate()
                        ->first();
                    $stockColumn = 'tyre_quantity';

                    if ($product) {
                        $availableQty = (int) $product->tyre_quantity;
                    }

                }

                if (!$product) {
                    \Log::warning("Product not found: Type={$item->product_type}, Supplier={$item->supplier}");
                    continue;
                  }
                $availableQty = (int) $product->$stockColumn;

                $existing = DB::table('stock_history')
                    ->where([
                        'ean' => $item->product_ean,
                        'ref_type' => 'INV',
                        'ref_id' => $workshop->id,
                    ])
                    ->orderByDesc('id')
                    ->first();

                if ($existing) {
                    if ($existing->qty != $item->quantity) {
                        $qtyDiff = $item->quantity - $existing->qty;
                        $stockType = $qtyDiff > 0 ? 'Decrease' : 'Increase';
                        if ($qtyDiff > 0) {
                        if ($availableQty < $qtyDiff) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Insufficient stock for EAN: ' . $item->product_ean);
                        }

                        $product->decrement($stockColumn, $qtyDiff);

                    } elseif ($qtyDiff < 0) {
                        $product->increment($stockColumn, abs($qtyDiff));
                    }

                        DB::table('stock_history')->insert([
                            'ean' => $item->product_ean,
                            'ref_type' => 'INV',
                            'ref_id' => $workshop->id,
                            'sku' => $item->product_sku,
                            'product_type' => $item->product_type,
                            'supplier' => $item->supplier,
                            'qty' => $item->quantity,
                            'available_qty' => $product->$stockColumn,//$availableQty,
                            'cost_price' => $item->margin_rate,
                            'product_id' => $item->product_id,
                            'user_id' => auth()->id(),
                            'reason' => 'Invoice Updated (qty change: ' . $qtyDiff . ')',
                            'stock_type' => $stockType,
                            'stock_date' => now()->format('Y-m-d'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    if ($availableQty < $item->quantity) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Insufficient stock for EAN: ' . $item->product_ean);
                    }

                    $product->decrement($stockColumn, $item->quantity);
                    DB::table('stock_history')->insert([
                        'ean' => $item->product_ean,
                        'ref_type' => 'INV',
                        'ref_id' => $workshop->id,
                        'sku' => $item->product_sku,
                        'product_type' => $item->product_type,
                        'supplier' => $item->supplier,
                        'qty' => $item->quantity,
                        'available_qty' => $product->$stockColumn,//$availableQty,
                        'cost_price' => $item->margin_rate,
                        'product_id' => $item->product_id,
                        'user_id' => auth()->id(),
                        'reason' => 'Invoice Created',
                        'stock_type' => 'Decrease',
                        'stock_date' => now()->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $existingEans = $items->pluck('product_ean')->toArray();
            $removedItems = DB::table('stock_history')
                ->where('ref_type', 'INV')
                ->where('ref_id', $workshop->id)
                ->whereNotIn('ean', $existingEans)
                ->get();

            foreach ($removedItems as $removed) {

                if ($removed->product_type === 'tyre') {
                    TyresProduct::where('tyre_ean', $removed->ean)
                        ->where('tyre_supplier_name', $removed->supplier)
                        ->increment('tyre_quantity', $removed->qty);
                }
            }

            DB::table('stock_history')
                ->where('ref_type', 'INV')
                ->where('ref_id', $workshop->id)
                ->whereNotIn('ean', $existingEans)
                ->delete();


            DB::commit();
            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error("Error processing invoice for workshop ID: $id", [
                'error' => $e->getMessage()
            ]);
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process the invoice: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        $this->authorizePermission('workshop.view');
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $workshopQuery = DB::table('workshops')
            ->whereNull('workshops.deleted_at')
            ->select('workshops.*')
            ->selectRaw("
                EXISTS (
                    SELECT 1 FROM workshop_tyres wt
                    WHERE wt.workshop_id = workshops.id
                    AND wt.product_type = 'tyre'
                ) as has_tyre
            ");        

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

        $workshopQuery->orderBy('id', 'desc');
        $workshopResults = $workshopQuery->paginate(10)->appends($request->except('page'));
        foreach ($workshopResults as $workshop) {
            $hasTyre = DB::table('workshop_tyres')
                ->where('workshop_id', $workshop->id)
                ->where('product_type', 'tyre')
                ->exists();

            $workshop->has_tyre = $hasTyre;
        }

        $viewData['workshop'] = $workshopResults;
        $viewData['pageTitle'] = 'Workshop Details';

        $formAutoFillup = $request->isMethod('post') ? $request->all() : $request->query();

        return view('AutoCare.workshop.search', $viewData, $formAutoFillup);
    }
   public function getWorkshopData(Request $request)
    {
        $workshopQuery = DB::table('workshops')
            ->leftJoin('garages', 'workshops.garage_id', '=', 'garages.id')
            ->select(
                'workshops.id',
                'workshops.created_at as workshop_date',
                'garages.garage_name',
                'workshops.name',
                'workshops.mobile',
                'workshops.vehicle_reg_number',
                // 'workshops.payment_method',
                'workshops.fitting_type',
                'workshops.balance_price',
                'workshops.grandTotal',
                'workshops.payment_status',
                'workshops.workshop_origin',
                'workshops.status',
                'workshops.is_converted_to_invoice',
                'workshops.is_void',
                'workshops.email'
            )
            ->whereNull('workshops.deleted_at');


        if ($request->filled('id')) {
            $workshopQuery->where('workshops.id', $request->id);
        }

        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $workshopQuery->where(function ($query) use ($searchTerm) {
                $query->where('workshops.name', 'like', $searchTerm)
                    ->orWhere('workshops.company_name', 'like', $searchTerm);
            });
        }

        if ($request->filled('garage_name')) {
            $workshopQuery->where('garages.garage_name', 'like', '%' . $request->garage_name . '%');
        }

        if ($request->filled('mobile')) {
            $workshopQuery->where('workshops.mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->filled('created_at_from')) {
            $workshopQuery->whereDate('workshops.created_at', '>=', $request->created_at_from);
        }

        if ($request->filled('created_at_to')) {
            $workshopQuery->whereDate('workshops.created_at', '<=', $request->created_at_to);
        }

        if ($request->filled('email')) {
            $workshopQuery->where('workshops.email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('origin')) {
            $workshopQuery->where('workshops.workshop_origin', $request->origin);
        }

        if ($request->filled('convert_to_invoice')) {
            $workshopQuery->where('workshops.is_converted_to_invoice', $request->convert_to_invoice);
        }

        if ($request->filled('status')) {
            $workshopQuery->where('workshops.status', $request->status);
        }

        // if ($request->filled('payment_method')) {
        //     $workshopQuery->where('workshops.payment_method', $request->payment_method);
        // }
        if ($request->filled('fitting_type')) {
            $workshopQuery->where('workshops.fitting_type', $request->fitting_type);
        }

        if ($request->filled('is_void')) {
            $workshopQuery->where('workshops.is_void', $request->is_void);
        }

        if ($request->filled('payment_status')) {
            $workshopQuery->where('workshops.payment_status', $request->payment_status);
        }

        if ($request->filled('vehicle_reg_number_for_search')) {
            $workshopQuery->where('workshops.vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
        }

        $workshopQuery->orderBy('workshops.id', 'desc');

        // --- Process with DataTables ---
        return DataTables::of($workshopQuery)
            // Format Date
            ->editColumn('workshop_date_formatted', function ($workshop) {
                return isset($workshop->workshop_date) ? date('d/m/Y H:i:s', strtotime($workshop->workshop_date)) : '';
            })
            // Customer Name (if just taking from workshops)
            ->addColumn('customer_name', function ($workshop) {
                return $workshop->name ?? '';
            })
            ->addColumn('garage_name', function ($workshop) {
                return $workshop->garage_name ?? 'Unknown Garage';
            })
            // Vehicle Reg (uppercase)
            ->addColumn('vehicle_reg', function ($workshop) {
                return strtoupper($workshop->vehicle_reg_number ?? '');
            })
            // Payment Method (formatted)
            // ->addColumn('payment_method_formatted', function ($workshop) {
            //     return strtoupper(str_replace('_', ' ', $workshop->payment_method ?? ''));
            // })
             ->addColumn('fitting_type_formatted', function ($workshop) {
                return strtoupper(str_replace('_', ' ', $workshop->fitting_type ?? ''));
            })
            // Amount Due (formatted)
            ->addColumn('amount_due', function ($workshop) {
                return '£' . number_format($workshop->balance_price ?? 0, 2, '.', '');
            })
            // Grand Total (formatted)
            ->addColumn('grand_total', function ($workshop) {
                return '£' . number_format($workshop->grandTotal ?? 0, 2, '.', '');
            })
            // Payment Status Badge
            ->addColumn('payment_status_badge', function ($workshop) {
                $statusClass = '';
                $statusText = '';
                switch ($workshop->payment_status) {
                    case 1:
                        $statusClass = 'Paid';
                        $statusText = 'Paid';
                        break;
                    case 3:
                        $statusClass = 'Partially';
                        $statusText = 'Partially';
                        break;
                    default:
                        $statusClass = 'Unpaid';
                        $statusText = 'Unpaid';
                }
                return "<span class='{$statusClass}'>{$statusText}</span>";
            })
            // Origin Badge
            ->addColumn('origin_badge', function ($workshop) {
                return "<span class='" . e($workshop->workshop_origin) . "'>" . e($workshop->workshop_origin) . "</span>";
            })
            // Status Badge
            ->addColumn('status_badge', function ($workshop) {
                return "<span class='" . e($workshop->status) . "'>" . e($workshop->status) . "</span>";
            })
            // Invoice Convert Badge
            ->addColumn('invoice_convert_badge', function ($workshop) {
                $text = ($workshop->is_converted_to_invoice == 1) ? 'invoice' : 'workshop';
                $class = ($workshop->is_converted_to_invoice == 1) ? 'invoice' : 'workshop';
                return "<span class='{$class}'>{$text}</span>";
            })

            // Actions Column (Crucial Part)
            ->addColumn('actions', function ($workshop) {
                $emailBody = getDefaultEmailBody($workshop) ?? '';
                $roleId = auth()->user()->role_id ?? 0;
                $isVoid = ($workshop->is_void ?? false);

                $actions = '<div class="btn-group" role="group">
                                <button id="btnGroupDrop' . $workshop->id . '" type="button"
                                    class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu btngroup-dropdown"
                                    aria-labelledby="btnGroupDrop' . $workshop->id . '">';

                if ($workshop->is_converted_to_invoice == 1) {
                    $actions .= '<li>
                                    <a href="' . url('/') . '/AutoCare/workshop/addinvoice/' . $workshop->id . '"
                                        class="dropdown-item btn btn-warning btn-sm">
                                        <i class="fa fa-pencil" aria-hidden="true"></i> Update Invoice
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank"
                                        href="' . url('/') . '/AutoCare/workshop/invoice/' . $workshop->id . '"
                                        class="dropdown-item btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> View Invoice
                                    </a>
                                </li>
                            <li>
                                <button type="button"
                                    class="dropdown-item btn btn-info btn-sm open-email-modal-btn"
                                    data-workshop-id="' . e($workshop->id) . '"
                                    data-workshop-email="' . e($workshop->email ?? '') . '"
                                    data-email-body-b64="' . e(base64_encode($emailBody)) . '">
                                    <i class="fa fa-envelope"></i> Email Invoice
                                </button>
                            </li>';
                    if ($roleId == 1) {
                        $actions .= '<li>
                                        <a href="' . route('invoice.preview', $workshop->id) . '" target="_blank"
                                            class="dropdown-item btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> Preview PDF
                                        </a>
                                    </li>';
                    }
                    if (!$isVoid ) {
                        $actions .= '<li>
                                        <form action="' . url('/AutoCare/workshop/void/' . $workshop->id) . '"
                                            method="POST"
                                            onsubmit="return confirm(\'Are you sure you want to void this workshop?\');">
                                            ' . csrf_field() . '
                                            ' . method_field('POST') . '
                                            <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                <i class="fa fa-remove"></i> Void Invoice
                                            </button>
                                        </form>
                                    </li>';
                    }
                } else {
                    if (!$isVoid) {
                        $actions .= '<li>
                                    <a href="' . url('/') . '/AutoCare/workshop/addinvoice/' . $workshop->id . '"
                                        class="dropdown-item btn btn-primary btn-sm">
                                        <i class="fa fa-upload"></i> Convert to Invoice
                                    </a>
                                </li>';
                    }
                }
        if (!$isVoid) {
                $actions .= '<li>
                                <a data-toggle="modal" id="' . $workshop->id . '"
                                    data-target="#workshopDiscount"
                                    data-balance-total="' . ($workshop->balance_price ?? 0) . '"
                                    class="dropdown-item btn btn-success openDiscountModelForWorkshop btn-sm">
                                    <i class="fa fa-money" aria-hidden="true"></i> Discount
                                </a>
                            </li>
                            <li>
                                <a data-toggle="modal" id="' . $workshop->id . '"
                                    data-target="#workshopPayment"
                                    class="dropdown-item btn btn-success openPayentModelForWorkshop btn-sm"
                                    data-grand-total="' . ($workshop->balance_price ?? 0) . '">
                                    <i class="fa fa-money" aria-hidden="true"></i> Receive Payment
                                </a>
                            </li>';
                            }
                            $actions .= '<li>
                                <a target="_blank"
                                    href="' . url('/') . '/AutoCare/workshop/view/' . $workshop->id . '"
                                    class="dropdown-item btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> Job View
                                </a>
                            </li>';
                if ($workshop->payment_status == 1) {
                    $actions .= '<li>
                                    <a target="_blank"
                                        href="' . url('/') . '/AutoCare/workshop/payment_history/' . $workshop->id . '"
                                        class="dropdown-item btn btn-danger btn-sm" title="Payment History">
                                        <i class="fa fa-eye"></i> Payment History
                                    </a>
                                </li>';
                        }
                    if (!$isVoid) {
                        $actions .= '<li>
                                        <a href="' . url('/') . '/AutoCare/workshop/add/' . $workshop->id . '"
                                            class="dropdown-item btn btn-success btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </li>';
                    }
                $actions .= '<li>
                                <a href="#"
                                    class="dropdown-item btn btn-success btn-sm open-activity-log-modal"
                                    data-id="' . $workshop->id . '">
                                    <i class="fa fa-eye" aria-hidden="true"></i> Activity Log
                                </a>
                            </li>';
                if ($roleId == 1) {
                    $actions .= '<li>
                                    <form action="' . url('/AutoCare/workshop/trash/' . $workshop->id) . '"
                                        method="POST"
                                        onsubmit="return confirm(\'Are you sure you want to delete this workshop?\');">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                            <i class="fa fa-remove"></i> Delete
                                        </button>
                                    </form>
                                </li>';
                }
                $actions .= '</ul></div>';
                return $actions;
            })
            // Payment Status custom search
            ->filterColumn('payment_status_badge', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if ($keyword === 'paid') {
                    $query->where('workshops.payment_status', 1);
                } elseif ($keyword === 'unpaid') {
                    $query->where('workshops.payment_status', 0);
                } elseif ($keyword === 'partially') {
                    $query->where('workshops.payment_status', 3);
                } else {
                    // Allow fuzzy search on labels
                    $query->whereRaw("
            CASE 
              WHEN workshops.payment_status = 1 THEN 'paid'
              WHEN workshops.payment_status = 0 THEN 'unpaid'
              WHEN workshops.payment_status = 3 THEN 'partially'
              ELSE 'unknown'
            END LIKE ?", ["%{$keyword}%"]);
                }
            })

            ->filterColumn('garage_name', function ($query, $keyword) {
                $query->where('garages.garage_name', 'like', "%{$keyword}%");
            })

            // Invoice Convert custom search
            ->filterColumn('invoice_convert_badge', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if ($keyword === 'invoice') {
                    $query->where('workshops.is_converted_to_invoice', 1);
                } elseif ($keyword === 'workshop') {
                    $query->where('workshops.is_converted_to_invoice', 0);
                } else {
                    $query->whereRaw("
            CASE 
              WHEN workshops.is_converted_to_invoice = 1 THEN 'invoice'
              WHEN workshops.is_converted_to_invoice = 0 THEN 'workshop'
              ELSE 'unknown'
            END LIKE ?", ["%{$keyword}%"]);
                }
            })

            ->setRowClass(function ($workshop) {
                $isVoid = ($workshop->is_void ?? false);
                return $isVoid ? 'table-danger' : '';
            })
            ->rawColumns([
                'workshop_date_formatted',
                'customer_name',
                'garage_name',
                'vehicle_reg',
                // 'payment_method_formatted',
                'fitting_type_formatted',
                'amount_due',
                'grand_total',
                'payment_status_badge',
                'origin_badge',
                'status_badge',
                'invoice_convert_badge',
                'actions'
            ])
            ->make(true);
    }

    public function viewSearchInvoice(Request $request)
    {
        $this->authorizePermission('invoice.view');
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');

        $invoiceQuery = DB::table('invoices')->whereNull('deleted_at');

        if ($request->filled('id')) {
            $invoiceQuery->where('workshop_id', $request->id);
        }
        if ($request->filled('customer_id')) {
            $invoiceQuery->where('customer_id', $request->customer_id);
        }
        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $invoiceQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                    ->orWhere('company_name', 'like', $searchTerm);
            });
        }
        if ($request->filled('created_at_from')) {
            $invoiceQuery->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $invoiceQuery->whereDate('created_at', '<=', $request->created_at_to);
        }
        if ($request->filled('mobile')) {
            $invoiceQuery->where('mobile', 'like', '%' . $request->mobile . '%');
        }
        if ($request->filled('email')) {
            $invoiceQuery->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('origin')) {
            $invoiceQuery->where('workshop_origin', 'like', '%' . $request->origin . '%');
        }
        if ($request->filled('status')) {
            $invoiceQuery->where('status', 'like', '%' . $request->status . '%');
        }
        if ($request->filled('payment_method')) {
            $invoiceQuery->where('payment_method', 'like', '%' . $request->payment_method . '%');
        }
        if ($request->filled('is_void')) {
            $invoiceQuery->where('is_void', $request->is_void);
        }
        if ($request->filled('payment_status')) {
            $invoiceQuery->where('payment_status', 'like', '%' . $request->payment_status . '%');
        }
        if ($request->filled('vehicle_reg_number_for_search')) {
            $invoiceQuery->where('vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
        }
        if ($request->filled('year')) {
            $invoiceQuery->where('year', $request->year);
        }

        $invoiceQuery->orderBy('id', 'desc');
        $now = \Carbon\Carbon::now();
        $totals = (clone $invoiceQuery)
            ->selectRaw('SUM(CASE WHEN due_out IS NOT NULL AND due_out < ? AND balance_price > 0 THEN balance_price ELSE 0 END) as total_overdue', [$now])
            ->selectRaw('
        SUM(paid_price) as total_paid,
        SUM(balance_price) as total_balance,
        SUM(discount_price) as total_discount
        ')
            ->first();

        $viewData['total_paid'] = $totals->total_paid ?? 0;
        $viewData['total_balance'] = $totals->total_balance ?? 0;
        $viewData['total_discount'] = $totals->total_discount ?? 0;
        $viewData['total_overdue'] = $totals->total_overdue ?? 0;

        $invoiceResults = $invoiceQuery->paginate(10)->appends($request->except('page'));

        $viewData['workshop'] = $invoiceResults;

        $viewData['pageTitle'] = 'Invoice Details';

        $formAutoFillup = $request->isMethod('post') ? $request->all() : $request->query();

        return view('AutoCare.workshop.search-invoice', $viewData, $formAutoFillup);
    }
    public function getInvoiceData(Request $request)
    {
        $invoiceQuery = DB::table('invoices')
            ->select(
                'invoices.workshop_id',
                'invoices.created_at as workshop_date',
                'invoices.name',
                'invoices.email',
                'invoices.mobile',
                'invoices.vehicle_reg_number',
                'invoices.payment_method',
                'invoices.grandTotal',
                'invoices.paid_price',
                'invoices.discount_price',
                'invoices.balance_price',
                'invoices.payment_status',
                'invoices.workshop_origin',
                'invoices.status',
                'invoices.is_void'
            )
            ->whereNull('invoices.deleted_at');


        if ($request->filled('id')) {
            $invoiceQuery->where('invoices.workshop_id', $request->id);
        }

        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $invoiceQuery->where(function ($query) use ($searchTerm) {
                $query->where('invoices.name', 'like', $searchTerm)
                    ->orWhere('invoices.company_name', 'like', $searchTerm);
            });
        }

        if ($request->filled('mobile')) {
            $invoiceQuery->where('invoices.mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->filled('created_at_from')) {
            $invoiceQuery->whereDate('invoices.created_at', '>=', $request->created_at_from);
        }

        if ($request->filled('created_at_to')) {
            $invoiceQuery->whereDate('invoices.created_at', '<=', $request->created_at_to);
        }

        if ($request->filled('email')) {
            $invoiceQuery->where('invoices.email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('origin')) {
            $invoiceQuery->where('invoices.workshop_origin', $request->origin);
        }

        if ($request->filled('status')) {
            $invoiceQuery->where('invoices.status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $invoiceQuery->where('invoices.payment_method', $request->payment_method);
        }

        if ($request->filled('is_void')) {
            $invoiceQuery->where('invoices.is_void', $request->is_void);
        }

        if ($request->filled('payment_status')) {
            $invoiceQuery->where('invoices.payment_status', $request->payment_status);
        }

        if ($request->filled('vehicle_reg_number_for_search')) {
            $invoiceQuery->where('invoices.vehicle_reg_number', 'like', '%' . $request->vehicle_reg_number_for_search . '%');
        }

        $invoiceQuery->orderBy('invoices.workshop_id', 'desc');

        // --- Process with DataTables ---
        return DataTables::of($invoiceQuery)
            // Format Date
            ->editColumn('workshop_date_formatted', function ($invoice) {
                return isset($invoice->workshop_date) ? date('d/m/Y H:i:s', strtotime($invoice->workshop_date)) : '';
            })
            // Customer Name (if just taking from invoices)
            ->addColumn('customer_name', function ($invoice) {
                return $invoice->name ?? '';
            })
            // Vehicle Reg (uppercase)
            ->addColumn('vehicle_reg', function ($invoice) {
                return strtoupper($invoice->vehicle_reg_number ?? '');
            })
            // Payment Method (formatted)
            ->addColumn('payment_method_formatted', function ($invoice) {
                return strtoupper(str_replace('_', ' ', $invoice->payment_method ?? ''));
            })
            // Amount Due (formatted)
            ->addColumn('amount_due', function ($invoice) {
                return '£' . number_format($invoice->balance_price ?? 0, 2, '.', '');
            })
            ->addColumn('discount', function ($invoice) {
                return '£' . number_format($invoice->discount_price ?? 0, 2, '.', '');
            })
            ->addColumn('paid_price', function ($invoice) {
                return '£' . number_format($invoice->paid_price ?? 0, 2, '.', '');
            })
            // Grand Total (formatted)
            ->addColumn('grand_total', function ($invoice) {
                return '£' . number_format($invoice->grandTotal ?? 0, 2, '.', '');
            })
            // Payment Status Badge
            ->addColumn('payment_status_badge', function ($invoice) {
                $statusClass = '';
                $statusText = '';
                switch ($invoice->payment_status) {
                    case 1:
                        $statusClass = 'Paid';
                        $statusText = 'Paid';
                        break;
                    case 3:
                        $statusClass = 'Partially';
                        $statusText = 'Partially';
                        break;
                    default: // case 0 or null/other
                        $statusClass = 'Unpaid';
                        $statusText = 'Unpaid';
                }
                return "<span class='{$statusClass}'>{$statusText}</span>";
            })
            // Origin Badge
            ->addColumn('origin_badge', function ($invoice) {
                // Ensure workshop_origin is safe for CSS class or escape output
                return "<span class='" . e($invoice->workshop_origin) . "'>" . e($invoice->workshop_origin) . "</span>";
            })
            // Status Badge
            ->addColumn('status_badge', function ($invoice) {
                // Ensure status is safe for CSS class or escape output
                return "<span class='" . e($invoice->status) . "'>" . e($invoice->status) . "</span>";
            })

            // Actions Column (Crucial Part)
            ->addColumn('actions', function ($invoice) {
                $emailBody = getInvoiceEmailBody($invoice) ?? '';
                $roleId = auth()->user()->role_id ?? 0;
                $isVoid = ($invoice->is_void ?? false);

                $actions = '<div class="btn-group" role="group">
                                <button id="btnGroupDrop' . $invoice->workshop_id . '" type="button"
                                    class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu btngroup-dropdown"
                                    aria-labelledby="btnGroupDrop' . $invoice->workshop_id . '">';

                $actions .= '<li>
                                    <a href="' . url('/') . '/AutoCare/workshop/addinvoice/' . $invoice->workshop_id . '"
                                        class="dropdown-item btn btn-warning btn-sm">
                                        <i class="fa fa-pencil" aria-hidden="true"></i> Update Invoice
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank"
                                        href="' . url('/') . '/AutoCare/workshop/invoice/' . $invoice->workshop_id . '"
                                        class="dropdown-item btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> View Invoice
                                    </a>
                                </li>
                            <li>
            <button type="button"
                class="dropdown-item btn btn-info btn-sm open-email-modal-btn"
                data-workshop-id="' . e($invoice->workshop_id) . '"
                data-workshop-email="' . e($invoice->email ?? '') . '"
                data-email-body-b64="' . e(base64_encode($emailBody)) . '">
                <i class="fa fa-envelope"></i> Email Invoice
            </button>
        </li>';
                if ($roleId == 1) {
                    $actions .= '<li>
                                        <a href="' . route('invoice.preview', $invoice->workshop_id) . '" target="_blank"
                                            class="dropdown-item btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> Preview PDF
                                        </a>
                                    </li>';
                }
                if (!$isVoid) {
                    $actions .= '<li>
                                        <form action="' . url('/AutoCare/workshop/void/' . $invoice->workshop_id) . '"
                                            method="POST"
                                            onsubmit="return confirm(\'Are you sure you want to void this workshop?\');">
                                            ' . csrf_field() . '
                                            ' . method_field('POST') . '
                                            <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                                <i class="fa fa-remove"></i> Void Invoice
                                            </button>
                                        </form>
                                    </li>';
                }

                $actions .= '<li>
                                <a data-toggle="modal" id="' . $invoice->workshop_id . '"
                                    data-target="#workshopDiscount"
                                    data-balance-total="' . ($invoice->balance_price ?? 0) . '"
                                    class="dropdown-item btn btn-success openDiscountModelForWorkshop btn-sm">
                                    <i class="fa fa-money" aria-hidden="true"></i> Discount
                                </a>
                            </li>
                            <li>
                                <a data-toggle="modal" id="' . $invoice->workshop_id . '"
                                    data-target="#workshopPayment"
                                    class="dropdown-item btn btn-success openPayentModelForWorkshop btn-sm"
                                    data-grand-total="' . ($invoice->balance_price ?? 0) . '">
                                    <i class="fa fa-money" aria-hidden="true"></i> Receive Payment
                                </a>
                            </li>
                            <li>
                                <a target="_blank"
                                    href="' . url('/') . '/AutoCare/workshop/view/' . $invoice->workshop_id . '"
                                    class="dropdown-item btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> Job View
                                </a>
                            </li>';
                if ($invoice->payment_status == 1) {
                    $actions .= '<li>
                                    <a target="_blank"
                                        href="' . url('/') . '/AutoCare/workshop/payment_history/' . $invoice->workshop_id . '"
                                        class="dropdown-item btn btn-danger btn-sm" title="Payment History">
                                        <i class="fa fa-eye"></i> Payment History
                                    </a>
                                </li>';
                }
                if (!$isVoid) {
                    $actions .= '<li>
                                        <a href="' . url('/') . '/AutoCare/workshop/add/' . $invoice->workshop_id . '"
                                            class="dropdown-item btn btn-success btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </li>';
                }
                $actions .= '<li>
                                <a href="#"
                                    class="dropdown-item btn btn-success btn-sm open-activity-log-modal"
                                    data-id="' . $invoice->workshop_id . '">
                                    <i class="fa fa-eye" aria-hidden="true"></i> Activity Log
                                </a>
                            </li>';
                if ($roleId == 1) {
                    $actions .= '<li>
                                    <form action="' . url('/AutoCare/workshop/trash/' . $invoice->workshop_id) . '"
                                        method="POST"
                                        onsubmit="return confirm(\'Are you sure you want to delete this workshop?\');">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item btn text-danger btn-sm">
                                            <i class="fa fa-remove"></i> Delete
                                        </button>
                                    </form>
                                </li>';
                }
                $actions .= '</ul></div>';
                return $actions;
            })
            // Payment Status custom search
            ->filterColumn('payment_status_badge', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if ($keyword === 'paid') {
                    $query->where('invoices.payment_status', 1);
                } elseif ($keyword === 'unpaid') {
                    $query->where('invoices.payment_status', 0);
                } elseif ($keyword === 'partially') {
                    $query->where('invoices.payment_status', 3);
                } else {
                    // Allow fuzzy search on labels
                    $query->whereRaw("
            CASE 
              WHEN invoices.payment_status = 1 THEN 'paid'
              WHEN invoices.payment_status = 0 THEN 'unpaid'
              WHEN invoices.payment_status = 3 THEN 'partially'
              ELSE 'unknown'
            END LIKE ?", ["%{$keyword}%"]);
                }
            })



            ->setRowClass(function ($invoice) {
                $isVoid = ($invoice->is_void ?? false);
                return $isVoid ? 'table-danger' : '';
            })
            ->rawColumns([
                'workshop_date_formatted',
                'customer_name',
                'vehicle_reg',
                'payment_method_formatted',
                'amount_due',
                'discount',
                'paid_price',
                'grand_total',
                'payment_status_badge',
                'origin_badge',
                'status_badge',
                'actions'
            ])
            ->make(true);
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
    public function viewpaymenthistory($id)
    {
        $check = DB::table('payment_histories')
            ->where('job_id', '=', $id)
            ->exists();

        if (!$check) {
            return redirect()->back()->with('error', 'No payment history found for this workshop.');
        }

        $all_view = DB::table('payment_histories')
            ->join('workshops', 'workshops.id', '=', 'payment_histories.job_id')
            ->leftJoin('customers', 'customers.id', '=', 'workshops.customer_id')
            ->leftJoin('customer_debit_logs', 'customer_debit_logs.payment_history_id', '=', 'payment_histories.id')
            ->where('payment_histories.job_id', '=', $id)
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

        // Convert to an array
        $viewData['AdminSaleView'] = json_decode(json_encode($all_view), true);
        return view('AutoCare.workshop.payment_history', $viewData);
    }
    public function voidInvoice(Request $request, $invoiceId)
    {
        DB::beginTransaction();

        try {
            $workshop = Workshop::findOrFail($invoiceId);
            $workshop->is_void = true;
            $workshop->save();
            $invoice = Invoice::where('workshop_id', $invoiceId)->first();
            if ($invoice) {
                $invoice->is_void = true;
                $invoice->save();
                WorkshopTyre::where('workshop_id', $workshop->id)->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);
                WorkshopService::where('workshop_id', $workshop->id)->where('ref_type', 'workshop')
                    ->update(['is_void' => true]);
            }
            $workshopTyres = WorkshopTyre::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->where('is_void', true)
                ->get();

            foreach ($workshopTyres as $workshopTyre) {
                $tyreProduct = TyresProduct::where(function ($query) use ($workshopTyre) {
                    $query->where('tyre_ean', $workshopTyre->product_ean)
                        ->where('tyre_supplier_name', $workshopTyre->supplier);
                })->first();

                if ($tyreProduct) {
                    $tyreProduct->tyre_quantity += $workshopTyre->quantity;
                    $tyreProduct->save();

                    ActivityLogger::log(
                        workshopId: $workshop->id,
                        action: 'Restored Tyre Quantity',
                        description: 'Restored tyre quantity for product: ' . $tyreProduct->tyre_description . ' ' . $tyreProduct->tyre_ean,
                        changes: ['quantity_restored' => $workshopTyre->quantity, 'reason' => 'Void Invoice']
                    );
                }
            }

            DB::table('stock_history')
                ->where('ref_type', 'INV')
                ->where('ref_id', $workshop->id)
                ->delete();

            DB::commit();

            ActivityLogger::log(
                workshopId: $workshop->id,
                action: 'Void Invoice',
                description: 'Void Invoice, Invoice ID: ' . $workshop->id,
                changes: [
                    'invoice_id' => $workshop->id,
                    'invoice_status' => $workshop->status,
                    'balance amount' => $workshop->balance_price
                ]
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
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

        DB::beginTransaction();
        try {
            $workshop = Workshop::findOrFail($id);

            if (!$workshop) {
                throw new \Exception("Workshop not found.");
            }
            $WorkshopTyre = WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->get();

            foreach ($WorkshopTyre as $item) {
                $tyreProduct = TyresProduct::where('tyre_ean', $item->product_ean)
                    ->where('tyre_supplier_name', $item->supplier)
                    ->first();
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

                if ($tyreProduct) {
                    $tyreProduct->tyre_quantity += $item->quantity;
                    $tyreProduct->save();
                } else {
                    \Log::warning("Tyre product not found for item: " . json_encode($item));
                }
            }

           
            // Delete related data
            Booking::where('workshop_id', $id)->delete();
            WorkshopService::where('workshop_id', $id)->where('ref_type', 'workshop')->delete();
            WorkshopTyre::where('workshop_id', $id)->where('ref_type', 'workshop')->delete();
            $invoice = Invoice::where('workshop_id', $id)->first();
            if ($invoice) {
                $invoice->delete();
            }

            CustomerDebitLog::where('workshop_id', $id)->delete();
            PaymentHistory::where('job_id', $id)->delete();
            $workshop->delete();
            DB::commit();
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Workshop was Deleted!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error trashing workshop ID: $id", ['error' => $e->getMessage()]);
            session()->flash('status', ['danger', 'Operation Failed!' . $e->getMessage()]);
        }

        // Prepare view data
        $viewData['pageTitle'] = 'Workshop';
        $viewData['workshop'] = Workshop::paginate(10);

        // Return the search view
        return redirect()->back();
    }
    public function trashedList()
    {
        $TrashedParty = Workshop::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.workshop.delete', compact('TrashedParty', 'TrashedParty'));

    }
    public function permanemetDelete(Request $request, $id)
    {
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
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();

        // Fetch workshop details
        $workshop = Workshop::whereId($id)->first();

        if ($workshop) {
            if ($workshop->discount_type === 'percentage' && $workshop->discount_value > 0) {
                $formattedDiscount = '(' . $workshop->discount_value . '%)';
            } elseif ($workshop->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = '';
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
            $WorkshopConsumable = WorkshopConsumable::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();
            $WorkshopPart = WorkshopPart::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();
            $WorkshopLabour = WorkshopLabour::where('workshop_id', $workshop->id)
                ->where('ref_type', 'workshop')
                ->get();
            $paymentHistory = $this->paymentHistoryService->getPaymentHistory($id);
            $WorkshopVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $workshop->vehicle_reg_number)
                ->get();
            // Pass data to the view
            $viewData['WorkshopTyre'] = $WorkshopTyre;
            $viewData['WorkshopService'] = $WorkshopService;
            $viewData['WorkshopConsumable'] = $WorkshopConsumable;
            $viewData['WorkshopPart'] = $WorkshopPart;
            $viewData['WorkshopLabour'] = $WorkshopLabour;
            $viewData['WorkshopVehicle'] = $WorkshopVehicle;
            $viewData['workshop'] = $workshop;
            $viewData['workshopId'] = $workshop->id;
            $viewData['paymentHistory'] = $paymentHistory;
            return view('AutoCare.workshop.view', $viewData);
        } else {
            return redirect()->back()->with('error', 'Workshop not found.');
        }
    }
    
    public function viewInvoice($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '1')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $workshop = Invoice::where('workshop_id', $id)->first();
        if ($workshop) {
            if ($workshop->discount_type === 'percentage' && $workshop->discount_value > 0) {
                $formattedDiscount = '(' . $workshop->discount_value . '%)';
            } elseif ($workshop->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = ''; // Default if no discount is set
            }

            $workshop->formatted_discount = $formattedDiscount;
            $WorkshopTyre = WorkshopTyre::select('workshop_tyres.*', 'workshop_tyres.description', 'workshop_tyres.quantity', 'workshop_tyres.tax_class_id', 'workshop_tyres.fitting_type as orderType', 'workshop_tyres.price as TyreWorkshopPrice', 'workshop_tyres.product_ean as product_ean', 'workshop_tyres.supplier as tyre_source', 'workshop_tyres.price as UnitExitPrice', 'workshop_tyres.tax_class_id as ProductVat')
                ->where('workshop_id', $workshop['workshop_id'])->where('ref_type', 'workshop')->get();
            $WorkshopService = DB::table('workshop_services')
                ->where('workshop_id', $workshop['workshop_id'])
                ->where('ref_type', 'workshop')->get();
            $WorkshopConsumable = DB::table('workshop_consumables')
                ->where('workshop_id', $workshop['workshop_id'])
                ->where('ref_type', 'workshop')->get();
            $WorkshopPart = DB::table('workshop_parts')
                ->where('workshop_id', $workshop['workshop_id'])
                ->where('ref_type', 'workshop')->get();
            $WorkshopLabour = DB::table('workshop_labours')
                ->where('workshop_id', $workshop['workshop_id'])
                ->where('ref_type', 'workshop')->get();
            $WorkshopVehicle = DB::table('vehicle_details')
                ->where('vehicle_reg_number', $workshop['vehicle_reg_number'])->get();
            $paymentHistory = DB::table('customer_debit_logs')
                ->where('workshop_id', $workshop['workshop_id'])->get();
            $viewData['WorkshopTyre'] = $WorkshopTyre;
            $viewData['WorkshopService'] = $WorkshopService;
            $viewData['WorkshopConsumable'] = $WorkshopConsumable;
            $viewData['WorkshopPart'] = $WorkshopPart;
            $viewData['WorkshopLabour'] = $WorkshopLabour;
            $viewData['WorkshopVehicle'] = $WorkshopVehicle;
            $viewData['workshop'] = $workshop;
            $viewData['workshopId'] = $workshop->id;
            $viewData['paymentHistory'] = $paymentHistory;
            $viewData['workshopId'] = "";
            return view('AutoCare.workshop.invoice', $viewData);
        } else {
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
        $workshopConsumableData = WorkshopConsumable::where('workshop_id', '=', $request->invoice_id)->where('ref_type', 'workshop')->get();
        $workshopPartData = WorkshopPart::where('workshop_id', '=', $request->invoice_id)->where('ref_type', 'workshop')->get();
        $workshopLabourData = WorkshopLabour::where('workshop_id', '=', $request->invoice_id)->where('ref_type', 'workshop')->get();
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

            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";
        $pdfContent = PDF::loadView('emails.invoice-pdf', compact('invoice', 'workshopServiceData', 'workshopConsumableData','workshopPartData', 'workshopLabourData', 'workshopVehicleData', 'workshopTyreData', 'paymentHistory'))->output();
        Storage::disk('public')->put($pdfPath, $pdfContent);
        $pdfFullPath = storage_path("app/public/{$pdfPath}");
        if ($request->attach_pdf && !Storage::disk('public')->exists($pdfPath)) {
            return redirect()->back()->withErrors('PDF generation failed.');
        }
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
                $formattedDiscount = '';
            }

            // Add formatted discount to the invoice object
            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }
        // Fetch and sanitize garage name
        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');

        // Define the correct PDF path dynamically
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";

        // Ensure the PDF exists, if not, regenerate it
        if (!Storage::disk('public')->exists($pdfPath)) {
            Log::error("PDF not found at path: {$pdfPath}, generating new PDF.");
            $this->generateInvoicePdf($id);
        }

        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Invoice PDF not found.');
        }

        return response()->file(storage_path("app/public/{$pdfPath}"), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }

    public function downloadInvoicePdf($id)
    {
        $invoice = Invoice::where('workshop_id', $id)->firstOrFail();

        $pdfPath = "invoices/{$invoice->workshop_id}.pdf";

        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'PDF not found.');
        }

        return response()->download(storage_path("app/public/{$pdfPath}"), 'invoice.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function generateInvoicePdf($invoiceId)
    {
        $invoice = Invoice::where('workshop_id', $invoiceId)->firstOrFail();
        $workshopTyreData = WorkshopTyre::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopServiceData = WorkshopService::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopConsumableData = WorkshopConsumable::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopPartData = WorkshopPart::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopLabourData = WorkshopLabour::where('workshop_id', '=', $invoiceId)->where('ref_type', 'workshop')->get();
        $workshopVehicleData = VehicleDetail::where('vehicle_reg_number', '=', $invoice->vehicle_reg_number)->get();
        $paymentHistory = DB::table('customer_debit_logs')->where('workshop_id', $invoiceId)->get();
        if ($invoice) {

            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $formattedDiscount = '(' . $invoice->discount_value . '%)';
            } elseif ($invoice->discount_type === 'amount') {
                $formattedDiscount = '';
            } else {
                $formattedDiscount = '';
            }

            $invoice->formatted_discount = $formattedDiscount;
        } else {
            $invoice->formatted_discount = "No Data Found";
        }

        $garageName = getGarageDetails()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');

        $pdfContent = PDF::loadView('emails.invoice-pdf', compact('invoice', 'workshopServiceData', 'workshopConsumableData', 'workshopPartData', 'workshopLabourData', 'workshopVehicleData', 'workshopTyreData', 'paymentHistory'))->output();
        //dd($pdfContent);
        $pdfPath = "invoices/{$safeGarageName}/INV-{$invoice->workshop_id}.pdf";

        Storage::disk('public')->makeDirectory("invoices/{$safeGarageName}");
        Storage::disk('public')->put($pdfPath, $pdfContent);

        return storage_path("app/public/{$pdfPath}");
    }

}
