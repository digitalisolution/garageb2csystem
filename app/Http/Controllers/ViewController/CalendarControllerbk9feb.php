<?php
namespace App\Http\Controllers\ViewController;

use App\Models\Booking;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Booking::all();
        $bookingDetails = session('booking_details', []);

        return view('calendar', ['events' => json_encode($events), 'bookingDetails' => $bookingDetails]);
    }

    public function saveSelectedSlot(Request $request)
    {
        // Validate the request
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // Convert the UTC time to the local timezone
        $startLocal = \Carbon\Carbon::parse($request->input('start'))->setTimezone(config('app.timezone'));
        $endLocal = \Carbon\Carbon::parse($request->input('end'))->setTimezone(config('app.timezone'));

        // Save the selected slot in the session
        session([
            'bookingDetails' => [
                'start' => $startLocal->toDateTimeString(),
                'end' => $endLocal->toDateTimeString(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Slot saved successfully.',
        ]);
    }

}
