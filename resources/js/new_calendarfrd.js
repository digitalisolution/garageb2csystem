require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { DateTime } from 'luxon';

import { Modal } from 'bootstrap'; // Import Bootstrap Modal

const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Europe/London';

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        fetchCalendarSettings()
            .then(data => {
                const businessHours = formatBusinessHours(data.businessHours);
                const { slotMinTime, slotMaxTime } = calculateSlotTimes(businessHours); // Calculate dynamic slot times
                const blockedEvents = createBlockedEvents(data);
                const slotDuration = data.duration || 30; // Default to 30 minutes if duration is not provided
                // console.log(data);
                initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration);
            })
            .catch(error => {
                console.error('Error loading calendar settings:', error);
                alert('Failed to load calendar settings. Please try again later.');
            });
    }
});

/**
 * Fetch calendar settings from the server.
 * @returns {Promise<Object>} Calendar settings data.
 */
async function fetchCalendarSettings() {
    const response = await fetch('/calendar-settings');
    if (!response.ok) {
        throw new Error('Failed to fetch calendar settings');
    }
    return response.json();
}

/**
 * Format business hours for FullCalendar.
 * @param {Array} businessHours - Business hours data.
 * @returns {Array} Formatted business hours.
 */
function formatBusinessHours(businessHours) {
    return businessHours
        .filter(hour => !hour.closed) // Exclude closed days
        .map(hour => ({
            daysOfWeek: hour.daysOfWeek,
            startTime: hour.startTime,
            endTime: hour.endTime,
        }));
}

/**
 * Calculate the earliest start time and latest end time from business hours.
 * @param {Array} businessHours - Formatted business hours.
 * @returns {Object} Object containing `slotMinTime` and `slotMaxTime`.
 */

function calculateSlotTimes(businessHours) {
    let slotMinTime = '23:59'; // Initialize with the latest possible time
    let slotMaxTime = '00:00'; // Initialize with the earliest possible time

    businessHours.forEach(hour => {
        if (hour.startTime < slotMinTime) {
            slotMinTime = hour.startTime; // Update to the earliest start time
        }
        if (hour.endTime > slotMaxTime) {
            slotMaxTime = hour.endTime; // Update to the latest end time
        }
    });

    return { slotMinTime, slotMaxTime };
}

/**
 * Create blocked events from the fetched data.
 * @param {Object} data - Calendar settings data.
 * @returns {Array} Array of blocked events.
 */

function createBlockedEvents(data) {
    const blockedEvents = [];

    // Convert blocked date times into events
    if (data.blockedTimes && data.blockedTimes.days) {
        const daysMapping = {
            Sunday: 0,
            Monday: 1,
            Tuesday: 2,
            Wednesday: 3,
            Thursday: 4,
            Friday: 5,
            Saturday: 6,
        };

        data.blockedTimes.days.forEach((day, index) => {
            if (day === "all") {
                // If "all", block the time for all days of the week
                blockedEvents.push({
                    title: data.blockedTimes.block_title[index],
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6], // All days
                    startTime: data.blockedTimes.from[index],
                    endTime: data.blockedTimes.to[index],
                    backgroundColor: 'red',
                    borderColor: 'red',
                    display: 'background', // Ensure blocked times are displayed as background events
                });
            } else {
                // Block the time for a specific day
                const dayNumber = daysMapping[day];
                if (dayNumber !== undefined) {
                    blockedEvents.push({
                        title: data.blockedTimes.block_title[index],
                        daysOfWeek: [dayNumber], // Convert day name to number
                        startTime: data.blockedTimes.from[index],
                        endTime: data.blockedTimes.to[index],
                        backgroundColor: 'red',
                        borderColor: 'red',
                        display: 'background', // Ensure blocked times are displayed as background events
                    });
                } else {
                    console.error(`Invalid day name: ${day}`);
                }
            }
        });
    }

    // Convert holidays into blocked events
    if (data.holidays && data.holidays.holidayDate) {
        data.holidays.holidayDate.forEach((date, index) => {
            blockedEvents.push({
                title: data.holidays.holiday_name[index],
                start: date,
                end: date, // Holidays are all-day events
                backgroundColor: '#ffcc00',
                borderColor: '#ffcc00',
                allDay: true, // Mark as all-day event
                display: 'background', // Ensure holidays are displayed as background events
            });
        });
    }

    // console.log("Blocked Events:", blockedEvents); // Debugging: Log blocked events
    return blockedEvents;
}

