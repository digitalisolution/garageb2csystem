<?php

namespace App\Http\Controllers\ViewController;


// use App\Services\ApiOrderingServiceFactory;
use App\Models\Workshop;
use App\Models\Customer;
use App\Models\WorkshopService;
use App\Models\CarService;
use App\Models\WorkshopTyre;
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
use App\Models\GarageDetails;
use App\Models\RegionCounty;
use App\Models\VehicleDetail;
use App\Models\Countries;
use Illuminate\Support\Facades\Session;
use App\Services\DojoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Services\EmailValidationService;
use App\Services\ApiOrderingService;
use App\Services\UpdateOrderQtyService;
use App\Rules\NoTestCustomer;

class CheckoutController extends Controller
{
    

    protected $dojoService;
    protected $emailValidationService;
    protected $apiOrderingService;
    protected $updateOrderQtyService;

    /**
     * Constructor to inject dependencies.
     *
     * @param DojoService $dojoService
     * @param EmailValidationService $emailValidationService
     * @param ApiOrderingService $apiOrderingService
     * @param UpdateOrderQtyService $updateOrderQtyService
     */
    public function __construct(DojoService $dojoService,EmailValidationService $emailValidationService,ApiOrderingService $apiOrderingService,UpdateOrderQtyService $updateOrderQtyService)
    {
        $this->dojoService = $dojoService;
        $this->emailValidationService = $emailValidationService;
        $this->apiOrderingService = $apiOrderingService;
        $this->updateOrderQtyService = $updateOrderQtyService;
    }

