<?php


// app/Http/Controllers/GeneralSettingsController.php
namespace App\Http\Controllers;
use DB;
use Mews\Purifier\Facades\Purifier;

use App\Models\GeneralSettings;
use Illuminate\Http\Request;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $generalSettings = GeneralSettings::orderBy('status', 'asc')->get();
        return view('AutoCare.general-settings.index', compact('generalSettings'));
    }

    public function updateBooking(Request $request)
    {
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
    }

     public function updateSmtpDetails(Request $request)
    {
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
    }

}