/**
 * Check if the selected slot overlaps with any blocked events.
 * @param {Date} start - The start date and time of the selected slot.
 * @param {Date} end - The end date and time of the selected slot.
 * @param {Array} blockedEvents - Array of blocked events.
 * @returns {boolean} True if the slot is blocked, otherwise false.
 */
function isSlotBlocked(start, end, blockedEvents) {
    return blockedEvents.some(blockedEvent => {
        let blockedStart, blockedEnd;

        if (blockedEvent.startTime && blockedEvent.endTime) {
            // For recurring blocked times (e.g., lunch breaks)
            const today = new Date(start).toISOString().split('T')[0]; // Get today's date
            blockedStart = new Date(`${today}T${blockedEvent.startTime}`);
            blockedEnd = new Date(`${today}T${blockedEvent.endTime}`);

            // Check if the blocked event applies to the selected day
            if (blockedEvent.daysOfWeek) {
                const selectedDay = start.getDay(); // Get the day of the week (0-6)
                if (!blockedEvent.daysOfWeek.includes(selectedDay)) {
                    return false; // Skip if the blocked event doesn't apply to the selected day
                }
            }
        } else if (blockedEvent.start && blockedEvent.end) {
            // For one-time blocked events (e.g., holidays)
            blockedStart = new Date(blockedEvent.start);
            blockedEnd = new Date(blockedEvent.end);

            // Check if the selected slot overlaps with the holiday
            if (blockedEvent.allDay) {
                // For all-day events (holidays), check if the selected slot is on the same day
                const selectedDate = start.toISOString().split('T')[0]; // Get the selected date
                const holidayDate = blockedStart.toISOString().split('T')[0]; // Get the holiday date
                if (selectedDate === holidayDate) {
                    return true; // Block the entire day
                }
            } else {
                // For non-all-day events, check for overlap
                return (
                    (start >= blockedStart && start < blockedEnd) || // Selected start time is within a blocked slot
                    (end > blockedStart && end <= blockedEnd) || // Selected end time is within a blocked slot
                    (start <= blockedStart && end >= blockedEnd) // Selected slot completely overlaps a blocked slot
                );
            }
        } else {
            return false; // Skip invalid blocked events
        }

        // console.log("Blocked Slot:", { blockedStart, blockedEnd }); // Debugging: Log blocked slot

        return (
            (start >= blockedStart && start < blockedEnd) || // Selected start time is within a blocked slot
            (end > blockedStart && end <= blockedEnd) || // Selected end time is within a blocked slot
            (start <= blockedStart && end >= blockedEnd) // Selected slot completely overlaps a blocked slot
        );
    });
}
function formatDateTime(date) {
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: userTimeZone // Set UK timezone
    };

    return new Intl.DateTimeFormat('en-GB', options).format(date);
}

function formatTime(date) {
    return date.toLocaleString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: userTimeZone // UK Time
    });
}

function getLondonDate() {
    const londonTimeString = new Intl.DateTimeFormat('en-GB', {
        timeZone: userTimeZone,
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false, // 24-hour format
    }).format(new Date());

    // Extract components and construct Date object
    const [day, month, year, hours, minutes, seconds] = londonTimeString.match(/\d+/g);
    return new Date(`${year}-${month}-${day}T${hours}:${minutes}:${seconds}`);
}

// Get current UK date
const now = getLondonDate();
const enddateuk = new Date(now);
enddateuk.setDate(now.getDate() + 6);
const formattedEnd = enddateuk.toLocaleDateString('en-GB');


/**
 * Calculate blocked times outside opening and closing hours.
 * @param {Array} businessHours - Business hours data.
 * @returns {Array} Array of blocked events.
 */
function calculateBlockedTimes(businessHours) {
    const blockedTimes = [];

    // Define the earliest and latest possible times for the calendar
    const earliestTime = '00:00';
    const latestTime = '23:59';

    // Iterate through each day of the week
    for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
        const dayHours = businessHours.find(hour => hour.daysOfWeek.includes(dayOfWeek));

        if (dayHours) {
            const { startTime, endTime } = dayHours;

            // Block time before opening hours
            if (startTime > earliestTime) {
                blockedTimes.push({
                    daysOfWeek: [dayOfWeek],
                    startTime: earliestTime,
                    endTime: startTime,
                    backgroundColor: 'rgba(255, 0, 0, 0.2)', // Light red for blocked times
                    borderColor: 'rgba(255, 0, 0, 0.2)',
                    display: 'background',
                });
            }

            // Block time after closing hours
            if (endTime < latestTime) {
                blockedTimes.push({
                    daysOfWeek: [dayOfWeek],
                    startTime: endTime,
                    endTime: latestTime,
                    backgroundColor: 'rgba(255, 0, 0, 0.2)', // Light red for blocked times
                    borderColor: 'rgba(255, 0, 0, 0.2)',
                    display: 'background',
                });
            }
        } else {
            // Block the entire day if no business hours are defined
            blockedTimes.push({
                daysOfWeek: [dayOfWeek],
                startTime: earliestTime,
                endTime: latestTime,
                backgroundColor: 'rgba(255, 0, 0, 0.2)', // Light red for blocked times
                borderColor: 'rgba(255, 0, 0, 0.2)',
                display: 'background',
            });
        }
    }

    return blockedTimes;
}


