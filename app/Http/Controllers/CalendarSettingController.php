<?php

namespace App\Http\Controllers;

use App\Models\CalendarSetting;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Garage;
use App\Models\Supplier;
use App\Models\CarService;
use App\Traits\HasPermissionCheck;
use Illuminate\Support\Facades\Session;
use App\Models\Ramps;
use App\Models\DeliveryTime;
use App\Models\HeaderLink;
use Carbon\Carbon;

class CalendarSettingController extends Controller
{

    use HasPermissionCheck;
    public function index()
    {
        $this->authorizePermission('calendar.view');
        $viewData['settings'] = CalendarSetting::with('garage')->get();
        $viewData['header_link'] = HeaderLink::where("menu_id", '17')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        return view('AutoCare.calendar.index', $viewData);
    }

    public function create()
    {
        $this->authorizePermission('calendar.create');
        $viewData['header_link'] = HeaderLink::where("menu_id", '17')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['ramps'] = Ramps::where('status', 1)->get();
        $viewData['services'] = CarService::where('status', 1)->get();
        $viewData['garage_data'] = Garage::all();

        return view('AutoCare.calendar.create', $viewData);
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'calendar_name' => 'required|string|max:255',
            'calendar_type' => 'required|string|max:255',
            'garage_id' => 'required|exists:garages,id',
            'am_pm_break_point' => 'required|string|max:50',
            'open_close_hours' => 'nullable|array',
            'holidays' => 'nullable|array',
            'block_date_time' => 'nullable|array',
            'block_specific_datetime' => 'nullable|array',
            'ramps_block_day_time' => 'nullable|array',
            'block_fitting_type_days' => 'nullable|array',
            'block_service_perdays' => 'nullable|array',
            'block_service_perhours' => 'nullable|array',
            'block_fitting_type_datetime' => 'nullable|array',
            'block_service_datetime' => 'nullable|array',
        ]);

        // Create the CalendarSetting record
        CalendarSetting::create($request->only([
            'calendar_name',
            'calendar_type',
            'garage_id',
            'open_close_hours',
            'am_pm_break_point',
            'holidays',
            'block_date_time',
            'block_specific_datetime',
            'ramps_block_day_time',
            'block_fitting_type_days',
            'block_service_perdays',
            'block_service_perhours',
            'block_fitting_type_datetime',
            'block_service_datetime',

        ]));

        return redirect()->route('calendar.index')->with('success', 'Calendar setting created successfully');
    }

    public function edit($id)
    {
        $this->authorizePermission('calendar.edit');
        $viewData['calendarSetting'] = CalendarSetting::findOrFail($id);
        $viewData['services'] = CarService::where('status', 1)->get();
        $viewData['ramps'] = Ramps::where('status', 1)->get();
        $viewData['garage_data'] = Garage::all();
        $viewData['header_link'] = HeaderLink::where("menu_id", '17')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        //dd($viewData);
        //return view('AutoCare.calendar.create', compact('calendarSetting', 'services'));
        return view('AutoCare.calendar.create', $viewData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'calendar_name' => 'required|string|max:255',
            'am_pm_break_point' => 'nullable|string|max:255',
            'calendar_type' => 'required|string|max:255',
            'slot_perday_booking' => 'nullable|numeric|max:50',
            'mot_perday_booking' => 'nullable|numeric|max:50',
            'open_close_hours' => 'nullable|array',
            'holidays' => 'nullable|array',
            'block_date_time' => 'nullable|array',
            'block_specific_datetime' => 'nullable|array',
            'ramps_block_day_time' => 'nullable|array',
            'block_fitting_type_days' => 'nullable|array',
            'block_service_perdays' => 'nullable|array',
            'block_service_perhours' => 'nullable|array',
            'block_fitting_type_datetime' => 'nullable|array',
            'block_service_datetime' => 'nullable|array',
        ]);
        $calendarSetting = CalendarSetting::findOrFail($id);

        $fields = [
            'calendar_name',
            'calendar_type',
            'open_close_hours',
            'am_pm_break_point',
            'slot_perday_booking',
            'mot_perday_booking',
            'holidays',
            'duration',
            'block_date_time',
            'block_specific_datetime',
            'ramps_block_day_time',
            'block_fitting_type_days',
            'block_service_perdays',
            'block_service_perhours',
            'block_fitting_type_datetime',
            'block_service_datetime',
        ];

        $data = [];

        foreach ($fields as $field) {
            $data[$field] = $request->has($field) ? $request->input($field) : null;
        }
        $calendarSetting->update($data);
        return redirect()->route('calendar.edit', $id)->with('success', 'Calendar setting updated successfully');
    }

    public function destroy($id)
    {
        $this->authorizePermission('calendar.delete');
        $calendarSetting = CalendarSetting::findOrFail($id);
        $calendarSetting->delete();

        return redirect()->route('calendar.index')->with('success', 'Calendar setting deleted successfully');
    }

    public function getCalendarSettings()
    {
        $calendarSettings = CalendarSetting::where('default', 1)->first();

        if (!$calendarSettings) {
            return response()->json(['error' => 'No calendar settings found'], 404);
        }

        $openCloseHours = $calendarSettings->open_close_hours;

        $blockedTimes = $calendarSettings->block_date_time;
        $blockedSpecificDateTimes = $calendarSettings->block_specific_datetime;
        $blockedRampsDayTime = $calendarSettings->ramps_block_day_time;
        $blockFittingTypeDays = $calendarSettings->block_fitting_type_days;
        $blockFittingTypeDateTime = $calendarSettings->block_fitting_type_datetime ?? [];
        $blockServiceDateTime = $calendarSettings->block_service_datetime ?? [];
        $holidays = $calendarSettings->holidays;
        $duration = $calendarSettings->duration;
        $slotPerdayBooking = $calendarSettings->slot_perday_booking;
        $motSlotPerdayBooking = $calendarSettings->mot_perday_booking;
        $businessHours = [];
        $daysMapping = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
        foreach ($openCloseHours as $day => $times) {
            $isClosed = isset($times['closed']) && $times['closed'] === 'on';
            if (!$isClosed && isset($times['opening']) && isset($times['closing'])) {
                $businessHours[] = [
                    'daysOfWeek' => [$daysMapping[$day]],
                    'startTime' => $times['opening'],
                    'endTime' => $times['closing'],
                ];
            }
        }

        return response()->json([
            'businessHours' => $businessHours,
            'blockedTimes' => $blockedTimes,
            'blockedSpecificDateTimes' => $blockedSpecificDateTimes,
            'blockedRampsDayTime' => $blockedRampsDayTime,
            'blockFittingTypeDays' => $blockFittingTypeDays,
            'blockFittingTypeDateTime' => $blockFittingTypeDateTime,
            'blockServiceDateTime' => $blockServiceDateTime,
            'holidays' => $holidays,
            'slotPerdayBooking' => $slotPerdayBooking,
            'motSlotPerdayBooking' => $motSlotPerdayBooking,
            'duration' => $duration,
        ]);
    }
    public function getWebsiteCalendarSettings()
    {
        $garageId = Session::get('selected_garage_id');
        if (!$garageId) {
            return response()->json(['error' => 'Please select a garage first'], 404);
        }
        $calendarSettings = CalendarSetting::where('garage_id', $garageId)->first();

        if (!$calendarSettings) {
            return response()->json(['error' => 'No calendar settings found'], 404);
        }

        $openCloseHours = $calendarSettings->open_close_hours;
        $blockedTimes = $calendarSettings->block_date_time;
        $blockedSpecificDateTimes = $calendarSettings->block_specific_datetime;
        $blockedRampsDayTime = $calendarSettings->ramps_block_day_time;
        $block = $calendarSettings->block_fitting_type_days;
        $blockServicePerhours = $calendarSettings->block_service_perhours;
        $BlockServiceDateTime = $calendarSettings->block_service_datetime;
        $BlockFittingTypeDateTime = $calendarSettings->block_fitting_type_datetime;
        $holidays = $calendarSettings->holidays;
        $duration = $calendarSettings->duration;
        $slotPerdayBooking = $calendarSettings->slot_perday_booking;
        $motSlotPerdayBooking = $calendarSettings->mot_perday_booking;
        $currentFittingTypes = $this->getCurrentFittingTypesFromCart();
        $currentServiceTypes = $this->getblockServicePerhours();
        $filteredBlockServicePerhours = [];
        $filteredBlockFittingTypeDays = [];
        $filteredBlockServiceDateTime = [];
        $filteredBlockFittingTypeDateTime = [];

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

        if (isset($BlockFittingTypeDateTime['jobtype']) && is_array($BlockFittingTypeDateTime['jobtype'])) {
            foreach ($BlockFittingTypeDateTime['jobtype'] as $index => $jobtype) {
                if (in_array($jobtype, $currentFittingTypes)) {
                    $filteredBlockFittingTypeDateTime[] = [
                        'date' => $BlockFittingTypeDateTime['date'][$index] ?? null,
                        'from' => $BlockFittingTypeDateTime['from'][$index] ?? null,
                        'to' => $BlockFittingTypeDateTime['to'][$index] ?? null,
                        'jobtype' => $jobtype,
                        'block_title' => $BlockFittingTypeDateTime['block_title'][$index] ?? null,
                    ];
                }
            }
        }

        $currentServiceTypeIds = [];
        foreach ($currentServiceTypes as $service) {
            if (isset($service['service_type'])) {
                $currentServiceTypeIds[] = $service['service_type'];
            }
        }

        if (isset($blockServicePerhours['service_type']) && is_array($blockServicePerhours['service_type'])) {
            foreach ($blockServicePerhours['service_type'] as $index => $block) {
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

        if (isset($BlockServiceDateTime['service_type']) && is_array($BlockServiceDateTime['service_type'])) {
            foreach ($BlockServiceDateTime['service_type'] as $index => $block) {
                if (in_array($block, $currentServiceTypeIds)) {
                    $filteredBlockServiceDateTime[] = [
                        'date' => $BlockServiceDateTime['date'][$index] ?? null,
                        'from' => $BlockServiceDateTime['from'][$index] ?? null,
                        'to' => $BlockServiceDateTime['to'][$index] ?? null,
                        'service_type' => $BlockServiceDateTime['service_type'][$index] ?? null,
                        'block_title' => $BlockServiceDateTime['block_title'][$index] ?? null,
                    ];
                }
            }
        }

        $businessHours = [];
        $daysMapping = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
        $validRangeStart = $this->getValidRangeStart();
        $breakPoint = $calendarSettings->am_pm_break_point ?? ($times['break_point'] ?? '12:00');
        foreach ($openCloseHours as $day => $times) {
            $isClosed = isset($times['closed']) && $times['closed'] === 'on';
            if (!$isClosed && isset($times['opening']) && isset($times['closing'])) {
                $businessHours[] = [
                    'daysOfWeek' => [$daysMapping[$day]],
                    'startTime' => $times['opening'],
                    'endTime' => $times['closing'],
                    'validRangeStart' => $validRangeStart,
                    'validRangeEnd' => date('Y-m-d', strtotime('+3 month')) . 'T20:00:00',
                    'breakPoint' => $breakPoint,
                ];
            }
        }

        $motBookedSlots = [];
        if (!empty($currentServiceTypeIds)) {
            $serviceNames = CarService::whereIn('service_id', $currentServiceTypeIds)->pluck('name')->toArray();
            foreach ($serviceNames as $name) {
                if (stripos($name, 'mot') !== false) {
                    $motBookedSlots = $this->getMotBookedSlots($garageId, $motSlotPerdayBooking);
                    break;
                }
            }
        }

        $fullyBookedSlots = $this->getFullyBookedSlots($garageId, $slotPerdayBooking);
        return response()->json([
            'businessHours' => $businessHours,
            'blockedTimes' => $blockedTimes,
            'blockedSpecificDateTimes' => $blockedSpecificDateTimes,
            'blockedRampsDayTime' => $blockedRampsDayTime,
            'blockFittingTypeDays' => $filteredBlockFittingTypeDays,
            'blockServicePerhours' => $filteredBlockServicePerhours,
            'blockFittingTypeDateTime' => $filteredBlockFittingTypeDateTime,
            'blockServiceDateTime' => $filteredBlockServiceDateTime,
            'holidays' => $holidays,
            'fullyBookedSlots' => $fullyBookedSlots,
            'motBookedSlots' => $motBookedSlots,
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

        $cartItems = session('cart', []);

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
        $supplier = Supplier::where('supplier_name', $tyreSource)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        return $supplier->id ?? $supplier;
    }

    private function getDeliveryTime($supplierId, $fitting_type)
    {
        $deliveryTimes = DeliveryTime::where('supplier', $supplierId)->where('delivery_type', $fitting_type)->get();
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

    private function getFullyBookedSlots($garageId, $slotPerBooking)
    {
        $bookings = Booking::selectRaw(
            'DATE(bookings.start) as date, 
            TIME(bookings.start) as time_slot_start,
            TIME(bookings.end) as time_slot_end,
            COUNT(*) as booking_count'
        )
            ->join('workshops', 'bookings.workshop_id', '=', 'workshops.id')
            // ->where('workshops.workshop_origin', 'website')
            ->where('bookings.garage_id', $garageId)
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
    private function getMotBookedSlots($garageId, $motSlotPerdayBooking)
    {
        $bookings = Booking::selectRaw(
            'DATE(bookings.start) as date, 
            TIME(bookings.start) as time_slot_start,
            TIME(bookings.end) as time_slot_end,
            COUNT(*) as booking_count'
        )
            ->join('workshops', 'bookings.workshop_id', '=', 'workshops.id')
            ->join('workshop_services', 'workshops.id', '=', 'workshop_services.workshop_id')
            ->where('workshops.workshop_origin', 'website')
            ->where('workshops.garage_id', $garageId)
            ->whereRaw('LOWER(workshop_services.service_name) LIKE ?', ['%mot%'])
            ->whereDate('bookings.start', '>=', now()->toDateString())
            ->groupBy('date', 'time_slot_start', 'time_slot_end')
            ->get();

        $motBookedSlots = [];

        foreach ($bookings as $booking) {
            if ($booking->booking_count >= $motSlotPerdayBooking) {
                $motBookedSlots[] = [
                    'date' => $booking->date,
                    'start' => $booking->time_slot_start,
                    'end' => $booking->time_slot_end,
                ];
            }
        }
        return $motBookedSlots;
    }
    public function getEvents()
    {
        $calendarAdminBookingActive = get_option('calendar_admin_booking_active');

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
            'workshops.payment_method as payment_method'
        )
            ->leftJoin('workshops', 'bookings.workshop_id', '=', 'workshops.id')
            ->where(function ($q) {
                $q->where('workshops.is_void', false)
                    ->orWhere('workshops.is_void', 0)
                    ->orWhereNull('workshops.is_void');
            });

        if ($calendarAdminBookingActive == 0) {
            $query->where('workshops.workshop_origin', 'Website');
        }

        $bookings = $query->get();
        $workshopIds = $bookings->pluck('workshop_id')->unique()->toArray();
        $tyreWorkshopIds = \DB::table('workshop_tyres')
            ->whereIn('workshop_id', $workshopIds)
            ->pluck('workshop_id')
            ->toArray();

        $serviceWorkshopIds = \DB::table('workshop_services')
            ->whereIn('workshop_id', $workshopIds)
            ->where('service_name', 'not like', '%mot%')
            ->pluck('workshop_id')
            ->toArray();

        $motServiceWorkshopIds = \DB::table('workshop_services')
            ->whereIn('workshop_id', $workshopIds)
            ->where('service_name', 'like', '%mot%')
            ->pluck('workshop_id')
            ->toArray();


        $formattedBookings = $bookings->map(function ($booking) use ($tyreWorkshopIds, $serviceWorkshopIds, $motServiceWorkshopIds) {
            $startTime = Carbon::parse($booking->start, 'Europe/London')->format(' h:iA');
            $isTyre = in_array($booking->workshop_id, $tyreWorkshopIds);
            $isService = in_array($booking->workshop_id, $serviceWorkshopIds);
            $isMotService = in_array($booking->workshop_id, $motServiceWorkshopIds);
            $version = time();
            $icons = [];

            if ($isTyre) {
                $icons[] = '<img src="' . asset('img/calendar-tyre.png') . '?v=' . $version . '" alt="Tyre" width="15" height="15">';
            }

            if ($isService) {
                $icons[] = '<img src="' . asset('img/calendar-service.png') . '?v=' . $version . '" alt="Service" width="15" height="15">';
            }

            if ($isMotService) {
                $icons[] = '<img src="' . asset('img/calendar-mot.png') . '?v=' . $version . '" alt="Service" width="15" height="15">';
            }

            if (empty($icons)) {
                $icons[] = '<img src="' . asset('img/calendar-tyre.png') . '?v=' . $version . '" alt="Other" width="15" height="15">';
            }

            $iconsHtml = implode('', $icons);
            $title = "{$iconsHtml}{$startTime} {$booking->title} "
                . "<span class=\"calendar_reg\">" . strtoupper($booking->vrm) . "</span>"
                . " - #{$booking->workshop_id}";
            $typeNames = [];
            if ($isTyre)
                $typeNames[] = 'Tyre';
            if ($isService)
                $typeNames[] = 'Service';
            if ($isMotService)
                $typeNames[] = 'MOT';
            if (empty($typeNames))
                $typeNames[] = 'Other';

            return [
                'id' => $booking->id,
                'workshop_id' => $booking->workshop_id,
                'start' => Carbon::parse($booking->start, 'Europe/London')->toIso8601String(),
                'end' => Carbon::parse($booking->end, 'Europe/London')->toIso8601String(),
                'title' => $title,
                'backgroundColor' => $this->getEventColor($booking->status),
                'borderColor' => $this->getEventColor($booking->status),
                'textColor' => '#fff',
                'display' => 'block',
                'workshop_details' => [
                    'name' => $booking->workshop_name,
                    'address' => $booking->workshop_address,
                    'payment_method' => $booking->payment_method,
                    'vrm' => $booking->vrm,
                    'types' => implode(', ', $typeNames),
                ],
            ];
        });

        return response()->json($formattedBookings);
    }

    private function getEventColor($status)
    {
        $defaultColor = get_option('calendar_booking_color_default', '#0000ff');
        switch ($status) {
            case 'completed':
                return get_option('calendar_booking_color_completed', '#00ff00');
            case 'pending':
                return get_option('calendar_booking_color_pending', '#ffcc00');
            case 'booked':
                return get_option('calendar_booking_color_booked', '#2ed943');
            case 'canceled':
                return get_option('calendar_booking_color_canceled', '#2ed943');
            case 'failed':
                return get_option('calendar_booking_color_failed', '#2ed943');
            case 'awaiting':
                return get_option('calendar_booking_color_awaiting', '#2ed943');
            default:
                return $defaultColor;
        }
    }


}