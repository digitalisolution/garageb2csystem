<?php

namespace App\Http\Controllers\ViewController;


// use App\Services\ApiOrderingServiceFactory;
use App\Models\Workshop;
use App\Models\Customer;
use App\Models\WorkshopService;
use App\Models\CarService;
use App\Models\WorkshopTyre;
use Illuminate\Http\Response;
use Exception;
use App\Mail\SendMailToCustomer;
use App\Mail\OrderToGarage;
use App\Mail\CustomerPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\TyresProduct;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\OrderTypes;
use App\Models\Garage;
use App\Models\GarageDetails;
use App\Models\RegionCounty;
use App\Models\VehicleDetail;
use App\Models\CalendarSetting;
use App\Models\Countries;
use Illuminate\Support\Facades\Session;
use App\Services\DojoService;
use App\Services\PaymentAssistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Services\EmailValidationService;
use App\Services\ApiOrderingService;
use App\Services\UpdateOrderQtyService;
use App\Rules\NoTestCustomer;
use App\Services\RevolutService;


class CheckoutController extends Controller
{


    protected $dojoService;
    protected $paymentAssistService;
    protected $emailValidationService;
    protected $apiOrderingService;
    protected $updateOrderQtyService;
    protected $revolutService;

    /**
     * Constructor to inject dependencies.
     *
     * @param DojoService $dojoService
     * @param PaymentAssistService $paymentAssistService
     * @param EmailValidationService $emailValidationService
     * @param ApiOrderingService $apiOrderingService
     * @param UpdateOrderQtyService $updateOrderQtyService
     * @param RevolutService $revolutService
     */
    public function __construct(
        DojoService $dojoService,
        PaymentAssistService $paymentAssistService,
        EmailValidationService $emailValidationService,
        ApiOrderingService $apiOrderingService,
        UpdateOrderQtyService $updateOrderQtyService,
        RevolutService $revolutService
    ) {
        $this->dojoService = $dojoService;
        $this->paymentAssistService = $paymentAssistService;
        $this->emailValidationService = $emailValidationService;
        $this->apiOrderingService = $apiOrderingService;
        $this->updateOrderQtyService = $updateOrderQtyService;
        $this->revolutService = $revolutService;
    }