/**
 * Initialize FullCalendar with the provided data.
 * @param {HTMLElement} calendarEl - The calendar container element.
 * @param {Array} businessHours - Formatted business hours.
 * @param {Array} blockedEvents - Array of blocked events.
 * @param {string} slotMinTime - Minimum time to display on the calendar.
 * @param {string} slotMaxTime - Maximum time to display on the calendar.
 * @param {number} slotDuration - Duration of each slot in minutes.
 */
function initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration) {
    let selectedEvent = null; // Track the currently selected event

    const now = getLondonDate();
    const today = now;
    const currentHour = now.getHours();
    const currentDayIndex = now.getDay();

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        timeZone: 'Europe/London',
        initialDate: today, // Start from today
        firstDay: currentDayIndex, // Ensure the first visible day is today
        validRange: { start: today }, // Prevent past dates
        visibleRange: { start: today, end: formattedEnd }, // Set visible range dynamically
        scrollTime: `${now.getHours()}:00:00`, // Auto-scroll to current time

        slotMinTime: slotMinTime,
        slotMaxTime: slotMaxTime,
        slotDuration: `00:${slotDuration}:00`, // Ensure single-slot selection
        selectable: true,
        selectOverlap: false, // Prevent selecting overlapping slots
        longPressDelay: 0, // Prevent long-press selection on mobile
        selectConstraint: { start: '00:00', end: '23:59' }, 
        allDaySlot: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek',
        },

        businessHours: businessHours,
        events: [...calculateBlockedTimes(businessHours), ...blockedEvents], // Include blocked slots
        displayEventTime: false,

        dayHeaderContent: function (arg) {
            const date = new Date(arg.date);
            return date.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: '2-digit' }).replace(',', '');
        },

        selectAllow: function (info) {
            return info.start >= new Date(); // Prevent past slots
        },

        select: function (info) {
            const startLuxon = DateTime.fromISO(info.startStr, { zone: 'Europe/London' });
            const endLuxon = startLuxon.plus({ minutes: slotDuration });
        
            const start = startLuxon.toJSDate(); // Local time version of UK slot
            const end = endLuxon.toJSDate();
        
            if (isSlotBlocked(start, end, blockedEvents)) return;
            if (start < new Date()) return;
        
            if (selectedEvent) selectedEvent.remove();
        
            // selectedEvent = calendar.addEvent({
            //     title: `${startLuxon.toFormat('hh:mm a')} - Available`,
            //     start,
            //     end,
            //     backgroundColor: '#6aeb53',
            //     borderColor: '#6aeb53',
            //     display: 'block',
            //     textColor: '#000',
            // });
        
            document.getElementById('selectedSlot').textContent =
                `Selected Slot: ${startLuxon.toFormat('dd MMM yyyy, hh:mm a')} - ${endLuxon.toFormat('hh:mm a')}`;
        
            saveSelectedSlot(start, end);
        }
        
    });

    calendar.render();
}


/**
 * Save the selected slot in the session via AJAX.
 * @param {Date} start - The start date and time of the selected slot.
 * @param {Date} end - The end date and time of the selected slot.
 */
function saveSelectedSlot(start, end) {
    const startLocal = start.toISOString(); // Convert to ISO string for consistency
    const endLocal = end.toISOString();

    fetch('/save-selected-slot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Add CSRF token
        },
        body: JSON.stringify({
            start: startLocal,
            end: endLocal,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Slot saved successfully:', data.message);
            } else {
                console.error('Error saving slot:', data.message);
                alert('Failed to save slot. Please try again.');
            }
        })
        .catch(error => {
            console.error('Unexpected error:', error);
            alert('Unexpected error occurred. Please try again.');
        });
}