require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', async function () {
    const calendarEl = document.getElementById('ampm-calendar');
    if (!calendarEl) return;

    try {
        const data = await fetchCalendarSettings();
        const blockedEvents = createBlockedEvents(data);
        const businessHours = formatBusinessHours(data.businessHours);
        const validRangeStart = data.businessHours[0].validRangeStart;
        const validRangeEnd = data.businessHours[0].validRangeEnd;

        initializeAmPmCalendar(calendarEl, businessHours, blockedEvents, validRangeStart, validRangeEnd);
    } catch (err) {
        console.error(err);
        alert('Failed to load calendar settings');
    }
});

async function fetchCalendarSettings() {
    const res = await fetch('/calendar-website-settings');
    if (!res.ok) throw new Error('Failed to fetch calendar settings');
    return res.json();
}


/**
 * Format business hours for FullCalendar.
 * @param {Array} businessHours
 * @returns {Array}
 */
function formatBusinessHours(businessHours) {
    return businessHours
        .filter(hour => !hour.closed)
        .map(hour => ({
            daysOfWeek: hour.daysOfWeek,
            startTime: hour.startTime,
            endTime: hour.endTime,
            breakPoint: hour.breakPoint
        }));
}


function createBlockedEvents(data) {

    const blocked = [];
    const daysMapping = { Sunday: 0, Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6 };

    data.blockedTimes?.days?.forEach((day, i) => {
        const dayIndexes = day === 'all' ? [0, 1, 2, 3, 4, 5, 6] : [daysMapping[day]];

        const from = data.blockedTimes.from?.[i] || '08:00';
        const to = data.blockedTimes.to?.[i] || '20:00';

        dayIndexes.forEach(d => blocked.push({
            dayIndex: d,
            from,
            to,
            title: data.blockedTimes.block_title?.[i] || 'Closed'
        }));
    });


    data.holidays?.holidayDate?.forEach((date, i) => {
        blocked.push({
            date,
            fullDay: true,
            title: data.holidays.holiday_name?.[i] || 'Holiday'
        });
    });

    data.blockedSpecificDateTimes?.date?.forEach((date, i) => {
        blocked.push({
            date,
            from: data.blockedSpecificDateTimes.from[i],
            to: data.blockedSpecificDateTimes.to[i],
            title: data.blockedSpecificDateTimes.block_title?.[i] || 'Blocked'
        });
    });

    data.fullyBookedSlots?.forEach(slot => {
        blocked.push({
            date: slot.date,
            from: slot.start,
            to: slot.end,
            title: 'Fully Booked'
        });
    });

    data.motBookedSlots?.forEach(slot => {
        blocked.push({
            date: slot.date,
            from: slot.start,
            to: slot.end,
            title: 'MOT Fully Booked'
        });
    });

    data.blockServicePerhours?.forEach(block => {
        const dayIndexes = block.days === 'all' ? [0, 1, 2, 3, 4, 5, 6] : [daysMapping[block.days]];
        dayIndexes.forEach(d => blocked.push({
            dayIndex: d,
            from: block.from,
            to: block.to,
            title: block.block_title || 'Service Block'
        }));
    });

    data.blockServiceDateTime?.forEach(block => {
        if (!block.date) return;
        blocked.push({
            date: block.date,
            from: block.from,
            to: block.to,
            title: block.block_title || 'Service Block'
        });
    });

    return blocked;
}

function getBusinessHoursForDate(dateStr, businessHours) {
    const date = new Date(dateStr + 'T00:00:00');
    const dayIndex = date.getDay();

    const dayHours = businessHours.find(bh => bh.daysOfWeek.includes(dayIndex));

    if (!dayHours) return null;

    const open = parseInt(dayHours.startTime.split(':')[0]);
    const close = parseInt(dayHours.endTime.split(':')[0]);

    return { open, close };
}

function getDayPeriods(dateStr, businessHours) {
    const bh = businessHours.find(bh => bh.daysOfWeek.includes(new Date(dateStr).getDay()));
    if (!bh) return null;

    const parseTime = (t) => {
        const [h, m] = t.split(':').map(Number);
        return { h, m };
    };

    const open = parseTime(bh.startTime);
    const close = parseTime(bh.endTime);
    const breakPoint = parseTime(bh.breakPoint || '12:00');

    const amClosed = open.h > breakPoint.h || (open.h === breakPoint.h && open.m >= breakPoint.m);

    return {
        AM: amClosed ? null : { start: open, end: breakPoint },
        PM: { start: breakPoint, end: close }
    };
}

