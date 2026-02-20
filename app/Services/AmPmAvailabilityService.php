<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\CalendarSession;

class AmPmAvailabilityService
{
    public function getAvailability($days = 30)
    {
        $settings = fetchCalendarSettings();      // YOUR existing helper
        $blockedEvents = createBlockedEvents($settings); // existing
        $session = CalendarSession::first();

        $startDate = now('Europe/London')->startOfDay();
        $endDate   = now('Europe/London')->addDays($days);

        $results = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {

            $results[] = [
                'date' => $date->toDateString(),
                'morning'   => $this->isSessionAvailable($date->copy(), 'morning', $settings, $session, $blockedEvents),
                'afternoon' => $this->isSessionAvailable($date->copy(), 'afternoon', $settings, $session, $blockedEvents),
            ];
        }

        return $results;
    }

    private function isSessionAvailable($date, $period, $settings, $session, $blockedEvents)
    {
        $slotDuration = $settings['slot_duration'];

        if ($period === 'morning') {
            $startTime = $session->morning_start;
            $endTime   = $session->morning_end;
        } else {
            $startTime = $session->afternoon_start;
            $endTime   = $session->afternoon_end;
        }

        $start = Carbon::parse($date->toDateString().' '.$startTime, 'Europe/London');
        $end   = Carbon::parse($date->toDateString().' '.$endTime, 'Europe/London');

        while ($start < $end) {

            $slotEnd = $start->copy()->addMinutes($slotDuration);

            // 🔥 REUSE YOUR SLOT BLOCKING ENGINE
            if (! $this->slotBlocked($start, $slotEnd, $blockedEvents)) {
                return true;
            }

            $start->addMinutes($slotDuration);
        }

        return false;
    }

    private function slotBlocked($start, $end, $blockedEvents)
    {
        // call your existing function here
        return isSlotBlocked($start, $end, $blockedEvents);
    }
}
