<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\ViewController\HomeController;
use App\Http\Controllers\ViewController\ServiceViewController;
use App\Http\Controllers\ServiceController;
// use App\Http\Controllers\ViewController\SlugController;
use App\Http\Controllers\ViewController\SitemapController;
use App\Http\Controllers\TyrePricingController;
use App\Http\Controllers\MobileTyrePricingController;
use App\Http\Controllers\ViewController\TyresProductController;
use App\Http\Controllers\TyresController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\CalendarSettingController;
use App\Http\Controllers\Gateways\DojoController;
use App\Http\Controllers\ClickTrackingController;
use App\Http\Controllers\HeaderMenuController;
use App\Http\Controllers\PluginController;

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ApiOrderController;
use App\Http\Controllers\MetaSettingsController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\ViewController\CarserviceProductController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ViewController\CustomerAccountController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MailTyrePricingController;
use App\Http\Controllers\TyreImportController;
// use App\Http\Controllers\BondApiController;
//use App\Http\Controllers\EdenApiController;
use App\Http\Controllers\VrmController;
use App\Http\Controllers\VehicleDetailController;
use App\Http\Controllers\OtpVerificationController;
// use App\Http\Controllers\ViewController\BrandController;
use App\Http\Controllers\ViewController\CalendarController;
use App\Http\Controllers\HTMLTemplateController;
use App\Http\Controllers\GarageDetailsController;
use App\Http\Controllers\ViewController\CartController;
use App\Http\Controllers\ViewController\CheckoutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\AppointmentController;

use App\Http\Controllers\MobilefittingformController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
 *  File Name              :
 *  Type                   :
 *  Description            :
 *  Author                 : Digital Ideas Ltd
 *  Contact                : 9658476170
 *  Email                  : info@digitalideasltd.co.uk
 *  Date                   : 12/12/2018
 *  Modified By            :
 *  Date of Modification   :
 *  Purpose of Modification:
 *
 */


// Route::get('/', function () {
// 	return include_dynamic_view('welcome');
// });
// Start: Frontend Routes

// Route::post('AutoCare/supplier/import', [SupplierController::class, 'import'])->name('supplier.import');




// Route::get('tyres/get-profiles', [TyresProductController::class, 'getProfiles'])->name('tyres.getProfiles');
// Route::get('tyres/get-diameters', [TyresProductController::class, 'getDiameters'])->name('tyres.getDiameters');
// Route::get('tyreslist', [TyresProductController::class, 'filter'])->name('tyres.filter');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/delete', [CartController::class, 'delete'])->name('cart.delete');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::get('/cart-refresh', [CheckoutController::class, 'refresh'])->name('cart.refresh');


Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/sitemap', [SitemapController::class, 'index'])->name('sitemap');
Route::post('/track-phone-click', [ClickTrackingController::class, 'trackPhoneClick'])->name('click.trackPhoneClick');

// Route::post('/checkout/submit', [CheckoutController::class, 'submitOrder'])->name('checkout.submit');

Route::post('/checkout/store-in-session', [CheckoutController::class, 'storeInSession'])->name('checkout.storeInSession');
// Route::post('/checkout/submit', [CheckoutController::class, 'submitCheckout'])->name('checkout.submit');
Route::post('/checkout/auto-save-customer', [CheckoutController::class, 'autoSaveCustomer'])->name('checkout.autoSaveCustomer');
// Route to display the payment page
Route::get('/payment/make', [PaymentController::class, 'makePaymentWebsite'])->name('payment.make');

// Route to handle the payment form submission
Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

Route::get('/calendar', [CalendarController::class, 'index']);
Route::post('/calendar/book', [CalendarController::class, 'book']);


Route::get('/dojo/make-payment', [DojoController::class, 'makePaymentWebsite'])->name('dojo.make-payment');
Route::get('/dojo/callback', [DojoController::class, 'callback'])->name('dojo.callback');

//appointment
// Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
// Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
// if (get_option('appointment_form', 0)) {
    Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
    Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');

    Route::get('/mobile-fitting-availablitiy', [MobilefittingformController::class, 'create'])->name('mobilefittingform.create');
    Route::post('/mobile-fitting-availablitiy', [MobilefittingformController::class, 'store'])->name('mobilefittingform.store');
// }

// Route::get('/brand', function () {
//     return view('layouts.app');
// });

// Route::get('/tyre-sizes', [TyresProductController::class, 'getTyreSizes']);
Route::get('/recommended-tyre', [TyresProductController::class, 'recommendedTyres'])->name('tyres.recommendedTyres');
Route::get('/search', [TyresProductController::class, 'tyreslist'])->name('tyreslist');
Route::get('/tyreslist', [TyresProductController::class, 'tyreslist'])->name('tyres.tyreslist');
Route::get('/tyreslist/filter', [TyresProductController::class, 'filter'])->name('tyres.filter');
Route::get('/tyreslist/get-profiles', [TyresProductController::class, 'getProfiles'])->name('tyres.getProfiles');
Route::get('/tyreslist/get-diameters', [TyresProductController::class, 'getDiameters'])->name('tyres.getDiameters');
// Route::get('/tyre-cards', [TyresProductController::class, 'filter'])->name('tyres.filter');
Route::get('/getSeasonOptions', [TyresProductController::class, 'getSeasonOptions'])->name('tyres.getSeasonOptions');
Route::get('/getWetGripOptions', [TyresProductController::class, 'getWetGripOptions'])->name('tyres.getWetGripOptions');
Route::get('/getTyreBrandOptions', [TyresProductController::class, 'getTyreBrandOptions'])->name('tyres.getTyreBrandOptions');
Route::get('/getFuelEfficiencyOptions', [TyresProductController::class, 'getFuelEfficiencyOptions'])->name('tyres.getFuelEfficiencyOptions');
Route::get('/getPriceRange', [TyresProductController::class, 'getPriceRange'])->name('tyres.getPriceRange');

Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process');


Route::post('/api/place-order', [ApiOrderController::class, 'placeOrder']);

Route::get('/', [HomeController::class, 'getVehicleDetails'])->name('home');

Route::get('/models', [CarserviceProductController::class, 'getModels'])->name('models');
Route::get('/years', [CarserviceProductController::class, 'getYears'])->name('years');
Route::get('/engines', [CarserviceProductController::class, 'getEngines'])->name('engines');


Route::get('/service', [ServiceViewController::class, 'services'])->name('service'); 
Route::post('/service-enquiry', [ServiceViewController::class, 'handleEnquiry'])->name('service.enquiry.submit');

Route::get('/cart/fetch', [CartController::class, 'fetchCartItems'])->name('cart.fetch');