    /**
     * Process the checkout request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $cart = session('cart', []);
        $cartItems = [];
        $data = Session::all();
        // dd($data);
        $total = 0;
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty. Please add items to the cart before proceeding to checkout.');
        }
        $garageId = Session::get('selected_garage_id');
        $garageFittingCharge = Session::get('garageFittingCharge');
        $garageFittingVAT = Session::get('garageFittingVAT');
        $domain = str_replace('.', '-', request()->getHost());

        if (!$garageId) {
            return response()->json(['error' => 'Please select a garage first'], 404);
        }
        $calenderBook = OrderTypes::where('status', 1)->where('calender_book', 1)->get()
            ->pluck('ordertype_name')
            ->toArray();
        $userOrdertype = session('user_ordertype');
        $garages = Garage::findOrFail($garageId);
        $token = uniqid('checkout_', true);
        Session::put('checkout_token', $token);
        // Process each item in the cart
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                $product = null;

                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::where('service_id', $item['id'])->first();
                }

                if ($item['type'] === 'tyre') {
                    $image = $product->tyre_image ?? 'sample-tyre.png';
                } elseif ($item['type'] === 'service') {
                    $image = $product->inner_image ?? 'no-img-service.jpg';
                } else {
                    $image = null; // fallback
                }

                if ($product) {
                    $priceField = $item['type'] === 'tyre' ? 'tyre_fullyfitted_price' : 'cost_price';
                    $cartItem = [
                        'product_id' => $product->product_id ?? $product->service_id,
                        'price' => $product->$priceField ?? 0,
                        'model' => $product->tyre_model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'type' => $item['type'],
                        'tyre_weight' => $product->tyre_weight ?? '10KG',
                        'image' => $image,
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'quantity' => $item['quantity'],
                        'tax_class_id' => $product->tax_class_id ?? '',
                        'garageFittingVAT' => $garageFittingVAT,
                        'total' => ($product->$priceField ?? 0) * $item['quantity'],
                        'garageFittingCharges' => $garageFittingCharge,
                    ];

                    if ($item['type'] === 'tyre') {
                        $cartItem['ean'] = $product->tyre_ean ?? '';
                        $cartItem['sku'] = $product->tyre_sku ?? '';
                        $cartItem['desc'] = $product->tyre_description ?? '';
                    }

                    $cartItems[] = $cartItem;
                    $total += ($product->$priceField ?? 0) * $item['quantity'];
                }
            }
        }

        // Example events data (unchanged)
        $events = Booking::all()->map(function ($booking) {
            return [
                'title' => $booking->title,
                'start' => $booking->start ? $booking->start->format('Y-m-d\TH:i:s') : null,
                'end' => $booking->end ? $booking->end->format('Y-m-d\TH:i:s') : null,
            ];
        })->filter(function ($event) {
            return $event['start'] && $event['end'];
        });
        $customer = Auth::guard('customer')->user();
        $vehicleDetails = collect();
        $billingDetails = [];
        $counties = RegionCounty::pluck('name', 'zone_id');
        $countries = Countries::pluck('name', 'country_id');
        if ($customer) {
            $vehicleIds = $customer->vehicles()->pluck('vehicle_detail_id');
            $vehicleDetails = VehicleDetail::whereIn('id', $vehicleIds)
                ->pluck('vehicle_reg_number', 'id');
            $selectedCounty = $customer->shipping_address_county;
            $selectedCountry = $customer->shipping_address_country;
            // dd($selectedCountry);
            $billingDetails = [
                'customer_name' => $customer->customer_name,
                'last_name' => $customer->customer_last_name,
                'email' => $customer->customer_email,
                'phone_number' => $customer->customer_contact_number,
                'address' => $customer->customer_address,
                'city' => $customer->shipping_address_city,
                'postcode' => $customer->shipping_address_postcode,
                'county' => $selectedCounty,
                'country' => $selectedCountry,
                'company' => $customer->company_name,
            ];
        } else {
            $selectedCounty = 1;
            $selectedCountry = 1;
            $VehicleDetails = session('vehicleData', []);

            $billingDetails = [
                'reg_number' => isset($VehicleDetails['regNumber']) ? $VehicleDetails['regNumber'] : null,
                'postcode' => session('postcode'),
            ];

        }
        $services = CarService::where('garage_id', $garageId)
            ->where('status', 1)
            ->get();
        $userOrdertype = session('user_ordertype');
        $bookingDetails = $this->getBookingDetails($userOrdertype);
        $shippingData = session('postcode_data', []);
        $VehicleDetails = session('vehicleData', []);

        $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();

        $response = response()->view('checkout', [
            'checkoutToken' => $token,
            'cartItems' => $cartItems,
            'total' => $total,
            'message' => null,
            'shippingData' => $shippingData,
            'events' => json_encode($events),
            'bookingDetails' => $bookingDetails,
            'VehicleDetails' => $VehicleDetails,
            'billingDetails' => $billingDetails,
            'counties' => $counties,
            'selectedCounty' => $selectedCounty,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,
            'vehicleDetails' => $vehicleDetails,
            'garages' => $garages,
            'services' => $services,
            'domain' => $domain,
            'calenderBook' => $calenderBook,
            'userOrdertype' => $userOrdertype,
            'calendarSettings' => $calendarSettings
        ]);

        $this->preventPageCaching($response);

        return $response;
    }
    public function refresh()
    {
        $cart = session('cart', []);
        $cartItems = [];
        $total = 0;
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Your cart is empty. Please add items to the cart before proceeding to checkout.');
        }
        $token = uniqid('checkout_', true);
        $garageFittingCharge = Session::get('garageFittingCharge');
        $garageFittingVAT = Session::get('garageFittingVAT');
        Session::put('checkout_token', $token);
        // Process each item in the cart
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                $product = null;
                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::where('service_id', $item['id'])->first();
                }
                if ($product) {
                    $priceField = $item['type'] === 'tyre' ? 'tyre_fullyfitted_price' : 'cost_price';
                    $cartItem = [
                        'product_id' => $product->product_id ?? $product->service_id,
                        'price' => $product->$priceField ?? 0,
                        'model' => $product->tyre_model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'type' => $item['type'],
                        'tyre_weight' => $product->tyre_weight ?? '10KG',
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'quantity' => $item['quantity'],
                        'tax_class_id' => $product->tax_class_id ?? '',
                        'garageFittingVAT' => $garageFittingVAT,
                        'total' => ($product->$priceField ?? 0) * $item['quantity'],
                        'garageFittingCharges' => $garageFittingCharge,
                    ];
                    if ($item['type'] === 'tyre') {
                        $cartItem['ean'] = $product->tyre_ean ?? '';
                        $cartItem['sku'] = $product->tyre_sku ?? '';
                        $cartItem['desc'] = $product->tyre_description ?? '';
                    }

                    $cartItems[] = $cartItem;
                    $total += ($product->$priceField ?? 0) * $item['quantity'];
                }
            }
        }

        // Example events data (unchanged)
        $events = Booking::all()->map(function ($booking) {
            return [
                'title' => $booking->title,
                'start' => $booking->start ? $booking->start->format('Y-m-d\TH:i:s') : null,
                'end' => $booking->end ? $booking->end->format('Y-m-d\TH:i:s') : null,
            ];
        })->filter(function ($event) {
            return $event['start'] && $event['end'];
        });
        $customer = Auth::guard('customer')->user();
        $vehicleDetails = collect();
        $billingDetails = [];
        $counties = RegionCounty::pluck('name', 'zone_id');
        $countries = Countries::pluck('name', 'country_id');
        // If logged in, fetch customer data
        if ($customer) {
            $vehicleIds = $customer->vehicles()->pluck('vehicle_detail_id');
            $vehicleDetails = VehicleDetail::whereIn('id', $vehicleIds);
            $selectedCounty = $customer->shipping_address_county;
            $selectedCountry = $customer->shipping_address_country;
            $billingDetails = [
                'customer_name' => $customer->customer_name,
                'last_name' => $customer->customer_last_name,
                'email' => $customer->customer_email,
                'phone_number' => $customer->customer_contact_number,
                'address' => $customer->customer_address,
                'city' => $customer->shipping_address_city,
                'postcode' => $customer->shipping_address_postcode,
                'county' => $selectedCounty,
                'country' => $selectedCountry,
                'company' => $customer->company_name,
            ];
        } else {
            $selectedCounty = 1;
            $selectedCountry = 1;
            $VehicleDetails = session('vehicleData', []);

            $billingDetails = [
                'reg_number' => isset($VehicleDetails['regNumber']) ? $VehicleDetails['regNumber'] : null,
                'postcode' => session('postcode'),
            ];

        }
        $garageId = Session::get('selected_garage_id');
        if (!$garageId) {
            return response()->json(['error' => 'Please select a garage first'], 404);
        }
        $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();
        // $calendarSettings = CalendarSetting::where('default', 1)->first();
        // dd($billin);
        $userOrdertype = session('user_ordertype');
        $bookingDetails = $this->getBookingDetails($userOrdertype);
        $shippingData = session('postcode_data', []);
        $VehicleDetails = session('vehicleData', []);

        // Pass all data to the view
        return view('cart', [
            'checkoutToken' => $token,
            'cartItems' => $cartItems,
            'total' => $total,
            'message' => null,
            'shippingData' => $shippingData,
            'events' => json_encode($events),
            'bookingDetails' => $bookingDetails,
            'VehicleDetails' => $VehicleDetails,
            'billingDetails' => $billingDetails,
            'counties' => $counties,
            'selectedCounty' => $selectedCounty,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,
            'vehicleDetails' => $vehicleDetails,
            'calendarSettings' => $calendarSettings
        ]);
    }
    public function autoSaveCustomer(Request $request)
    {
        $customerData = Session::get('customer');

        if (!$customerData) {
            return response()->json(['success' => false, 'error' => 'No customer data found in session.'], 400);
        }

        try {
            $validationResult = $this->emailValidationService->validateEmail(
                $customerData['email'],
                $customerData['email']
            );
            if (!$validationResult['status']) {
                throw new \Exception($validationResult['message']);
            }
            $existingCustomer = Customer::where('customer_email', $customerData['email'])->first();

            if ($existingCustomer) {
                $loggedInCustomer = Auth::guard('customer')->user();
                if ($loggedInCustomer && $loggedInCustomer->id === $existingCustomer->id) {
                    $existingCustomer->update([
                        'customer_name' => $customerData['customer_name'],
                        'customer_last_name' => $customerData['last_name'] ?? $existingCustomer->customer_last_name,
                        'customer_contact_number' => $customerData['phone_number'] ?? $existingCustomer->customer_contact_number,
                        'customer_address' => $customerData['address'] ?? $existingCustomer->customer_address,
                        'shipping_address_city' => $customerData['city'] ?? $existingCustomer->shipping_address_city,
                        'shipping_address_postcode' => $customerData['postcode'] ?? $existingCustomer->shipping_address_postcode,
                        'shipping_address_county' => $customerData['county'] ?? $existingCustomer->shipping_address_county,
                        'shipping_address_country' => $customerData['country'] ?? $existingCustomer->shipping_address_country,
                        'company_name' => $customerData['company_name'] ?? $existingCustomer->company_name,
                    ]);

                    return response()->json(['success' => true, 'customer_id' => $existingCustomer->id], 200);
                } else {
                    $customerId = $existingCustomer->id;
                }
            } else {
                $password = Str::random(10);

                $newCustomer = Customer::create([
                    'customer_name' => $customerData['customer_name'],
                    'customer_last_name' => $customerData['last_name'],
                    'customer_email' => $customerData['email'],
                    'customer_contact_number' => $customerData['phone_number'],
                    'customer_address' => $customerData['address'],
                    'shipping_address_city' => $customerData['city'],
                    'shipping_address_postcode' => $customerData['postcode'],
                    'shipping_address_county' => $customerData['county'],
                    'shipping_address_country' => $customerData['country'],
                    'company_name' => $customerData['company_name'],
                    'password' => Hash::make($password),
                ]);
                try {
                    Mail::to($newCustomer->customer_email)->send(new CustomerPasswordMail($password, $newCustomer->customer_email));
                } catch (\Exception $e) {
                    \Log::error('Failed to send password email:', ['error' => $e->getMessage()]);
                }

                return response()->json(['success' => true, 'customer_id' => $newCustomer->id], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function storeInSession(Request $request)
    {
        // dd($request);
        $fieldName = $request->input('fieldName');
        $fieldValue = $request->input('fieldValue');
        $customerData = Session::get('customer', []);
        $customerData[$fieldName] = $fieldValue;
        Session::put('customer', $customerData);
        return response()->json(['success' => true, 'session_data' => $customerData]);
    }
    protected function getBookingDetails($userOrdertype)
    {
        $calenderBook = OrderTypes::where('status', 1)
            ->pluck('calender_book', 'ordertype_name')
            ->toArray();
        $bookingDetails = session('bookingDetails', null);

        if (isset($calenderBook[$userOrdertype]) && $calenderBook[$userOrdertype] == 1) {
            if (!$bookingDetails) {
                $bookingDetails = null;
            }
        } else {
            $now = Carbon::now();
            $bookingDetails = [
                'start' => $now->copy()->addDays(5)->setTime(12, 30, 0)->format('Y-m-d H:i:s'),
                'end' => $now->copy()->addDays(5)->setTime(13, 0, 0)->format('Y-m-d H:i:s'),
            ];
        }

        session(['bookingDetails' => $bookingDetails]);

        return $bookingDetails;
    }
    public function submit(Request $request)
    {
        // Validate the token
        $storedToken = Session::get('checkout_token');
        $submittedToken = $request->input('checkout_token');
        $userOrdertype = session('user_ordertype');
        $bookingDetails = $this->getBookingDetails($userOrdertype);
        if (empty($bookingDetails) || !isset($bookingDetails['start']) || !isset($bookingDetails['end'])) {
            return back()->withErrors(['booking_slot' => 'Booking slot is not selected. Please select a booking slot before submitting the order.'])->withInput();
        }

        Session::forget('checkout_token');
        // dd($request);
        $rules = $this->getValidationRules();
        $data = $request->all();

        if (!empty($data['new_reg_number'])) {
            unset($data['reg_number']);
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $workshopDefaults = $this->getWorkshopDefaults();
        $workshopData = $this->prepareWorkshopData($validated, $workshopDefaults);
        // dd($workshopData);
        try {
            DB::beginTransaction();

            $customerId = $this->handleCustomer($validated);
            $workshopData['customer_id'] = $customerId;
            $workshop = $this->saveWorkshop($workshopData);
            $workshopId = $workshop['id'];
            $this->saveCartItems($workshop->id);
            $this->saveCalendarBooking($workshop);
            $cartItems = Session::get('cart', []);
            DB::commit();

            if (isset($validated['payment_method']) && $validated['payment_method'] === 'global_payment') {
                return redirect()->route('payment.make', ['workshopid' => $workshop->id]);
            }

            if ($validated['payment_method'] && $validated['payment_method'] === 'dojo') {
                return $this->dojoService->processPaymentWebsite([
                    'workshop_id' => $workshop->id,
                    'total' => $workshop->grandTotal,
                ]);
            }
            if ($validated['payment_method'] && $validated['payment_method'] === 'paymentassist') {
                $secret = get_option('paymentmethod_paymentassist_Secret_key') ?? null;
                $hash = hash_hmac('sha256', $workshop->id . '|' . $workshop->grandTotal, $secret);

                return redirect()->route('paymentassist.pay', [
                    'jobid' => $workshop->id,
                    'hash' => $hash,
                    'total' => $workshop->grandTotal,
                ]);
            }
            if (isset($validated['payment_method']) && $validated['payment_method'] === 'revolut') {
                return $this->revolutService->processPaymentWebsite([
                    'workshop_id' => $workshop->id,
                ]);
            }

            if (isset($validated['payment_method']) && $validated['payment_method'] === 'pay_at_fitting_center') {
                if ($workshop->status !== 'failed') {
                    $this->processOrder($validated, $workshop, $workshop->id);
                    $this->updateOrderQtyService->updateStockQty($workshop->id);
                }
            }

            return redirect()->route('checkout.ordersuccess')->with('message', 'Order submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during submission:', ['error' => $e->getMessage()]);
            return back()->withErrors('Error processing request: ' . $e->getMessage())->withInput();
            ;
        }
    }
    protected function getValidationRules()
    {
        return [
            'email' => 'required|email',
            'customer_name' => 'required|string|min:2|max:50|regex:/^[A-Za-z\s]+$/',
            'last_name' => 'required|string|min:2|max:50|regex:/^[A-Za-z\s]+$/',
            'phone_number' => 'required|string|min:10|max:15|regex:/^[0-9\-\+\(\)\s]+$/',
            'address' => 'required|string|min:5|max:500',
            'comment' => 'nullable|string|min:5|max:1000',
            'payment_method' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'county' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:10|regex:/^[A-Za-z0-9\s]{3,10}$/',
            'new_reg_number' => 'nullable|string|min:5|max:10|regex:/^[A-Za-z0-9\-]+$/',
            'reg_number' => 'nullable|string|min:5|max:10|regex:/^[A-Za-z0-9\-]+$/',
            'reg_number.required_without' => 'new_reg_number',
            'new_reg_number.required_without' => 'reg_number',

        ];
    }
    protected function getWorkshopDefaults()
    {
        return [
            'is_workshop' => 1,
            'is_complete' => false,
            'paid_price' => 0,
            'installmentPayment' => 0,
            'discount_price' => 0,
            'balance_price' => 0,
            'serviceGST' => 0,
        ];
    }
    protected function prepareWorkshopData(array $validated, array $defaults)
    {
        // Get cart items and booking details from session
        $cartTotalPrice = Session::get('cartTotalPrice', []);
        // dd($cartTotalPrice);
        $cartItems = Session::get('cart', []);
        $localTimezone = 'Europe/London';
        $userOrdertype = session('user_ordertype');
        $bookingDetails = $this->getBookingDetails($userOrdertype);
        $fittingType = null;
        foreach ($cartItems as $item) {
            if (isset($item['fitting_type'])) {
                if (!$fittingType) {
                    $fittingType = $item['fitting_type'];
                }
            }
        }

        // Convert booking start and end times to UTC
        $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $bookingDetails['start'], $localTimezone)
            ->setTimezone('UTC');

        $endUtc = Carbon::createFromFormat('Y-m-d H:i:s', $bookingDetails['end'], $localTimezone)
            ->setTimezone('UTC');

        // Get current UK time
        $currentDateTimeUk = now($localTimezone);
        $VehicleDetails = Session::get('vehicleData', []);
        $status = 'booked';
        if (
            isset($validated['payment_method']) &&
            $validated['payment_method'] !== 'pay_at_fitting_center'
        ) {
            $status = 'failed';
        }
        $vehicleRegNumber = $validated['new_reg_number'] ?? $validated['reg_number'];
        if (!$vehicleRegNumber) {
            throw new \Exception('Either reg_number or new_reg_number must be provided.');
        }
        $countyName = RegionCounty::where('zone_id', $validated['county'])->value('name');
        $countryName = Countries::where('country_id', $validated['country'])->value('name');
        $verification_code = Str::upper(Str::random(4));
        $validated['county_name'] = $countyName;
        $validated['country_name'] = $countryName;
        $garageId = Session::get('selected_garage_id');
        return array_merge($defaults, [
            'name' => $validated['customer_name'] ?? 'Unnamed Workshop',
            'last_name' => $validated['last_name'] ?? null,
            'garage_id' => $garageId,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'county' => $validated['county_name'] ?? null,
            'country' => $validated['country_name'] ?? null,
            'zone' => $validated['postcode'] ?? null,
            'mobile' => $validated['phone_number'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'vehicle_reg_number' => $vehicleRegNumber,
            'make' => $VehicleDetails['make'] ?? null,
            'model' => $VehicleDetails['model'] ?? null,
            'year' => $VehicleDetails['year'] ?? null,
            'due_in' => $startUtc->format('Y-m-d H:i:s') ?? null,
            'due_out' => $endUtc->format('Y-m-d H:i:s') ?? null,
            'workshop_date' => $currentDateTimeUk,
            'notes' => $validated['comment'] ?? null,
            'grandTotal' => $cartTotalPrice ?? 0,
            'balance_price' => $cartTotalPrice ?? 0,
            'workshop_origin' => 'Website',
            'fitting_type' => $fittingType ?? 'fully_fitted',
            'status' => $status,
            'verification_code' => $verification_code,
        ]);
    }
    protected function handleCustomer(array $validated)
    {
        $customerId = Session::get('customer_id');

        if (!$customerId) {
            $validationResult = $this->emailValidationService->validateEmail(
                $validated['email'],
                $validated['email']
            );
            if (!$validationResult['status']) {
                throw new \Exception($validationResult['message']);
            }
            $existingCustomer = Customer::where('customer_email', $validated['email'])->first();

            if ($existingCustomer) {
                $loggedInCustomer = Auth::guard('customer')->user();

                if ($loggedInCustomer && $loggedInCustomer->id === $existingCustomer->id) {
                    $existingCustomer->update([
                        'customer_name' => $validated['customer_name'],
                        'customer_last_name' => $validated['last_name'] ?? $existingCustomer->customer_last_name,
                        'customer_contact_number' => $validated['phone_number'] ?? $existingCustomer->customer_contact_number,
                        'customer_address' => $validated['address'] ?? $existingCustomer->customer_address,
                        'shipping_address_city' => $validated['city'] ?? $existingCustomer->shipping_address_city,
                        'shipping_address_postcode' => $validated['postcode'] ?? $existingCustomer->shipping_address_postcode,
                        'shipping_address_county' => $validated['county'] ?? $existingCustomer->shipping_address_county,
                        'shipping_address_country' => $validated['country'] ?? $existingCustomer->shipping_address_country,
                        'company_name' => $validated['company_name'] ?? $existingCustomer->company_name,
                    ]);

                    $customerId = $existingCustomer->id;
                } else {
                    $customerId = $existingCustomer->id;
                }
            } else {
                $password = Str::random(10);
                $newCustomer = Customer::create([
                    'customer_name' => $validated['customer_name'],
                    'customer_last_name' => $validated['last_name'],
                    'customer_email' => $validated['email'],
                    'customer_contact_number' => $validated['phone_number'],
                    'customer_address' => $validated['address'],
                    'shipping_address_city' => $validated['city'],
                    'shipping_address_postcode' => $validated['postcode'],
                    'shipping_address_county' => $validated['county'],
                    'shipping_address_country' => $validated['country'],
                    'company_name' => $validated['company_name'],
                    'password' => Hash::make($password),
                ]);

                $customerId = $newCustomer->id;
                try {
                    Mail::to($newCustomer->customer_email)->send(
                        new CustomerPasswordMail($password, $newCustomer->customer_email)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send password email:', ['error' => $e->getMessage()]);
                }
            }

            // Store customer details in the session
            Session::put('customer_id', $customerId);
            Session::put('customer_name', $validated['customer_name']);
        }
        $vehicleRegNumber = $validated['new_reg_number'] ?? $validated['reg_number'];
        // $vehicleRegNumber = session('vehicleData.regNumber') ?? $validated['reg_number'] ?? null;
        $this->attachVehicleToCustomer($customerId, $vehicleRegNumber);
        return $customerId;
    }
    private function attachVehicleToCustomer(int $customerId, ?string $vehicleRegNumber): void
    {
        // dd($vehicleRegNumber);
        if ($vehicleRegNumber) {
            $vehicle = VehicleDetail::firstOrCreate(
                ['vehicle_reg_number' => $vehicleRegNumber],
                ['make' => '', 'model' => '', 'year' => '']
            );

            // Attach the vehicle to the customer
            $customer = Customer::find($customerId);
            $customer->vehicles()->attach($vehicle->id);
        }
    }
    protected function saveWorkshop(array $workshopData)
    {
        // dd($workshopData);
        return Workshop::create($workshopData);
    }
    protected function saveCartItems($workshopId)
    {
        $cartItems = Session::get('cart', []);
        $postcodeData = Session::get('postcode_data', []);        
        $garageFittingCharge = Session::get('garageFittingCharge');        
        $garageFittingVAT = Session::get('garageFittingVAT');
        $garageVatClass = Session::get('garageVatClass');
        $shippingPricePerJob = Session::get('shippingPricePerJob');
        $shippingPricePerTyre = Session::get('shippingPricePerTyre');
        $shippingPostcode = $postcodeData['postcode'] ?? null;
        $shippingType = $postcodeData['ship_type'] ?? 'job';
        $shippingPriceWithoutVAT = 0;
        $garageId = Session::get('selected_garage_id');
        // dd($garageId);
        // Determine the shipping price based on ship_type
        if ($shippingType === 'job') {
            $shippingPriceWithoutVAT = $shippingPricePerJob ?? 0;
        } elseif ($shippingType === 'tyre') {
            $shippingPriceWithoutVAT = $shippingPricePerTyre ?? 0;
        }

        $shippingTaxId = $postcodeData['includes_vat'] ?? 0;


        foreach ($cartItems as $key => $item) {
            try {
                // Handle tyres
                if ($item['type'] === 'tyre') {
                    $tyreData = [
                        'workshop_id' => $workshopId,
                        'garage_id' => $garageId,
                        'ref_type' => 'workshop',
                        'product_ean' => $item['ean'],
                        'product_sku' => $item['sku'],
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'fitting_type' => $item['fitting_type'],
                        'tax_class_id' => $item['tax_class_id'],
                        'description' => $item['desc'],
                        'tyre_weight' => $item['tyre_weight'],
                        'model' => $item['model'],
                        'price' => $item['price'],
                        'margin_rate' => $item['price'],
                        'supplier' => $item['supplier'],
                        'product_type' => $item['type'],
                        'shipping_postcode' => $shippingPostcode,
                        'shipping_price' => $shippingPriceWithoutVAT,
                        'shipping_tax_id' => $shippingTaxId,
                        'garage_vat_class' => $garageVatClass,
                        'garage_fitting_charges' => $garageFittingCharge
                    ];
                    WorkshopTyre::create($tyreData);
                }

                    if ($item['type'] === 'service') {
                        $service = CarService::find($item['id']);

                        if (!$service) {
                            \Log::warning('Service not found', ['service_id' => $item['id']]);
                            return;
                        }

                        $serviceData = [
                            'workshop_id' => $workshopId,
                            'garage_id' => $garageId,
                            'service_id' => $item['id'],
                            'ref_type' => 'workshop',
                            'service_name' => $item['model'],
                            'service_price' => $item['price'],
                            'fitting_type' => $item['fitting_type'],
                            'service_quantity' => $item['quantity'],
                            'tax_class_id' => $item['tax_class_id'],
                            'product_type' => $item['type'],
                            'service_commission_price' => $service->service_commission_price,
                        ];

                        WorkshopService::create($serviceData);
                    }
            } catch (\Exception $e) {
                Log::error('Error saving cart item:', [
                    'item_key' => $key,
                    'item' => $item,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
    protected function saveCalendarBooking($workshopData)
    {
        // dd($workshopData);
        // $slotDetails = Session::get('bookingDetails', []);
        $userOrdertype = session('user_ordertype');
        $slotDetails = $this->getBookingDetails($userOrdertype);
        $workshopId = $workshopData->id;
        $garageId = $workshopData->garage_id;
        $workshopName = $workshopData->name;

        $customerName = $workshopName ?: 'Unknown Customer';

        $localTimezone = 'Europe/London';

        $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $slotDetails['start'], $localTimezone)
            ->setTimezone('UTC');

        $endUtc = Carbon::createFromFormat('Y-m-d H:i:s', $slotDetails['end'], $localTimezone)
            ->setTimezone('UTC');

        Booking::create([
            'workshop_id' => $workshopId,
            'garage_id' => $garageId,
            'title' => $customerName,
            'start' => $startUtc->format('Y-m-d H:i:s'),
            'end' => $endUtc->format('Y-m-d H:i:s'),
        ]);
    }
    protected function processOrder(array $validated, $workshop, $orderId)
    {
        if ($validated['payment_method'] === 'pay_at_fitting_center' && $workshop['status'] !== 'failed') {
            // Process API order immediately for "pay_at_fitting_center"
            $this->apiOrderingService->processApiOrder($orderId);
            $this->sendOrderConfirmationEmail($validated, $orderId);
        }
    }
    protected function sendOrderConfirmationEmail(array $validated, $orderId)
    {
        $nameRule = new NoTestCustomer();
        $emailRule = new NoTestCustomer();

        if (
            !$nameRule->passes('customer_name', $validated['customer_name']) ||
            !$emailRule->passes('email', $validated['email'])
        ) {
            Log::info('Skipping email due to spam-like customer details.', [
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
            ]);
            return true;
        }

        try {
            $customer = [
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
            ];

            $garage = GarageDetails::first();
            $garageEmail = $garage?->email;

            // Send to customer
            Mail::to($customer['email'])->send(new SendMailToCustomer($orderId, $customer, $garage));

            // Send to owner
            $ownerEmail = 'info@digitalideasltd.co.uk';
            Mail::to($ownerEmail)->send(new OrderToGarage($orderId, $customer, $garage));

            // Send to garage
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
    public function orderSuccess(Request $request)
    {
        $this->clearCartAndCheckoutSession();
        $isNewCustomer = Session::get('isNewCustomer', false);
        Session::forget('isNewCustomer');
        $response = response()->view('ordersuccess', [
            'isNewCustomer' => $isNewCustomer,
        ]);

        $this->preventPageCaching($response);

        return $response;
    }
    public function orderFailure(Request $request)
    {
        $this->clearCartAndCheckoutSession();
        $isNewCustomer = Session::get('isNewCustomer', false);
        Session::forget('isNewCustomer');
        $response = response()->view('orderfailure', [
            'isNewCustomer' => $isNewCustomer,
        ]);

        $this->preventPageCaching($response);

        return $response;
    }
    private function clearCartAndCheckoutSession()
    {
        $keysToClear = [
            'cart',
            'cartTotalPrice',
            'OrderType',
            'postcode_data',
            'vehicleData',
            'bookingDetails',
            'shippingPricePerJob',
            'shippingPricePerTyre',
            'checkout_token',
        ];
        foreach ($keysToClear as $key) {
            Session::forget($key);
        }
        session()->regenerateToken();
    }
    private function preventPageCaching(Response $response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
    }
}
