<?php

namespace App\Http\Controllers;

use App\Models\CalendarSetting;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Supplier;
use App\Models\CarService;
use App\Models\DeliveryTime;
use Carbon\Carbon;


class CalendarSettingController extends Controller
{
    // Display all calendar settings
    public function index()
    {
        $settings = CalendarSetting::all();
        return view('AutoCare.calendar.index', compact('settings'));
    }

    // Show the form to create a new calendar setting
    public function create()
    {
        return view('AutoCare.calendar.create');
    }

    // Store a new calendar setting
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'calendar_name' => 'required|string|max:255',
            'open_close_hours' => 'nullable|array',
            'holidays' => 'nullable|array',
            'block_date_time' => 'nullable|array',
            'block_fitting_type_days' => 'nullable|array',
            'block_service_perdays' => 'nullable|array',
            'block_service_perhours' => 'nullable|array',
        ]);

        // Create the CalendarSetting record
        CalendarSetting::create($request->only([
            'calendar_name',
            'open_close_hours',
            'holidays',
            'block_date_time',
            'block_fitting_type_days',
            'block_service_perdays',
            'block_service_perhours',

        ]));

        return redirect()->route('AutoCare.calendar.index')->with('success', 'Calendar setting created successfully');
    }

    // Show the form to edit an existing calendar setting
    public function edit($id)
    {
        $calendarSetting = CalendarSetting::findOrFail($id);
         $services = CarService::where('status', 1)->get(); 
        return view('AutoCare.calendar.create', compact('calendarSetting','services'));
    }

    // Update an existing calendar setting
   public function update(Request $request, $id)
{
    $request->validate([
        'calendar_name' => 'required|string|max:255',
        'slot_perday_booking' => 'nullable|numeric|max:50',
        'open_close_hours' => 'nullable|array',
        'holidays' => 'nullable|array',
        'block_date_time' => 'nullable|array',
        'block_fitting_type_days' => 'nullable|array',
        'block_service_perdays' => 'nullable|array',
        'block_service_perhours' => 'nullable|array',
    ]);

    $calendarSetting = CalendarSetting::findOrFail($id);

    // Ensure missing fields are explicitly set to null
    $fields = [
        'calendar_name',
        'open_close_hours',
        'slot_perday_booking',
        'holidays',
        'duration',
        'block_date_time',
        'block_fitting_type_days',
        'block_service_perdays',
        'block_service_perhours',
    ];

    $data = [];

    foreach ($fields as $field) {
        $data[$field] = $request->has($field) ? $request->input($field) : null;
    }

    $calendarSetting->update($data);

    return redirect()->route('calendar.edit', $id)->with('success', 'Calendar setting updated successfully');
}

    
    // Delete a calendar setting
    public function destroy($id)
    {
        $calendarSetting = CalendarSetting::findOrFail($id);
        $calendarSetting->delete();

        return redirect()->route('AutoCare.calendar.index')->with('success', 'Calendar setting deleted successfully');
    }

    public function getCalendarSettings()
    {
        $calendarSettings = CalendarSetting::where('default', 1)->first();

        if (!$calendarSettings) {
            return response()->json(['error' => 'No calendar settings found'], 404);
        }

        // Use the already-casted array instead of decoding it
        $openCloseHours = $calendarSettings->open_close_hours;

        // Unserialize other attributes
        $blockedTimes = $calendarSettings->block_date_time;
        $blockFittingTypeDays = $calendarSettings->block_fitting_type_days;
        $holidays = $calendarSettings->holidays;
        $duration = $calendarSettings->duration;
        $slotPerdayBooking = $calendarSettings->slot_perday_booking;
        // Convert open/close hours to FullCalendar format
        $businessHours = [];
        $daysMapping = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];

        foreach ($openCloseHours as $day => $times) {
            // Check if the day is closed
            $isClosed = isset($times['closed']) && $times['closed'] === 'on';

            // Only add business hours for open days
            if (!$isClosed && isset($times['opening']) && isset($times['closing'])) {
                $businessHours[] = [
                    'daysOfWeek' => [$daysMapping[$day]],
                    'startTime' => $times['opening'],
                    'endTime' => $times['closing'],
                ];
            }
        }
        // dd($businessHours);
        return response()->json([
            'businessHours' => $businessHours,
            'blockedTimes' => $blockedTimes,
            'blockFittingTypeDays' => $blockFittingTypeDays,
            'holidays' => $holidays,
            'slotPerdayBooking' => $slotPerdayBooking,
            'duration' => $duration,
        ]);
    }

    public function getWebsiteCalendarSettings()
    {
        $calendarSettings = CalendarSetting::where('default', 1)->first();

        if (!$calendarSettings) {
            return response()->json(['error' => 'No calendar settings found'], 404);
        }

        // Use the already-casted array instead of decoding it
        $openCloseHours = $calendarSettings->open_close_hours;

        // Unserialize other attributes
        $blockedTimes = $calendarSettings->block_date_time;
        $block = $calendarSettings->block_fitting_type_days;
        $blockServicePerhours = $calendarSettings->block_service_perhours;
        $holidays = $calendarSettings->holidays;
        $duration = $calendarSettings->duration;
        $slotPerdayBooking = $calendarSettings->slot_perday_booking;
        $currentFittingTypes = $this->getCurrentFittingTypesFromCart();
        $currentServiceTypes = $this->getblockServicePerhours();
        $filteredBlockServicePerhours = [];
        $filteredBlockFittingTypeDays = [];
            if (isset($block['jobtype']) && is_array($block['jobtype'])) {
                foreach ($block['jobtype'] as $index => $jobtype) {
                    if (in_array($jobtype, $currentFittingTypes)) {
                        $filteredBlockFittingTypeDays[] = [
                            'days' => $block['days'][$index] ?? null,
                            'from' => $block['from'][$index] ?? null,
                            'to' => $block['to'][$index] ?? null,
                            'jobtype' => $jobtype,
                            'block_title' => $block['block_title'][$index] ?? null,
                        ];
                    }
                }
            }

            // Extract just the service_type values from $currentServiceTypes
            $currentServiceTypeIds = [];
            foreach ($currentServiceTypes as $service) {
                if (isset($service['service_type'])) {
                    $currentServiceTypeIds[] = $service['service_type'];
                }
            }

            // Iterate over the blockServicePerhours array
            if (isset($blockServicePerhours['service_type']) && is_array($blockServicePerhours['service_type'])) {
                foreach ($blockServicePerhours['service_type'] as $index => $block) {
                    // Check if the service_type exists in the block and matches any current service type
                    if (in_array($block, $currentServiceTypeIds)) {
                        $filteredBlockServicePerhours[] = [
                            'days' => $blockServicePerhours['days'][$index] ?? null,
                            'from' => $blockServicePerhours['from'][$index] ?? null,
                            'to' => $blockServicePerhours['to'][$index] ?? null,
                            'service_type' => $blockServicePerhours['service_type'][$index] ?? null,
                            'block_title' => $blockServicePerhours['block_title'][$index] ?? null,
                        ];
                    }
                }
            }

            // Convert open/close hours to FullCalendar format
            $businessHours = [];
            $daysMapping = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
            $validRangeStart = $this->getValidRangeStart();
            foreach ($openCloseHours as $day => $times) {
                // Check if the day is closed
                $isClosed = isset($times['closed']) && $times['closed'] === 'on';

                // Only add business hours for open days
                if (!$isClosed && isset($times['opening']) && isset($times['closing'])) {
                    $businessHours[] = [
                        'daysOfWeek' => [$daysMapping[$day]],
                        'startTime' => $times['opening'],
                        'endTime' => $times['closing'],
                        'validRangeStart' => $validRangeStart,
                        'validRangeEnd' => date('Y-m-d', strtotime('+3 month')). 'T20:00:00',
                    ];
                }
            }
        // dd($filteredBlockServicePerhours);
            
             $fullyBookedSlots = $this->getFullyBookedSlots($slotPerdayBooking);

            return response()->json([
                'businessHours' => $businessHours,
                'blockedTimes' => $blockedTimes,
                'blockFittingTypeDays' => $filteredBlockFittingTypeDays,
                'blockServicePerhours' => $filteredBlockServicePerhours,
                'holidays' => $holidays,
                'fullyBookedSlots' => $fullyBookedSlots,
                'duration' => $duration,
            ]);
    }

        private function getValidRangeStart()
    {
        $current_time = strtotime(date('H:i'));
        $current_day = strtolower(date('l'));
        $current_date = date('Y-m-d');
        $hasTyre = false;
        $hasService = false;
        $maxTime = [];
        $productInfo = [];

        // Retrieve cart data from session
        $cartItems = session('cart', []);
       
        // Check if the cart has products
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                // dd($item);
                $product_id = $item['id'];
                if ($item['type'] == 'tyre') {
                    $hasTyre = true;
                    $supplier_id = $this->getSupplierId($item['supplier']);
                    if (!empty($item['supplier'])) {
                        $productInfo[$supplier_id]['supplier'] = $item['supplier'];
                        $productInfo[$supplier_id]['fitting_type'] = $item['fitting_type'];
                    }
                }
            }
        }

        // Handle tyre delivery time
        if ($hasTyre) {
            if (!empty($productInfo)) {
                foreach ($productInfo as $key => $value) {
                    $supplier_id = $key;
                    $fitting_type = $value['fitting_type'];
                    if ($supplier_id) {
                        $deliveryTime = $this->getDeliveryTime($supplier_id, $fitting_type);
                        foreach ($deliveryTime as $data) {
                            $week_day = strtolower($data['day']);
                            $delivery_time = strtolower($data['delivery_time']);
                            $start_hours = strtotime($data['start_hours'] . ':' . $data['start_minutes']);
                            $end_hours = strtotime($data['end_hours'] . ':' . $data['end_minutes']);

                            if ($current_day == $week_day) {
                                if (($current_time > $start_hours) && ($current_time < $end_hours)) {
                                    $end_minutes = ($data['end_minutes'] < 10) ? '0' . $data['end_minutes'] : $data['end_minutes'];
                                    $data_end_hours = $current_date . ' ' . $data['end_hours'] . ':' . $end_minutes . ':00';
                                    $start_date = date('Y-m-d H:i:s', strtotime($data_end_hours . '+' . $delivery_time . ' hours'));
                                    $maxTime[$delivery_time] = date('Y-m-d', strtotime($start_date)) . 'T' . date('H:i:s', strtotime($start_date));
                                }
                            }
                        }
                    }
                }
            }
        }

        // Determine the maximum valid time
        if (!empty($maxTime)) {
            $key = max(array_keys($maxTime));
            return $maxTime[$key];
        } else {
            $date = strtotime('+ 0 hours');
            return date('Y-m-d', $date) . 'T' . date('H:i:s', $date);
        }
    }

    private function getCurrentFittingTypesFromCart()
    {
        $cartItems = session('cart', []);
        $fittingTypes = [];

        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if (isset($item['fitting_type'])) {
                    $fittingTypes[] = $item['fitting_type'];
                }
            }
        }

        return array_unique($fittingTypes);
    }

    private function getblockServicePerhours()
{
    // Fetch cart items from the session
    $cartItems = session('cart', []);
    $cartServices = [];

    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            if (isset($item['type']) && $item['type'] === 'service') {
                if (isset($item['fitting_type'])) {
                    $cartServices[] = [
                        'service_type' => $item['id'],
                        'model' => $item['model'],
                    ];
                }
            }
        }
    }

    return $cartServices;
    }

    private function getSupplierId($tyreSource)
    {
        // Fetch the supplier ID by name where status is 1
        $supplier = Supplier::where('supplier_name', $tyreSource)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        // Return the supplier ID if found, otherwise return null
        return $supplier->id ?? $supplier;
    }


    private function getDeliveryTime($supplierId, $fitting_type)
    {
        // Fetch delivery time data for the given supplier ID
        $deliveryTimes = DeliveryTime::where('supplier', $supplierId)->where('delivery_type',  $fitting_type)->get();
        // Format the delivery times into an array
        $formattedDeliveryTimes = [];
        foreach ($deliveryTimes as $deliveryTime) {
            $formattedDeliveryTimes[] = [
                'day' => $deliveryTime->day,
                'start_hours' => $deliveryTime->start_hours,
                'start_minutes' => $deliveryTime->start_minutes,
                'end_hours' => $deliveryTime->end_hours,
                'end_minutes' => $deliveryTime->end_minutes,
                'delivery_time' => $deliveryTime->delivery_time,
                'delivery_type' => $deliveryTime->delivery_type,
            ];
        }

        return $formattedDeliveryTimes;
    }

    private function getFullyBookedSlots($slotPerBooking)
    {
        $bookings = Booking::selectRaw(
            'DATE(bookings.start) as date, 
            TIME(bookings.start) as time_slot_start,
            TIME(bookings.end) as time_slot_end,
            COUNT(*) as booking_count'
        )
        ->join('workshops', 'bookings.workshop_id', '=', 'workshops.id')
        ->where('workshops.workshop_origin', 'website')
        ->whereDate('bookings.start', '>=', now()->toDateString())
        ->groupBy('date', 'time_slot_start', 'time_slot_end')
        ->get();


        $fullyBookedSlots = [];

        foreach ($bookings as $booking) {
            if ($booking->booking_count >= $slotPerBooking) {
                $fullyBookedSlots[] = [
                    'date' => $booking->date,
                    'start' => $booking->time_slot_start,
                    'end' => $booking->time_slot_end,
                ];
            }
        }

        return $fullyBookedSlots;
    }

    public function getEvents()
    {
        // Fetch the admin booking active setting
        $calendarAdminBookingActive = get_option('calendar_admin_booking_active');
        // Fetch bookings from the database
        $query = Booking::select(
            'bookings.id',
            'bookings.workshop_id',
            'bookings.title',
            'bookings.start',
            'bookings.end',
            'workshops.status as status',
            'workshops.name as workshop_name',
            'workshops.address as workshop_address',
            'workshops.vehicle_reg_number as vrm',
            'workshops.payment_method as payment_method',
        )
            ->leftJoin('workshops', 'bookings.workshop_id', '=', 'workshops.id');
    
        // Add condition to filter bookings based on calendar_admin_booking_active
        if ($calendarAdminBookingActive == 0) {
            $query->where('workshops.workshop_origin', 'Website');
        }
    
        // Execute the query
        $bookings = $query->get();
    
        // Format the bookings for FullCalendar
        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'workshop_id' => $booking->workshop_id,
                'title' => $booking->title . ' (' . strtoupper($booking->vrm) . ')',
                'start' => Carbon::parse($booking->start, 'Europe/London')->toIso8601String(),
                'end' => Carbon::parse($booking->end, 'Europe/London')->toIso8601String(),
                'backgroundColor' => $this->getEventColor($booking->status),
                'borderColor' => $this->getEventColor($booking->status),
                'textColor' => '#fff', // optional but good for readability
                'display' => 'block',
                'workshop_details' => [
                    'name' => $booking->workshop_name,
                    'address' => $booking->workshop_address,
                    'payment_method' => $booking->payment_method,
                    'vrm' => $booking->vrm,
                ],
            ];
        });
    
        return response()->json($formattedBookings);
    }


    // Helper method to determine event color based on status
    private function getEventColor($status)
    {
        // Define default color if no mapping is found
        $defaultColor = get_option('calendar_booking_color_default', '#0000ff');
    
        // Fetch the color for the given status
        switch ($status) {
            case 'completed':
                return get_option('calendar_booking_color_completed', '#00ff00'); // Default green for confirmed
            case 'pending':
                return get_option('calendar_booking_color_pending', '#ffcc00'); // Default yellow for pending
            case 'booked':
                return get_option('calendar_booking_color_booked', '#2ed943'); // Default light green for booked
            case 'canceled':
                return get_option('calendar_booking_color_canceled', '#2ed943'); // Default light green for booked
            case 'failed':
                return get_option('calendar_booking_color_failed', '#2ed943'); // Default light green for booked
            case 'awaiting':
                return get_option('calendar_booking_color_awaiting', '#2ed943'); // Default light green for booked
            default:
                return $defaultColor; // Default color for other statuses
        }
    }


}