// Route for handling the form submission
Route::post('/checkout', [CheckoutController::class, 'submit'])->name('checkout.submit');

Route::post('/save-selected-slot', [CalendarController::class, 'saveSelectedSlot'])->name('save.selected.slot');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemapIndex']);
Route::get('/sitemap-pages.xml', [SitemapController::class, 'sitemapPages']);
Route::get('/sitemap-tyresizes.xml', [SitemapController::class, 'sitemapTyreSizes']);
Route::get('/sitemap-brand.xml', [SitemapController::class, 'sitemapBrands']);
Route::get('/sitemap-blogs.xml', [SitemapController::class, 'sitemapBlogs']);
Route::get('/sitemap-manufacturer-models.xml', [SitemapController::class, 'sitemapManufacturerModels']);


Route::get('/tyres-size/{width}-{profile}-{diameter}', [TyresProductController::class, 'tyreslist']);
Route::get('/', function () {
    return include_dynamic_view('home');
})->name('home');

Route::post('/store-vehicle-data', [CartController::class, 'storeVehicleData'])->name('store.vehicle.data');

Route::get('/js/plugin.js', function () {
    $domain = str_replace('.', '-', request()->getHost());
    $path = public_path("frontend/{$domain}/js/plugin.js");

    if (!file_exists($path)) {
        abort(404, 'Plugin file not found for this domain.');
    }

    return response()->file($path, [
        'Content-Type' => 'application/javascript'
    ]);
});
Route::middleware(['web', 'plugin.domain'])->group(function () {
Route::get('/plugin/search', [PluginController::class, 'showSearchForm'])->name('plugin.search.form');
Route::get('/plugin/vehicle-search', [VrmController::class, 'showVehicleSearch'])->name('plugin.vehicle-search');
Route::get('/plugin/search/submit', [PluginController::class, 'redirectToSearchResults'])->name('plugin.search.submit');
});

Route::middleware('verify.token')->group(function () {
    Route::get('/vehicle-data', [VrmController::class, 'getVehicleAndMotDetails'])
        ->name('vehicle.data');
    // Rate limit: 10 requests per minute
});
Route::middleware('verify.token')->group(function () {
    Route::get('/vehicle-mot-data', [VrmController::class, 'getVehicleAndMotDetails'])
        ->name('vehicleMot.data');
    // Rate limit: 10 requests per minute
});
// Route::get('/about-us', function () {
// 	return view('about'); // Ensure `resources/views/about.blade.php` exists
// })->name('aboutUs');

// Route::get('/contact', function () {
//     // Dynamically include the contact view based on the domain and theme
//     return include_dynamic_view('contact');
// })->name('contact');

Route::get('/checkout/ordersuccess', [CheckoutController::class, 'orderSuccess'])->name('checkout.ordersuccess');
// Route::get('/checkout/ordersuccess', function () {
//     return include_dynamic_view('ordersuccess');
// })->name('checkout.ordersuccess');

Route::get('/payment/make?workshopid=', function () {
})->name('checkout.repayment');

Route::get('/checkout/session-data', function () {
    // Dump all session data for debugging
    return response()->json(session()->all());
})->name('checkout.sessionData');


// Route::get('/services', function () {
//     return include_dynamic_view('services'); // Ensure `resources/views/services.blade.php` exists
// })->name('services');

Route::get('/tyres', function () {
    return include_dynamic_view('tyres'); // Ensure `resources/views/tyres.blade.php` exists
})->name('tyres');


Route::get('/searchVehicle', function () {
    return include_dynamic_view('searchVehicle'); // Ensure `resources/views/contact.blade.php` exists
})->name('searchVehicle');

Route::get('/searchByMakeYear', function () {
    return include_dynamic_view('searchByMakeYear'); // Ensure `resources/views/contact.blade.php` exists
})->name('searchByMakeYear');

// Route::get('/services', function () {
//     return include_dynamic_view('service'); // Ensure `resources/views/contact.blade.php` exists
// })->name('services');

Route::get('/calendar-settings', [CalendarSettingController::class, 'getCalendarSettings']);
Route::get('/calendar-website-settings', [CalendarSettingController::class, 'getWebsiteCalendarSettings']);
Route::post('/calculate-shipping', [MobileTyrePricingController::class, 'calculateShipping'])->name('calculateShipping');
Route::post('/store-postcode-session', [MobileTyrePricingController::class, 'storePostcodeSession'])->name('storePostcodeSession');
Route::post('/calculate-mailshipping', [MailTyrePricingController::class, 'calculateMailShipping'])->name('calculateMailShipping');
Route::post('/store-mailpostcode-session', [MailTyrePricingController::class, 'storeMailPostcodeSession'])->name('storeMailPostcodeSession');

Route::post('/check-email', [CheckoutController::class, 'checkEmailExists'])->name('check.email');

