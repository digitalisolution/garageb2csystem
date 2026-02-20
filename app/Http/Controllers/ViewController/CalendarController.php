<?php
namespace App\Http\Controllers\ViewController;

use App\Models\Booking;
use App\Models\CalendarSetting;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Booking::all();

        $garageId = Session::get('selected_garage_id');
        if (!$garageId) {
            return redirect()->route('grages')
                ->with('error', 'Please select a garage first.');
        }

        $garage = Garage::findOrFail($garageId);

        $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();

        if (!$calendarSettings) {
            $calendarSettings = CalendarSetting::where('default', 1)->first();
        }

        $bookingDetails = session('booking_details', []);

        return view('calendar', [
            'events' => json_encode($events),
            'bookingDetails' => $bookingDetails,
            'calendarSettings' => $calendarSettings,
            'garage' => $garage
        ]);
    }
    private function getDayPeriodsFromSettings($date)
    {
        $garageId = Session::get('selected_garage_id');
        if (!$garageId) {
            return redirect()->route('grages')
                ->with('error', 'Please select a garage first.');
        }

        $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();

        $openCloseHours = $calendarSettings->open_close_hours ?? [];

        $dayKey = strtolower(Carbon::parse($date)->format('l'));

        if (!isset($openCloseHours[$dayKey]) || isset($openCloseHours[$dayKey]['closed'])) {
            return null;
        }

        $opening = $openCloseHours[$dayKey]['opening'];
        $closing = $openCloseHours[$dayKey]['closing'];
        $breakPoint = $calendarSettings->am_pm_break_point ?? '12:00';

        $date = Carbon::parse($date);

        $openTime = Carbon::parse($date->format('Y-m-d') . ' ' . $opening);
        $breakTime = Carbon::parse($date->format('Y-m-d') . ' ' . $breakPoint);
        $closeTime = Carbon::parse($date->format('Y-m-d') . ' ' . $closing);

        return [
            'AM' => [
                'start' => $openTime,
                'end' => $breakTime,
            ],
            'PM' => [
                'start' => $breakTime,
                'end' => $closeTime,
            ]
        ];
    }

    public function saveSelectedSlot(Request $request)
    {
        if ($request->has('start') && $request->has('end')) {

            session([
                'bookingDetails' => [
                    'start' => $request->start,
                    'end' => $request->end,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slot saved successfully (time slot).',
            ]);
        }

        $request->validate([
            'date' => 'required|date',
            'period' => 'required|in:AM,PM',
        ]);

        $periods = $this->getDayPeriodsFromSettings($request->date);

        if (!$periods || !isset($periods[$request->period])) {
            return response()->json([
                'success' => false,
                'message' => 'Selected day is closed.',
            ], 422);
        }

        $start = $periods[$request->period]['start'];
        $end = $periods[$request->period]['end'];

        session([
            'bookingDetails' => [
                'start' => $start->format('Y-m-d H:i:s'),
                'end' => $end->format('Y-m-d H:i:s'),
                'period' => $request->period,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Slot saved successfully (dynamic AM/PM).',
        ]);
    }


}
