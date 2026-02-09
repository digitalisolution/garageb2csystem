<?php
// app/Http/Controllers/GeneralSettingsController.php
namespace App\Http\Controllers;
use DB;
use App\Models\GeneralSettings;
use App\Models\OrderTypes;
use Illuminate\Http\Request;

class GeneralSettingsController extends Controller
{

    public function index()
    {
        $generalSettings = GeneralSettings::orderBy('status', 'asc')->get();
        // Fetch only active order types (status = 1)
        $orderTypes = OrderTypes::all();
        return view('AutoCare.general-settings.index', compact('generalSettings','orderTypes'));
    }

    public function updateBooking(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'calendar_booking_color_pending' => 'nullable|string',
                'calendar_booking_color_booked' => 'nullable|string',
                'calendar_booking_color_canceled' => 'nullable|string',
                'calendar_booking_color_failed' => 'nullable|string',
                'calendar_booking_color_awaiting' => 'nullable|string',
                'calendar_booking_color_completed' => 'nullable|string',
                'calendar_booking_color_default' => 'nullable|string',
                'calendar_admin_booking_active' => 'nullable|string',
            ]);

            // Save settings
            foreach ($validated as $key => $value) {
                // Update only the 'value' field for the row with the matching 'name'
                DB::table('general_settings')
                    ->where('name', $key) // Find the row by 'name'
                    ->update(['value' => $value]); // Update only the 'value' field
            }

            return redirect()->back()->with('success', 'Calendar settings updated successfully.');
        } catch (\Throwable $e) {
            \Log::error("Error updating Calender: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateSmtpDetails(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'smtp_host' => 'nullable|string',
                'smtp_port' => 'nullable|string',
                'smtp_encrpt' => 'nullable|string',
                'smtp_username' => 'nullable|string',
                'smtp_email' => 'nullable|string',
                'smtp_password' => 'nullable|string',
                'smtp_from_name' => 'nullable|string',
            ]);

            // Save settings
            foreach ($validated as $key => $value) {
                DB::table('general_settings')
                    ->where('name', $key)
                    ->update(['value' => $value]);
            }

            return redirect()->back()->with('success', 'SMTP Details updated successfully.');

        } catch (\Throwable $e) {
            \Log::error("Error updating SMTP Details: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateTyreService(Request $request)
    {
        try {
            $fields = [
                'module_tyre_status',
                'module_tyre_order_type',
                'module_tyre_search_type',

                // Tyre payment modes
                'module_tyre_payment_fully_fitted',
                'module_tyre_payment_mobile_fitted',
                'module_tyre_payment_mail',

                // Full service
                'module_fullservice_status',
                'module_fullservice_search_type',
                'module_fullservice_payment_mode',
            ];

            foreach ($fields as $field) {

                $value = $request->input($field);

                if (is_array($value)) {
                    $value = json_encode($value); // convert array to JSON
                }

                DB::table('general_settings')
                    ->where('name', $field)
                    ->update([
                        'value' => $value ?? '',
                        'updated_at' => now()
                    ]);
            }

            // First set ALL order types to 0 (default)
            OrderTypes::query()->update(['status' => 0]);

            // Get checked order types from form
            $selectedOrderTypes = $request->input('module_tyre_order_type', []);
            // Status = 1 group
            $statusOneGroup = [
                'fully_fitted',
                'mailorder',
                'mobile_fitted',
            ];

            // Status = 2 group
            $statusTwoGroup = [
                'delivery',
                'emergency',
                'trade_customer_price',
            ];

            // Update status = 1 for selected items
            OrderTypes::whereIn('ordertype_name', $statusOneGroup)
                ->whereIn('ordertype_name', $selectedOrderTypes)
                ->update(['status' => 1]);

            // Update status = 2 for selected items
            OrderTypes::whereIn('ordertype_name', $statusTwoGroup)
                ->whereIn('ordertype_name', $selectedOrderTypes)
                ->update(['status' => 2]);

            return back()->with('success', 'Tyre & Full Service settings updated.');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updatePayment(Request $request)
    {
        try {
            $fields = $request->except('_token', '_method');

            foreach ($fields as $key => $value) {

                // Decide if this field is a status field
                $isStatusField = str_ends_with($key, '_active')
                    || str_ends_with($key, '_display_on_website')
                    || str_ends_with($key, '_default_selected')
                    || str_ends_with($key, '_initialized')
                    || str_ends_with($key, '_test')
                    || str_ends_with($key, '_test_mode_enabled');

                DB::table('general_settings')->updateOrInsert(
                    ['name' => $key],
                    [
                        'value'      => $isStatusField ? null : $value,
                        'status'     => $isStatusField ? $value : 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            return back()->with('success', 'Payment settings updated successfully.');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function updateCommonModule(Request $request)
    {
        // --------------------------
        // SUPPORT CALL UPDATE
        // --------------------------
        DB::table('general_settings')
            ->updateOrInsert(
                ['name' => 'support_call'],
                [
                    'value' => $request->support_call ?? null,
                    'status' => $request->support_call_status ?? 0,
                    'updated_at' => now()
                ]
            );

        // --------------------------
        // WEBSITE ADMIN FREEZE UPDATE
        // --------------------------
        DB::table('general_settings')
            ->updateOrInsert(
                ['name' => 'website_admin_freeze'],
                [
                    'value' => $request->website_admin_freeze ?? 0,   // 0 or 1
                    'status' => 1,                                     // always active row
                    'updated_at' => now()
                ]
            );

        // --------------------------
        // Ownstock Inventry
        // --------------------------
        DB::table('general_settings')
            ->updateOrInsert(
                ['name' => 'add_ownstock_inventry'],
                [
                    'value' => $request->add_ownstock_inventry ?? 0,   // 0 or 1
                    'status' => 1,                                     // always active row
                    'updated_at' => now()
                ]
            );

        return back()->with('success', 'Settings updated successfully.');
    }



}