function getPeriodStatus(dateStr, period, blockedEvents, businessHours) {
    const periods = getDayPeriods(dateStr, businessHours);
    if (!periods || !periods[period]) return { available: false, reason: 'Closed' };

    const periodStart = periods[period].start.h;
    const periodEnd = periods[period].end.h;

    const now = new Date();
    const selectedDate = new Date(dateStr + 'T00:00:00');
    const isToday = selectedDate.toDateString() === now.toDateString();

    if (isToday && now.getHours() >= periodEnd)
        return { available: false, reason: 'Past time' };

    for (let be of blockedEvents) {
        const sameDate = be.date && be.date === dateStr;
        const sameDay = be.dayIndex !== undefined && be.dayIndex === selectedDate.getDay();
        if (!sameDate && !sameDay) continue;

        if (be.fullDay) return { available: false, reason: be.title };

        if (be.from && be.to) {
            const blockStart = parseInt(be.from.split(':')[0]);
            const blockEnd = parseInt(be.to.split(':')[0]);
            const intersects = Math.max(blockStart, periodStart) < Math.min(blockEnd, periodEnd);
            if (intersects) return { available: false, reason: be.title };
        }
    }

    return { available: true, reason: '' };
}
function getLondonDate() {
    const londonTimeString = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/London',
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false,
    }).format(new Date());
    const [day, month, year, hours, minutes, seconds] = londonTimeString.match(/\d+/g);
    return new Date(`${year}-${month}-${day}T${hours}:${minutes}:${seconds}`);
}
function getVisibleRangeStart(date) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    return d.toISOString().split('T')[0];
}

function initializeAmPmCalendar(calendarEl, businessHours, blockedEvents, validRangeStart, validRangeEnd) {

    let selectedEvent = null;
    const now = getLondonDate();
    const today = now;
    const currentHour = now.getHours();
    const currentDayIndex = now.getDay();
    const todayStr = today.toISOString().split('T')[0];

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        initialDate: today,
        // firstDay: currentDayIndex,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },

    showNonCurrentDates: false,
    fixedWeekCount: false,

        validRange: { start: todayStr, end: validRangeEnd },

        visibleRange: {
            start: todayStr,
            end: validRangeEnd
        },


        eventDidMount(info) {
            const dateStr = info.event.extendedProps.date;
            const period = info.event.extendedProps.period;
            const periods = getDayPeriods(dateStr, businessHours);

            if (info.event.extendedProps.blocked) {
                info.el.title = info.event.extendedProps.reason || 'Unavailable';
                info.el.style.cursor = 'not-allowed';
            } else if (periods && periods[period]) {
                const { start, end } = periods[period];

                // Pad hours and minutes to always have 2 digits
                const startTime = `${String(start.h).padStart(2, '0')}:${String(start.m).padStart(2, '0')}`;
                const endTime = `${String(end.h).padStart(2, '0')}:${String(end.m).padStart(2, '0')}`;

                info.el.title = `${period} (${startTime} - ${endTime})`;
                info.el.style.cursor = 'pointer';
            }
        },

        events(fetchInfo, successCallback) {
            const events = [];

            const start = new Date(Math.max(fetchInfo.start.getTime(), new Date(validRangeStart).getTime()));
            const end = new Date(Math.min(fetchInfo.end.getTime(), new Date(validRangeEnd).getTime()));

            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                const isoDate = d.toISOString().split('T')[0];
                const periods = getDayPeriods(isoDate, businessHours);

                const validStart = new Date(validRangeStart);

                    ['AM', 'PM'].forEach(period => {

                        if (!periods || !periods[period]) return;

                        const periodEndHour = periods[period].end.h;

                        const periodEnd = new Date(isoDate);
                        periodEnd.setHours(periodEndHour, 0, 0, 0);

                        // Block slots before validRangeStart
                        if (periodEnd <= validStart) return;

                        const status = getPeriodStatus(isoDate, period, blockedEvents, businessHours);

                        events.push({
                            title: period,
                            start: isoDate,
                            allDay: true,
                            backgroundColor: status.available ? '#6aeb53' : 'red',
                            borderColor: status.available ? '#6aeb53' : 'red',
                            extendedProps: {
                                period,
                                blocked: !status.available,
                                date: isoDate,
                                reason: status.reason || ''
                            }
                        });

                    });
            }

            successCallback(events);
        },

        eventClick(info) {

            if (info.event.extendedProps.blocked) {
                alert(info.event.extendedProps.reason);
                return;
            }

            if (selectedEvent) selectedEvent.setProp('backgroundColor', '#6aeb53');

            selectedEvent = info.event;
            selectedEvent.setProp('backgroundColor', '#007bff');

            const d = new Date(info.event.extendedProps.date);
            const formattedDate = `${d.getDate()}-${d.getMonth() + 1}-${d.getFullYear()}`;

            document.getElementById('selectedSlot').textContent =
                `Selected Slot: ${formattedDate} - ${info.event.extendedProps.period}`;

            saveSelectedSlot(info.event.extendedProps.date, info.event.extendedProps.period);
        }
    });

    calendar.render();
}

function saveSelectedSlot(date, period) {
    fetch('/save-selected-slot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ date, period })
    });
}