Route::middleware('guest:customer')->group(function () {
    Route::get('/customer/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('customer.register');
    Route::post('/customer/register', [CustomerAuthController::class, 'customerRegister']);
    Route::get('/customer/login', [CustomerAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/customer/login', [CustomerAuthController::class, 'customerlogin']);
    Route::get('/customer/password/reset', [App\Http\Controllers\Auth\CustomerForgotPasswordController::class, 'showLinkRequestForm'])->name('customer.password.request');
    Route::post('/customer/password/email', [App\Http\Controllers\Auth\CustomerForgotPasswordController::class, 'sendResetLinkEmail'])->name('customer.password.email');
    Route::get('/customer/password/reset/{token}', [App\Http\Controllers\Auth\CustomerResetPasswordController::class, 'showResetForm'])->name('customer.password.reset');
    Route::post('/customer/password/reset', [App\Http\Controllers\Auth\CustomerResetPasswordController::class, 'reset'])->name('customer.password.update');
    // Route::get('/customer/forgot-password', [CustomerAuthController::class, 'ShowforgotPassward'])->name('customer.forgot-password');
    // Route::post('/customer/forgot-password', [CustomerAuthController::class, 'forgotPassward']);
});
Route::get('customer/password/email', function () {
    return redirect('/customer/password/reset');
});
Route::middleware('auth:customer')->group(function () {
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::get('/customer/myaccount', [CustomerAccountController::class, 'myaccount'])->name('customer.myaccount');
    Route::get('/customer/orders', [CustomerAccountController::class, 'orders'])->name('customer.orders');
    Route::get('/customer/vehicles', [CustomerAccountController::class, 'vehicles'])->name('customer.vehicles');
    Route::get('/customer/invoices', [CustomerAccountController::class, 'invoices'])->name('customer.invoices');
    Route::get('/customer/statement', [CustomerAccountController::class, 'statements'])->name('customer.statement');


    Route::post('/customer/update-profile', [CustomerAccountController::class, 'updateProfile'])->name('customer.update-profile');
    Route::post('/customer/update-password', [CustomerAccountController::class, 'updatePassword'])->name('customer.update-password');
    Route::post('/customer/update-billing-address', [CustomerAccountController::class, 'updateBillingAddress'])->name('customer.update-billing-address');
    Route::post('/customer/update-shipping-address', [CustomerAccountController::class, 'updateShippingAddress'])->name('customer.update-shipping-address');


    Route::get('/customer/vehicles/create', [CustomerAccountController::class, 'createVehicle'])->name('customer.vehicles.create');
    Route::get('/customer/vehicles/{id}/edit', [CustomerAccountController::class, 'editVehicle'])->name('customer.vehicles.edit');
    Route::post('/customer/vehicles', [CustomerAccountController::class, 'storeVehicle'])->name('customer.vehicles.store');
    Route::put('/customer/vehicles/{id}', [CustomerAccountController::class, 'updateVehicle'])->name('customer.vehicles.update');
    Route::delete('/customer/vehicles/{id}', [CustomerAccountController::class, 'deleteVehicle'])->name('customer.vehicles.delete');


    Route::get('/customer/orders/job-{id}', [CustomerAccountController::class, 'viewOrder'])->name('customer.orders.view');
    Route::get('/customer/invoice/inv-{id}', [CustomerAccountController::class, 'viewInvoice'])->name('customer.invoice.view');
});
// Route::prefix('customer')->group(function () {
//     Route::get('password/reset', [App\Http\Controllers\Auth\CustomerForgotPasswordController::class, 'showLinkRequestForm'])->name('customer.password.request');
//     Route::post('password/email', [App\Http\Controllers\Auth\CustomerForgotPasswordController::class, 'sendResetLinkEmail'])->name('customer.password.email');
//     Route::get('password/reset/{token}', [App\Http\Controllers\Auth\CustomerResetPasswordController::class, 'showResetForm'])->name('customer.password.reset');
//     Route::post('password/reset', [App\Http\Controllers\Auth\CustomerResetPasswordController::class, 'reset'])->name('customer.password.update');
// });

Auth::routes();
// Auth::routes(['login' => false]); // Disable default login
// Auth::routes(['register' => false]); // Disable default register
// Auth::routes(['password' => false]); // Disable default register
// Prevent access to default password reset routes
Route::match(['get', 'post'], 'password/reset', function () {
    abort(404);
})->name('password.reset.blocked');



Route::middleware(['dashboard'])->group(function () {


    Route::get('webmaster/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('webmaster.password.request');
    Route::post('webmaster/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('webmaster.password.email');
    Route::get('webmaster/password/email', function () {
        return redirect('/webmaster/password/reset');
    });
    
    Route::get('webmaster/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('webmaster.password.reset');
    Route::post('webmaster/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);
    
    Route::get('webmaster/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('webmaster.login');
    Route::post('webmaster/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    Route::get('webmaster/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('webmaster.register');
    Route::post('webmaster/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    });


Route::middleware('auth:web')->group(function () {
    Route::get('/AutoCare/html-templates/create', [HTMLTemplateController::class, 'create'])->name('AutoCare.html-templates.create');
    // Route::any('test',function(){
    // print_r(Auth::user()->role_id);
    // });
    // Admin Routes
    // HTML Templates Management Routes
    Route::get('/AutoCare/click-report', [ClickTrackingController::class, 'clickReport'])->name('admin.click.report');
    Route::get('/AutoCare/api-orders', [ApiOrderController::class, 'viewApiOrders'])->name('viewApiOrders');

    Route::get('/AutoCare/get-services', [ServiceController::class, 'getServices']);


    // Route::get('/calendar-settings', [CalendarSettingController::class, 'getCalendarSettings']);
    Route::get('/get-events', [CalendarSettingController::class, 'getEvents']);
    Route::get('/get-booking-details/{id}', [BookingController::class, 'getBookingDetails']);
    Route::post('/create-booking', [BookingController::class, 'createBooking']);

    Route::prefix('AutoCare/html-templates')->group(function () {
        Route::get('/', [HTMLTemplateController::class, 'index'])->name('AutoCare.html-templates.index');
        Route::get('/create', [HTMLTemplateController::class, 'create'])->name('AutoCare.html-templates.create');
        Route::post('/', [HTMLTemplateController::class, 'store'])->name('AutoCare.html-templates.store');
        Route::get('/{id}', [HTMLTemplateController::class, 'show'])->name('AutoCare.html-templates.show');
        Route::get('/{id}/edit', [HTMLTemplateController::class, 'edit'])->name('AutoCare.html-templates.edit');
        Route::put('/{id}', [HTMLTemplateController::class, 'update'])->name('AutoCare.html-templates.update');
        Route::delete('/{id}', [HTMLTemplateController::class, 'destroy'])->name('AutoCare.html-templates.destroy');
    });

    Route::prefix('AutoCare/blogs')->group(function () {
        Route::get('/blogs', [BlogController::class, 'index'])->name('AutoCare.blogs.index');
        Route::get('/blogs/create', [BlogController::class, 'create'])->name('AutoCare.blogs.create');
        Route::post('/blogs', [BlogController::class, 'store'])->name('AutoCare.blogs.store');
        Route::get('/blogs/{blog_id}/edit', [BlogController::class, 'edit'])->name('AutoCare.blogs.edit');
        Route::put('/blogs/{blog_id}', [BlogController::class, 'update'])->name('AutoCare.blogs.update');
        Route::delete('/blogs/{blog_id}', [BlogController::class, 'destroy'])->name('AutoCare.blogs.destroy');

    });

    Route::prefix('AutoCare/blogs/categories')->name('AutoCare.blogs.blog_categories.')->group(function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('index');
        Route::get('/create', [BlogCategoryController::class, 'create'])->name('create');
        Route::post('/', [BlogCategoryController::class, 'store'])->name('store');
        Route::get('/{category_id}/edit', [BlogCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category_id}', [BlogCategoryController::class, 'update'])->name('update');
        Route::delete('/{category_id}', [BlogCategoryController::class, 'destroy'])->name('destroy');
    });

    // Route::get('AutoCare/garage_details/', [GarageDetailsController::class, 'index'])->name('AutoCare.garage_details.index');
    Route::prefix('AutoCare')->group(function () {
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
          //header menu
        Route::get('/headermenu', [HeaderMenuController::class, 'index'])->name('headermenu.index');
        Route::get('/headermenu/create', [HeaderMenuController::class, 'create'])->name('headermenu.create');
        Route::post('/headermenu', [HeaderMenuController::class, 'store'])->name('headermenu.store');
        Route::get('/headermenu/{page}/edit', [HeaderMenuController::class, 'edit'])->name('headermenu.edit');
        Route::put('/headermenu/{page}', [HeaderMenuController::class, 'update'])->name('headermenu.update');
        Route::delete('/headermenu/{page}', [HeaderMenuController::class, 'destroy'])->name('headermenu.destroy');
        Route::get('/get-slugs/{type}', [HeaderMenuController::class, 'getSlugs'])->name('get.slugs');
        //brands
        // Route::resource('brands', BrandController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('blog', BlogController::class);
        Route::resource('services', ServiceController::class)->except(['show']);


        Route::resource('vehicles', VehicleDetailController::class);
        Route::get('/vehicles', [VehicleDetailController::class, 'index'])->name('AutoCare.vehicles.index');
        Route::get('/vehicles/create', [VehicleDetailController::class, 'create'])->name('AutoCare.vehicles.create');
        Route::post('/vehicles', [VehicleDetailController::class, 'store'])->name('AutoCare.vehicles.store');
        Route::get('/vehicles/{id}/edit', [VehicleDetailController::class, 'edit'])->name('AutoCare.vehicles.edit');
        Route::put('/vehicles/{id}', [VehicleDetailController::class, 'update'])->name('AutoCare.vehicles.update');
        Route::delete('/vehicles/{id}', [VehicleDetailController::class, 'destroy'])->name('AutoCare.vehicles.destroy');

        Route::get('/garage_details', [GarageDetailsController::class, 'index'])->name('AutoCare.garage_details.index');
        Route::get('/garage_details/create', [GarageDetailsController::class, 'create'])->name('AutoCare.garage_details.create');
        Route::post('/garage_details', [GarageDetailsController::class, 'store'])->name('AutoCare.garage_details.store');
        Route::get('/garage_details/{garage}/edit', [GarageDetailsController::class, 'edit'])->name('AutoCare.garage_details.edit');
        Route::put('/garage_details/{garage}', [GarageDetailsController::class, 'update'])->name('AutoCare.garage_details.update');
        Route::delete('/garage_details/{garage}', [GarageDetailsController::class, 'destroy'])->name('AutoCare.garage_details.destroy');
    });
    Route::prefix('AutoCare')->group(function () {
        // Register resourceful routes for CalendarSettingController
        Route::resource('/calendar', CalendarSettingController::class);
        Route::resource('/mobile_fitting_pricing', MobileTyrePricingController::class);
        Route::post('/save-shipping-charges', [MobileTyrePricingController::class, 'saveShippingCharges'])->name('save.shipping.charges');
        Route::resource('/mail_order_pricing', MailTyrePricingController::class);
        Route::post('/save-mailshipping-charges', [MailTyrePricingController::class, 'saveMailShippingCharges'])->name('save.mailshipping.charges');
    });
    Route::get('/tinymce-api-key', function () {
        return response()->json(['key' => env('TINYMCE_API_KEY')]);
    });


    Route::prefix('AutoCare/meta-settings')->group(function () {
        Route::get('/', [MetaSettingsController::class, 'index'])->name('AutoCare.meta-settings.index');
        Route::get('/create', [MetaSettingsController::class, 'create'])->name('AutoCare.meta-settings.create');
        Route::post('/', [MetaSettingsController::class, 'store'])->name('AutoCare.meta-settings.store');
        Route::get('/{setting_id}', [MetaSettingsController::class, 'show'])->name('AutoCare.meta-settings.show');
        Route::get('/{setting_id}/edit', [MetaSettingsController::class, 'edit'])->name('AutoCare.meta-settings.edit');
        Route::put('/{setting_id}', [MetaSettingsController::class, 'update'])->name('AutoCare.meta-settings.update');
        Route::delete('/{setting_id}', [MetaSettingsController::class, 'destroy'])->name('AutoCare.meta-settings.destroy');
    });
    Route::prefix('AutoCare/general-settings')->group(function () {
        // Route::get('/', [GeneralSettingsController::class, 'index'])->name('AutoCare.general-settings.index');
        // Route::get('/create', [GeneralSettingsController::class, 'create'])->name('AutoCare.general-settings.create');
        // Route::post('/', [GeneralSettingsController::class, 'store'])->name('AutoCare.general-settings.store');
        // Route::get('/{id}', [GeneralSettingsController::class, 'show'])->name('AutoCare.general-settings.show');
        // Route::get('/{id}/edit', [GeneralSettingsController::class, 'edit'])->name('AutoCare.general-settings.edit');
        // Route::put('/{id}', [GeneralSettingsController::class, 'update'])->name('AutoCare.general-settings.update');
        // Route::delete('/{id}', [GeneralSettingsController::class, 'destroy'])->name('AutoCare.general-settings.destroy');

    Route::get('/', [GeneralSettingsController::class, 'index'])->name('settings.index');
    Route::put('/smtp', [GeneralSettingsController::class, 'updateSmtpDetails'])->name('settings.smtp.update');
    Route::put('/payment', [GeneralSettingsController::class, 'updatePayment'])->name('settings.payment.update');
    Route::post('/booking', [GeneralSettingsController::class, 'updateBooking'])->name('AutoCare.booking.update');
});


//estimates route

    Route::get('/AutoCare/estimate/add', 'EstimateController@save');
    Route::get('/AutoCare/estimate/add/{id}', 'EstimateController@save');
    Route::get('/AutoCare/estimate/addWorkshop/{id}', 'EstimateController@convertToWorkshop');
    Route::get('/validate-tyre-stock/{productId}', 'EstimateController@validateTyreStock');
    Route::post('/AutoCare/estimate/add', 'EstimateController@save');
    Route::get('/AutoCare/estimate/search', 'EstimateController@view');
    Route::post('/AutoCare/estimate/search', 'EstimateController@view')->name('AutoCare.estimate.view');
    Route::post('/AutoCare/estimate/update', 'EstimateController@save');
    Route::delete('/AutoCare/estimate/trash/{id}', 'EstimateController@trash');
    Route::get('/AutoCare/estimate/delete', 'EstimateController@trashedList');
    Route::get('/AutoCare/estimate/delete/{id}', 'EstimateController@permanemetDelete');
    Route::get('/AutoCare/estimate/view/{id}', 'EstimateController@viewIndivisual')->name('estimate.job.view');
    Route::get('/AutoCare/estimate/payment_history/{id}', 'EstimateController@viewpaymenthistory');
    Route::post('/AutoCare/estimate/send-estimate-email', 'EstimateController@sendEstimateEmail')->name('estimate.sendEstimateEmail');
    Route::get('/estimate/preview/{id}', 'EstimateController@previewEstimatePdf')->name('estimate.preview');
    Route::get('/estimate/download/{id}', 'EstimateController@downloadEstimatePdf')->name('estimate.download');

//estimate end

    Route::get('AutoCare/workshop/add/getTyreProducts', [TyresController::class, 'getTyreProducts'])->name('getTyreProducts');
    // Workshop :start
    Route::get('/dashboard', 'MasterformsController@dashboard');
    Route::get('/AutoCare/workshop/add', 'WorkshopController@save');
    Route::get('/AutoCare/workshop/add/{id}', 'WorkshopController@save');
    Route::get('/AutoCare/workshop/addinvoice/{id}', 'WorkshopController@convertToInvoice');
    Route::get('/validate-tyre-stock/{productId}', 'WorkshopController@validateTyreStock');
    Route::post('/AutoCare/workshop/add', 'WorkshopController@save');
    Route::get('/AutoCare/workshop/search', 'WorkshopController@view');
    Route::post('/AutoCare/workshop/search', 'WorkshopController@view')->name('AutoCare.workshop.view');
    Route::post('/AutoCare/workshop/update', 'WorkshopController@save');
    Route::delete('/AutoCare/workshop/trash/{id}', 'WorkshopController@trash');
    Route::get('/AutoCare/workshop/delete', 'WorkshopController@trashedList');
    Route::get('/AutoCare/workshop/delete/{id}', 'WorkshopController@permanemetDelete');
    Route::get('/AutoCare/workshop/view/{id}', 'WorkshopController@viewIndivisual')->name('workshop.job.view');
    Route::get('/AutoCare/workshop/invoice/{id}', 'WorkshopController@viewInvoice');
    Route::get('/AutoCare/workshop/search-invoice', 'WorkshopController@viewSearchInvoice');
    Route::post('/AutoCare/workshop/search-invoice', 'WorkshopController@viewSearchInvoice');
    Route::get('/AutoCare/workshop/payment_history/{id}', 'WorkshopController@viewpaymenthistory');
    Route::get('/AutoCare/payment-record', 'PaymentRecordController@index');
    // Wokshop :stop
    // In routes/web.php
    Route::get('/AutoCare/tyres/search', [TyresController::class, 'search'])->name('AutoCare.tyres.search');
    Route::post('/AutoCare/workshop/send-invoice-email', [WorkshopController::class, 'sendInvoiceEmail'])->name('workshop.sendInvoiceEmail');
    Route::get('/invoice/preview/{id}', [WorkshopController::class, 'previewInvoicePdf'])->name('invoice.preview');
    Route::get('/invoice/download/{id}', [WorkshopController::class, 'downloadInvoicePdf'])->name('invoice.download');
    // Edit or Add tyre route
    Route::get('/AutoCare/tyres/edit/{product_id?}', [TyresController::class, 'edit'])->name('AutoCare.tyres.edit');
    Route::post('/AutoCare/tyres', [TyresController::class, 'store'])->name('AutoCare.tyres.store'); // For adding new tyres
    Route::put('/AutoCare/tyres/edit/{product_id}', [TyresController::class, 'update'])->name('AutoCare.tyres.update');
    Route::get('/AutoCare/workshop/get-suppliers', [TyresController::class, 'getSuppliers'])->name('AutoCare.workshop.getSuppliers');
    Route::get('/AutoCare/workshop/getOrderType', [TyresController::class, 'getOrderType'])->name('AutoCare.workshop.getOrderType');
    // Delete tyre route
    Route::delete('/AutoCare/tyres/delete/{product_id}', [TyresController::class, 'destroy'])->name('AutoCare.tyres.delete');

    // Resource route for TyresController (optional, if you prefer using resourceful routes)
    Route::resource('tyres', TyresController::class)->except(['create', 'show']);
    Route::prefix('AutoCare/pricing')->name('AutoCare.pricing.')->group(function () {
        Route::get('/manage', [TyrePricingController::class, 'index'])->name('manage'); // Ensure this route is defined correctly
        Route::get('/create', [TyrePricingController::class, 'create'])->name('create');
        Route::post('/store', [TyrePricingController::class, 'store'])->name('store');
        Route::get('/{tyrePricing}/edit', [TyrePricingController::class, 'edit'])->name('edit');
        Route::put('/{tyrePricing}', [TyrePricingController::class, 'update'])->name('update');
        Route::delete('/{tyrePricing}', [TyrePricingController::class, 'destroy'])->name('destroy');
    });
    Route::post('/pricing/updateTyrePrices', [TyrePricingController::class, 'updateTyrePrices'])->name('AutoCare.pricing.updateTyrePrices');
    Route::post('/pricing/sync', [TyrePricingController::class, 'saveTyrePricing'])->name('AutoCare.pricing.sync');

    // Show the form to import tyres
    // Route::get('/tyre/import', [TyreImportController::class, 'showFtpFilePathForm'])->name('tyre.import.form');
    Route::post('/test-ftp-connection', [TyreImportController::class, 'testFtpConnection'])->name('ftp.test.connection');
    // Route::post('/ftp/importftp', [TyreImportController::class, 'importFtp'])->name('ftp.importftp');
    // Route::post('/test-ftp-connection', [TyreImportController::class, 'testFtpConnection'])->name('ftp.test.connection');
    Route::post('tyres/import', [SupplierController::class, 'importTyres'])->name('tyres.import');
      Route::get('/AutoCare/supplier/deleterow', [SupplierController::class, 'deleteRow'])
    ->name('AutoCare.supplier.deleterow');
      Route::post('/AutoCare/supplier/store/{id}', [SupplierController::class, 'store']);
    // tyre product data :stop
    // Route::post('file/importpath', [TyreImportController::class, 'saveFilePath']);
    // Route::get('/AutoCare/supplier/install/{id}', [SupplierController::class, 'install'])->name('supplier.install');
    Route::get('/AutoCare/supplier/uninstall/{id}', [TyreImportController::class, 'uninstall'])->name('supplier.uninstall');
    Route::get('/AutoCare/supplier/install/{id}', [TyreImportController::class, 'install'])->name('supplier.install');


    // Route::post('/bond/place-order', [BondApiController::class, 'placeOrder']);
    //Route::post('/eden/test-order', [EdenApiController::class, 'placeOrder']);

    // product sale:sale
    Route::get('/AutoCare/sale/add', 'SaleProductController@index');
    Route::get('/AutoCare/sale/edit/{id}', 'SaleProductController@edit');
    Route::post('/AutoCare/sale/create', 'SaleProductController@create');
    Route::get('/AutoCare/sale/search', 'SaleProductController@view');
    Route::post('/AutoCare/sale/search', 'SaleProductController@view');
    Route::post('/AutoCare/sale/update', 'SaleProductController@update');
    Route::get('/AutoCare/sale/trash/{id}', 'SaleProductController@trash');
    Route::get('/AutoCare/sale/delete', 'SaleProductController@trashedList');
    Route::get('/AutoCare/sale/delete/{id}', 'SaleProductController@permanemetDelete');
    Route::get('/AutoCare/sale/view/{id}', 'SaleProductController@viewIndivisual');
    Route::get('/AutoCare/sale/sale_return', 'SalesReturnController@show');
    Route::post('/AutoCare/sale/sale_return', 'SalesReturnController@show');
    // Product Sale :stop


    Route::get('/dashboard/bookings', [BookingController::class, 'getBookings'])->name('get.bookings');

    // Route::post('/bookings', [BookingController::class, 'saveBooking'])->name('save.booking');
    // Route::post('/bookings/{id}/create-job', [BookingController::class, 'createJob'])->name('create.job');

    // Start: Supplier Details
    Route::get('/AutoCare/supplier/add', 'SupplierController@save');
    Route::post('/AutoCare/supplier/add', 'SupplierController@save');
    Route::post('/AutoCare/supplier/update', 'SupplierController@save');
    Route::get('/AutoCare/supplier/add/{id}', 'SupplierController@save');
    Route::get('/AutoCare/supplier/search', 'SupplierController@view');
    Route::post('/AutoCare/supplier/search', 'SupplierController@view');
    Route::get('/AutoCare/supplier/trash/{id}', 'SupplierController@trash');
    Route::get('/AutoCare/supplier/delete', 'SupplierController@trashedList');
    Route::get('/AutoCare/supplier/delete/{id}', 'SupplierController@permanemetDelete');
    Route::get('/download-csv/{id}', 'SupplierController@downloadCsv')->name('download.csv');
    // End: Supplier Details
    // Purchase Details

    // Route::get('/get-tyre-products', 'TyresController@getTyreProducts');

    Route::get('/AutoCare/purchase/add', 'PurchaseController@save');
    Route::post('/AutoCare/purchase/add', 'PurchaseController@save');
    Route::post('/AutoCare/purchase/update', 'PurchaseController@update');
    Route::get('/AutoCare/purchase/add/{id}', 'PurchaseController@save');
    Route::get('/AutoCare/purchase/search', 'PurchaseController@view');
    Route::post('/AutoCare/purchase/search', 'PurchaseController@view');
    Route::get('/AutoCare/purchase/trash/{id}', 'PurchaseController@trash');
    Route::get('/AutoCare/purchase/delete', 'PurchaseController@trashedList');
    Route::get('/AutoCare/purchase/delete/{id}', 'PurchaseController@permanemetDelete');
    Route::get('/AutoCare/purchase/purhase_return', 'PurchaseReturnController@show');
    Route::post('/AutoCare/purchase/purhase_return', 'PurchaseReturnController@show');
    //Start: Product Details

    Route::get('/AutoCare/product/add', 'ProductController@save');
    Route::post('/AutoCare/product/add', 'ProductController@save');
    Route::post('/AutoCare/product/update', 'ProductController@save');
    Route::get('/AutoCare/product/add/{id}', 'ProductController@save');
    Route::get('/AutoCare/product/search', 'ProductController@view');
    Route::post('/AutoCare/product/search', 'ProductController@view');
    Route::get('/AutoCare/product/trash/{id}', 'ProductController@trash');
    Route::get('/AutoCare/product/delete', 'ProductController@trashedList');
    Route::get('/AutoCare/product/delete/{id}', 'ProductController@permanemetDelete');
    //End: Product Details

    // Start: Supplier Details
    Route::get('/AutoCare/customer/add', 'CustomerController@save');
    Route::post('/AutoCare/customer/add', 'CustomerController@save');
    Route::post('/AutoCare/customer/update', 'CustomerController@update');
    Route::get('/AutoCare/customer/add/{id}', 'CustomerController@save');
    Route::get('/AutoCare/customer/search', 'CustomerController@view');
    Route::post('/AutoCare/customer/search', 'CustomerController@view');
    Route::get('/AutoCare/customer/trash/{id}', 'CustomerController@trash');
    Route::get('/AutoCare/customer/delete', 'CustomerController@trashedList');
    Route::get('/AutoCare/customer/delete/{id}', 'CustomerController@permanemetDelete');
    Route::get('/AutoCare/customer/details/{id}', 'CustomerController@details')->name('AutoCare.customer.details');
    Route::get('/AutoCare/customer/details/{id}/vehicles', 'CustomerController@vehicles')->name('AutoCare.customer.vehicles');
    Route::get('/AutoCare/customer/details/{id}/orders', 'CustomerController@orders')->name('AutoCare.customer.orders');
    Route::get('/AutoCare/customer/details/{id}/invoices', 'CustomerController@invoices')->name('AutoCare.customer.invoices');
    Route::get('/AutoCare/customer/details/{id}/statements', 'CustomerController@statements')->name('AutoCare.customer.statement');
    Route::get('/AutoCare/customer/details/{id}/vehicles/create', 'CustomerController@createVehicle');
    // End: Supplier Details

    Route::post('/AutoCare/customer/details/{id}/update-profile', 'CustomerController@updateProfile')->name('AutoCare.customer.update-profile');
    Route::post('/AutoCare/customer/details/{id}/update-password', 'CustomerController@updatePassword')->name('AutoCare.customer.update-password');
    Route::post('/AutoCare/customer/details/{id}/update-billing-address', 'CustomerController@updateBillingAddress')->name('AutoCare.customer.update-billing-address');
    Route::post('/AutoCare/customer/details/{id}/update-shipping-address', 'CustomerController@updateShippingAddress')->name('AutoCare.customer.update-shipping-address');


    Route::get('/AutoCare/customer/{id}/vehicles/create', 'CustomerController@createVehicle')->name('AutoCare.customer.vehicles.create');
    Route::get('/AutoCare/customer/{id}/vehicles/{vehicleId}/edit', 'CustomerController@editVehicle')->name('AutoCare.customer.vehicles.edit');
    Route::post('/AutoCare/customer/{id}/vehicles', 'CustomerController@storeVehicle')->name('AutoCare.customer.vehicles.store');
    Route::put('/AutoCare/customer/{id}/vehicles/{vehicleId}', 'CustomerController@updateVehicle')->name('AutoCare.customer.vehicles.update');
    Route::delete('/AutoCare/customer/{id}/vehicles/{vehicleId}', 'CustomerController@deleteVehicle')->name('AutoCare.customer.vehicles.delete');

    Route::get('/customers/search', [WorkshopController::class, 'searchCustomers']);
    // Route::get('/AutoCare/customer/statement', 'CustomerController@getStatement')->name('AutCare.customer.statement');
    Route::get('/AutoCare/customer/statement/pdf', 'CustomerController@downloadStatementPDF')->name('AutoCare.customer.statement.pdf');
    Route::post('/AutoCare/customer/statement/email', 'CustomerController@sendStatementEmail')->name('AutoCare.customer.statement.email');
    Route::get('/customers/{id}/statement/preview', 'CustomerController@previewStatementPdf')->name('customer.statement.preview');
Route::get('/customers/{id}/statement/download', 'CustomerController@downloadStatementPdf')->name('customer.statement.download');
Route::post('/customers/statement/email', 'CustomerController@sendStatementEmail')->name('customer.statement.email');

    // Start: Master Form Details
    Route::post('/master/brands', 'MasterController@brand');
    Route::post('/master/modal', 'MasterController@modal');
    Route::post('/master/service_name', 'MasterController@service');
    Route::post('/master/service_type', 'MasterController@serviceType');
    Route::get('/master/brands', 'MasterController@brand');
    Route::get('/master/modal', 'MasterController@modal');
    Route::get('/master/service_name', 'MasterController@service');
    Route::get('/master/service_type', 'MasterController@serviceType');
    Route::get('/master/brands/{id}', 'MasterController@brandUpdate');
    Route::get('/master/modal/{id}', 'MasterController@modalUpdate');
    Route::get('/master/service_name/{id}', 'MasterController@serviceUpdate');
    Route::get('/master/service_type/{id}', 'MasterController@serviceTypeUpdate');
    Route::get('/master/brands/update/{id}', 'MasterController@brandUpdate');
    Route::get('/master/modal/update/{id}', 'MasterController@modalUpdate');
    Route::get('/master/service_name/update/{id}', 'MasterController@serviceUpdate');
    Route::get('/master/service_type/update/{id}', 'MasterController@serviceTypeUpdate');
    Route::post('/master/brands/update', 'MasterController@brandUpdate');
    Route::post('/master/modal/update', 'MasterController@modalUpdate');
    Route::post('/master/service_name/update', 'MasterController@serviceUpdate');
    Route::post('/master/service_type/update', 'MasterController@serviceTypeUpdate');

    // End: Master Form Details


    // Start:  Marketing Details
    Route::post('/marketing/add', 'MarketingController@save');
    Route::post('/marketing/update', 'MarketingController@update');
    Route::post('/marketing/search', 'MarketingController@view');
    Route::get('/marketing/add', 'MarketingController@save');
    Route::get('/marketing/add/{id}', 'MarketingController@save');
    Route::get('/marketing/search', 'MarketingController@view');
    Route::get('/marketing/delete', 'MarketingController@trashedList');
    Route::get('/marketing/trash/{id}', 'MarketingController@trash');
    Route::get('/marketing/delete/{id}', 'MarketingController@permanemetDelete');
    // End: Marketing Details
    // Start:  Marketing Details
    Route::post('/credit-debit/add', 'CreditDebitController@save');
    Route::post('/credit-debit/update', 'CreditDebitController@update');
    Route::post('/credit-debit/search', 'CreditDebitController@view');
    Route::get('/credit-debit/add', 'CreditDebitController@save');
    Route::get('/credit-debit/add/{id}', 'CreditDebitController@save');
    Route::get('/credit-debit/search', 'CreditDebitController@view');
    Route::get('/credit-debit/delete', 'CreditDebitController@trashedList');
    Route::get('/credit-debit/trash/{id}', 'CreditDebitController@trash');
    Route::get('/credit-debit/delete/{id}', 'CreditDebitController@permanemetDelete');
    // End: Marketing Details


    // Start:  Marketing Details
    Route::post('/SupplierCreditDebitLog/add', 'SupplierCreditDebitLog@save');
    Route::post('/SupplierCreditDebitLog/update', 'SupplierCreditDebitLog@update');
    Route::post('/SupplierCreditDebitLog/search', 'SupplierCreditDebitLog@view');
    Route::get('/SupplierCreditDebitLog/add', 'SupplierCreditDebitLog@save');
    Route::get('/SupplierCreditDebitLog/add/{id}', 'SupplierCreditDebitLog@save');
    Route::get('/SupplierCreditDebitLog/search', 'SupplierCreditDebitLog@view');
    Route::get('/SupplierCreditDebitLog/delete', 'SupplierCreditDebitLog@trashedList');
    Route::get('/SupplierCreditDebitLog/trash/{id}', 'SupplierCreditDebitLog@trash');
    Route::get('/cSupplierCreditDebitLog/delete/{id}', 'SupplierCreditDebitLog@permanemetDelete');
    // End: Marketing Details

    // Start:  CustomerCreditDebitLog Details
    Route::post('/CustomerCreditDebitLog/add', 'CustomerCreditDebitLog@save');
    Route::post('/CustomerCreditDebitLog/update', 'CustomerCreditDebitLog@update');
    Route::post('/CustomerCreditDebitLog/search', 'CustomerCreditDebitLog@view');
    Route::get('/CustomerCreditDebitLog/add', 'CustomerCreditDebitLog@save');
    Route::get('/CustomerCreditDebitLog/add/{id}', 'CustomerCreditDebitLog@save');
    Route::get('/CustomerCreditDebitLog/search', 'CustomerCreditDebitLog@view');
    Route::get('/CustomerCreditDebitLog/delete', 'CustomerCreditDebitLog@trashedList');
    Route::get('/CustomerCreditDebitLog/trash/{id}', 'CustomerCreditDebitLog@trash');
    Route::get('/CustomerCreditDebitLog/delete/{id}', 'CustomerCreditDebitLog@permanemetDelete');
    // End: Marketing Details

    // Start: Ajax Related
    Route::post('/ajax/getPurchase', 'AjaxController@getPurchase');
    Route::post('/ajax/getCustomerForWorkshop', 'AjaxController@getCustomerForWorkshop');
    Route::post('/ajax/getProductForworkshop', 'AjaxController@getProductForworkshop');
    Route::post('/ajax/getService', 'AjaxController@getService');
    Route::post('/ajax/getProduct', 'AjaxController@getProduct');
    // Route::post('/ajax/TyresProduct', 'AjaxController@TyresProduct');
    Route::post('/ajax/mark-as-read', 'AjaxController@markAsRead')->name('notifications.markAsRead');
    Route::post('/ajax/mark-as-read/{id}', 'AjaxController@markAsReadSingle')->name('notifications.markAsRead.single');

    Route::post('/ajax/getModal', 'AjaxController@getModal');
    Route::post('/ajax/getServiceThroughServiceId', 'AjaxController@getServiceThroughServiceId');
    Route::post('/ajax/getServiceTypeForWorkshop', 'AjaxController@getServiceTypeForWorkshop');
    Route::post('/ajax/getServiceTypeForWorkshopThroughModel', 'AjaxController@getServiceTypeForWorkshopThroughModel');
    Route::post('/ajax/getProductThroughModelAndBrand', 'AjaxController@getProductThroughModelAndBrand');
    Route::post('/ajax/submitSupplierDetail', 'AjaxController@submitSupplierDetail');
    Route::post('/ajax/submitCustomerPaymentDetail', 'AjaxController@submitCustomerPaymentDetail');
    Route::post('/ajax/GetVehicleDetailFromWorkshop', 'AjaxController@GetVehicleDetailFromWorkshop');
    Route::post('/ajax/GetVehicleRegFromWorkshop', 'AjaxController@GetVehicleRegFromWorkshop');
    Route::post('/ajax/submitPurchaseReturn', 'AjaxController@submitPurchaseReturn');
    Route::post('/ajax/submitSaleReturn', 'AjaxController@submitSaleReturn');
    Route::post('/ajax/getWorkshopReport', 'AjaxController@getWorkshopReport');
    Route::post('/ajax/paymentForWorkshop', 'AjaxController@paymentForWorkshop');
    Route::post('/ajax/updateWorkshopBalance', 'AjaxController@updateWorkshopBalance');
    Route::post('/ajax/discountForWorkshop', 'AjaxController@discountForWorkshop');
    Route::get('/AutoCare/workshop/search/fetch-discount/{id}', 'AjaxController@fetchDiscount');
    Route::get('/AutoCare/workshop/search/get-payment-logs/{id}', 'AjaxController@getPaymentLogs');
    Route::get('/AutoCare/workshop/search/get-payment-log/{id}', 'AjaxController@getPaymentLog');
    Route::post('/AutoCare/workshop/search/update-payment-log/{id}', 'AjaxController@updatePaymentLog');
    Route::delete('/AutoCare/workshop/search/delete-payment-log/{id}', 'AjaxController@deletePaymentLog');
    Route::post('/AutoCare/workshop/createCustomer', 'AjaxController@createCustomerForWorkshop');
    // routes/web.php or routes/api.php
    Route::get('/get-customers-by-vehicle', 'AjaxController@getCustomersByVehicle')->name('get.customers.by.vehicle');
    Route::get('/get-stock-history', 'AjaxController@getStockHistory')->name('tyre.getStockHistory');
    // End: Ajax Related

    /**
     * Start: Employee Module
     */


    Route::get('/employee', 'MasterformsController@addUser')->name('employee');
    Route::post('/employee-save', 'MasterformsController@addUser')->name('employee-save');
    Route::get('/employee-list', 'MasterformsController@userList')->name('employee-list');
    Route::get('/employee-edit/{id}', 'MasterformsController@addUser')->name('employee-edit');
    Route::get('/employee/block/{type}/{id}', 'MasterformsController@blockUser')->name('employee-block-edit');
    Route::get('/employee/trash/{type}/{id}', 'MasterformsController@trashUser')->name('employee-trash-edit');
    Route::get('/employee/{id}/{view}', 'MasterformsController@addUser')->name('employee-view-edit');

    // Route::get('/get-payment-overview/{sid}', 'StudentController@paymentOverview')->name('get-payment-overview');
    // Route::get('/get-payment-overview-by-year-id/{sid}/{year}', 'StudentController@paymentOverview')->name('get-payment-overview');
    /**
     * End: Employee Module
     */


    Route::get('/send', 'SendEmailController@send');
    Route::view('/sample/cards', 'samples.cards');
    Route::view('/sample/forms', 'samples.forms');

    Route::view('/sample/modals', 'samples.modals');
    Route::view('/sample/buttons', 'samples.buttons');
    Route::view('/sample/switches', 'samples.switches');
    Route::view('/sample/tables', 'samples.tables');
    Route::view('/sample/tabs', 'samples.tabs');
    Route::view('/sample/icons-font-awesome', 'samples.font-awesome-icons');
    Route::view('/sample/icons-simple-line', 'samples.simple-line-icons');
    Route::view('/sample/widgets', 'samples.widgets');
    Route::view('/sample/charts', 'samples.charts');

});

// Route::get('/home', 'HomeController@index')->name('home');
Route::get('brand/{slug}', [App\Http\Controllers\ViewController\BrandController::class, 'show'])->name('brands.show');
Route::get('brand', [App\Http\Controllers\ViewController\BrandController::class, 'index'])->name('brands.index');
//Blog
//Route::get('/blogs/tag/{tag}', [BlogController::class, 'tag'])->name('blogs.tag');
Route::get('blogs', [App\Http\Controllers\ViewController\BlogController::class, 'index'])->name('blogs.index');
Route::get('blogs/{slug}', [App\Http\Controllers\ViewController\BlogController::class, 'show'])->name('blogs.show');
Route::get('/blogs/category/{slug}', [App\Http\Controllers\ViewController\BlogController::class, 'category'])->name('blogs.category');

Route::post('/contact-submit', [ServiceViewController::class, 'submitContactForm'])->name('contact.submit');
Route::get('/contact-submit', function () {
    return redirect('/contact');
});

Route::post('/otp/send', [OtpVerificationController::class, 'send'])->middleware('throttle.otp')->name('otp.send');





// End: Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Route::get('/vehicle-data', [VrmController::class, 'fetchVehicleData'])->name('vehicle-data');
// Route::get('{slug}', [App\Http\Controllers\ViewController\SlugController::class, 'handleSlug'])->name('slug.handle');
Route::get('{slug}', [App\Http\Controllers\ViewController\SlugController::class, 'handleSlug'])
    ->where('slug', '^(?!login|register|dashboard|Home|password|getTyreProducts|logout|AutoCare|vehicle-data|admin).*') // Exclude login-related routes
    ->name('slug.handle');