    /**
     * Process the checkout request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Get the cart items from the session
        $cart = session('cart', []);
        $cartItems = [];
        $total = 0;
        if (empty($cart)) {
            // Redirect to home with an error message if the cart is empty
            return redirect()->route('home')->with('error', 'Your cart is empty. Please add items to the cart before proceeding to checkout.');
        }
        $token = uniqid('checkout_', true);
        Session::put('checkout_token', $token);
        // Process each item in the cart
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                $product = null;

                // Fetch the product details based on type
                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::where('service_id', $item['id'])->first();
                }

                // If the product exists, prepare cart item details
                if ($product) {
                    $priceField = $item['type'] === 'tyre' ? 'tyre_fullyfitted_price' : 'cost_price';
                    $cartItem = [
                        'product_id' => $product->product_id ?? $product->service_id,
                        'price' => $product->$priceField ?? 0,
                        'model' => $product->tyre_model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'type' => $item['type'], // Add type for identification
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'quantity' => $item['quantity'],
                        'tax_class_id' => $product->tax_class_id ?? '',
                        'total' => ($product->$priceField ?? 0) * $item['quantity'],
                    ];

                    // Add `ean` and `sku` only for `tyre` type
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
            return $event['start'] && $event['end']; // Exclude events with invalid dates
        });
        $customer = Auth::guard('customer')->user();
        $vehicleDetails = collect();
        $billingDetails = [];
        $counties = RegionCounty::pluck('name', 'zone_id');
        $countries = Countries::pluck('name', 'country_id');
        // If logged in, fetch customer data
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
        // dd($billingDetails);
        $bookingDetails = session('bookingDetails', []);
        $shippingData = session('postcode_data', []);
        $VehicleDetails = session('vehicleData', []);
        // dd($VehicleDetails);
        // Pass all data to the view
        return view('checkout', [
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
        ]);
    }
    public function refresh()
    {
        // Get the cart items from the session
        $cart = session('cart', []);
        $cartItems = [];
        $total = 0;
        if (empty($cart)) {
            // Redirect to home with an error message if the cart is empty
            return redirect()->route('home')->with('error', 'Your cart is empty. Please add items to the cart before proceeding to checkout.');
        }
        $token = uniqid('checkout_', true);
        Session::put('checkout_token', $token);
        // Process each item in the cart
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['type'])) {
                $product = null;

                // Fetch the product details based on type
                if ($item['type'] === 'tyre') {
                    $product = TyresProduct::find($item['id']);
                } elseif ($item['type'] === 'service') {
                    $product = CarService::where('service_id', $item['id'])->first();
                }

                // If the product exists, prepare cart item details
                if ($product) {
                    $priceField = $item['type'] === 'tyre' ? 'tyre_fullyfitted_price' : 'cost_price';
                    $cartItem = [
                        'product_id' => $product->product_id ?? $product->service_id,
                        'price' => $product->$priceField ?? 0,
                        'model' => $product->tyre_model ?? ($item['type'] === 'service' ? $product->name : ''),
                        'type' => $item['type'],
                        'fitting_type' => $item['fitting_type'] ?? null,
                        'quantity' => $item['quantity'],
                        'tax_class_id' => $product->tax_class_id ?? '',
                        'total' => ($product->$priceField ?? 0) * $item['quantity'],
                    ];

                    // Add `ean` and `sku` only for `tyre` type
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
        // dd($billin);
        $bookingDetails = session('bookingDetails', []);
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
        ]);
    }
    // public function checkEmailExists(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //     ]);

    //     $email = $request->input('email');

    //     // Check if the email exists in the database
    //     $exists = Customer::where('customer_email', $email)->exists();

    //     return response()->json(['exists' => $exists]);
    // }
       
    public function autoSaveCustomer(Request $request)
    {
        // Retrieve customer data from the session
        $customerData = Session::get('customer');
    
        if (!$customerData) {
            return response()->json(['success' => false, 'error' => 'No customer data found in session.'], 400);
        }
    
        try {
            // Validate the email using EmailValidationService
            $validationResult = $this->emailValidationService->validateEmail(
                $customerData['email'],
                $customerData['email'] // Use the app's default sender email address
            );
    
            // If the email is invalid, return an error response
            if (!$validationResult['status']) {
                throw new \Exception($validationResult['message']);
            }
    
            // Check if the email already exists in the database
            $existingCustomer = Customer::where('customer_email', $customerData['email'])->first();
    
            if ($existingCustomer) {
                // Email exists, check if the user is logged in
                $loggedInCustomer = Auth::guard('customer')->user();
                if ($loggedInCustomer && $loggedInCustomer->id === $existingCustomer->id) {
                    // Logged-in user matches the existing customer, update the data
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
                // Email does not exist, create a new customer record
                $password = Str::random(10); // Generate a random password for the new customer
    
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
                    'password' => Hash::make($password), // Hash the generated password
                ]);
    
                // Send a welcome email with the generated password
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

        // Retrieve or create the customer session
        $customerData = Session::get('customer', []);
        $customerData[$fieldName] = $fieldValue;

        // Save updated data in the session
        Session::put('customer', $customerData);

        // Debugging: Check if session data is correct
        // Log::info('Updated customer session data:', $customerData);

        // Return response with success
        return response()->json(['success' => true, 'session_data' => $customerData]);
    }
    public function submit(Request $request)
    {
        // Validate the token
        $storedToken = Session::get('checkout_token');
        $submittedToken = $request->input('checkout_token');

        // if ($storedToken !== $submittedToken) {
        //     return back()->withErrors(['error' => 'Invalid or expired form submission.'])->withInput();
        // }
        $bookingDetails = Session::get('bookingDetails', []);
        if (empty($bookingDetails) || !isset($bookingDetails['start']) || !isset($bookingDetails['end'])) {
            return back()->withErrors(['booking_slot' => 'Booking slot is not selected. Please select a booking slot before submitting the order.'])->withInput();
        }

        // Invalidate the token after use
        Session::forget('checkout_token');
        // dd($request);
        $rules = $this->getValidationRules();
        $data = $request->all();

        // If new_reg_number is provided, remove reg_number
        if (!empty($data['new_reg_number'])) {
            unset($data['reg_number']);
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        // Log::info('Request validated:', $validated);

         // Fetch county and country names
   

    $workshopDefaults = $this->getWorkshopDefaults();
    $workshopData = $this->prepareWorkshopData($validated, $workshopDefaults);
        // dd($workshopData);
        try {
            DB::beginTransaction();

            $customerId = $this->handleCustomer($validated);
            // Log::info('Customer saved:', ['id' => $customerId]);
            
            $workshopData['customer_id'] = $customerId;
            $workshop = $this->saveWorkshop($workshopData);
            $workshopId = $workshop['id'];
            // Log::info('Workshop saved:', ['id' => $workshop->id]);

            $this->saveCartItems($workshop->id);
            // Log::info('Cart items saved.');

            $this->saveCalendarBooking($workshop);
            // Log::info('Calendar details saved.');
            $cartItems = Session::get('cart', []);
            // dd($cartItems);
            // $this->updateStockQty($cartItems);

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
            
            foreach (Session::all() as $key => $value) {
                if (Str::startsWith($key, 'login_customer_')) {
                    continue;
                }
                Session::forget($key);
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
            'new_reg_number' => 'nullable|string|min:6|max:10|regex:/^[A-Za-z0-9\-]+$/',
            'reg_number' => 'nullable|string|min:6|max:10|regex:/^[A-Za-z0-9\-]+$/',
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
        $cartItems = Session::get('cart', []);
        $localTimezone = 'Europe/London';
        $bookingDetails = Session::get('bookingDetails', []);
        // Initialize fitting type variable
        $fittingType = null;
    
        // Extract fitting_type from cart items
        foreach ($cartItems as $item) {
            if (isset($item['fitting_type'])) {
                // Use the first fitting_type encountered
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
    
        // Retrieve vehicle details from session
        $VehicleDetails = Session::get('vehicleData', []);
    
        // Determine the status based on payment method
        $status = 'booked'; // Default status
        if (
            isset($validated['payment_method']) &&
            $validated['payment_method'] !== 'pay_at_fitting_center'
        ) {
            $status = 'failed'; // Set status to "failed" for other payment methods
        }
     // Determine which registration number to use
     $vehicleRegNumber = $validated['new_reg_number'] ?? $validated['reg_number'];

     // Validate that at least one registration number exists
     if (!$vehicleRegNumber) {
         throw new \Exception('Either reg_number or new_reg_number must be provided.');
     }
     $countyName = RegionCounty::where('zone_id', $validated['county'])->value('name');
     $countryName = Countries::where('country_id', $validated['country'])->value('name');
 
     // Add county and country names to validated data
     $validated['county_name'] = $countyName;
     $validated['country_name'] = $countryName;
        // Return prepared workshop data
        return array_merge($defaults, [
            'name' => $validated['customer_name'] ?? 'Unnamed Workshop',
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
        ]);
    }
    protected function handleCustomer(array $validated)
    {
        $customerId = Session::get('customer_id');

        if (!$customerId) {
            $validationResult = $this->emailValidationService->validateEmail(
                $validated['email'],
                $validated['email'] // Use the app's default sender email address
            );
    
            // If the email is invalid, return an error response
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
                        'postcode' => $validated['postcode'] ?? $existingCustomer->shipping_address_postcode,
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
    /**
 * Create a relationship between the customer and the vehicle.
 *
 * @param int $customerId
 * @param string|null $vehicleRegNumber
 * @return void
 */
private function attachVehicleToCustomer(int $customerId, ?string $vehicleRegNumber): void
{
    // dd($vehicleRegNumber);
    if ($vehicleRegNumber) {
        // Find or create the vehicle by registration number
        $vehicle = VehicleDetail::firstOrCreate(
            ['vehicle_reg_number' => $vehicleRegNumber],
            ['make' => '', 'model' => '', 'year' => ''] // Default values for new vehicles
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
        $shippingPricePerJob = Session::get('shippingPricePerJob');
        $shippingPricePerTyre = Session::get('shippingPricePerTyre');
        $shippingPostcode = $postcodeData['postcode'] ?? null;
        $shippingType = $postcodeData['ship_type'] ?? 'job';
        $shippingPriceWithoutVAT = 0;

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
                        'ref_type' => 'workshop',
                        'product_ean' => $item['ean'],
                        'product_sku' => $item['sku'],
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'fitting_type' => $item['fitting_type'],
                        'tax_class_id' => $item['tax_class_id'],
                        'description' => $item['desc'],
                        'model' => $item['model'],
                        'price' => $item['price'],
                        'margin_rate' => $item['price'],
                        'supplier' => $item['supplier'],
                        'product_type' => $item['type'],
                        'shipping_postcode' => $shippingPostcode,
                        'shipping_price' => $shippingPriceWithoutVAT,
                        'shipping_tax_id' => $shippingTaxId,
                    ];
                    WorkshopTyre::create($tyreData);
                }

                // Handle services
                if ($item['type'] === 'service') {
                    $serviceData = [
                        'workshop_id' => $workshopId,
                        'service_id' => $item['id'],
                        'ref_type' => 'workshop',
                        'service_name' => $item['model'],
                        'service_price' => $item['price'],
                        'fitting_type' => $item['fitting_type'],
                        'service_quantity' => $item['quantity'],
                        'tax_class_id' => $item['tax_class_id'],
                        'product_type' => $item['type'],
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
        // Retrieve the booking details from the session
        $slotDetails = Session::get('bookingDetails', []);
        $workshopId = $workshopData->id;
        $workshopName = $workshopData->name; 
        
        $customerName = $workshopName ?: 'Unknown Customer';
        
        $localTimezone = 'Europe/London';
        
        $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $slotDetails['start'], $localTimezone)
            ->setTimezone('UTC');
        
        $endUtc = Carbon::createFromFormat('Y-m-d H:i:s', $slotDetails['end'], $localTimezone)
            ->setTimezone('UTC');
        
        Booking::create([
            'workshop_id' => $workshopId,
            'title' => $customerName,
            'start' => $startUtc->format('Y-m-d H:i:s'),
            'end' => $endUtc->format('Y-m-d H:i:s'),
        ]);
    }

    
    protected function processOrder(array $validated,$workshop, $orderId)
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
        foreach (Session::all() as $key => $value) {
            if (Str::startsWith($key, 'login_customer_')) {
                continue;
            }
            Session::forget($key);
        }
        // Check if a new customer was created during checkout
        $isNewCustomer = Session::get('isNewCustomer', false);

        // Clear the session flag after retrieving it
        Session::forget('isNewCustomer');

        // Pass the $isNewCustomer variable to the view
        return view('ordersuccess', [
            'isNewCustomer' => $isNewCustomer,
        ]);
    }
}
