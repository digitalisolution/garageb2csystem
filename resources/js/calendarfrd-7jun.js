require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { DateTime } from 'luxon';
import { Modal } from 'bootstrap'; // Import Bootstrap Modal

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        fetchCalendarSettings()
            .then(data => {
                const businessHours = formatBusinessHours(data.businessHours);
                const { slotMinTime, slotMaxTime } = calculateSlotTimes(businessHours); // Calculate dynamic slot times
                const blockedEvents = createBlockedEvents(data);
                const slotDuration = data.duration || 30; // Default to 30 minutes if duration is not provided
                const validRangeStart = data.businessHours[0].validRangeStart;
                const validRangeEnd = data.businessHours[0].validRangeEnd;
                // console.log(data);
                initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration,validRangeStart,validRangeEnd);
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
    const response = await fetch('/calendar-website-settings');
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
                    display: 'background',
                });
            } else {
                // Block the time for a specific day
                const dayNumber = daysMapping[day];
                if (dayNumber !== undefined) {
                    blockedEvents.push({
                        title: data.blockedTimes.block_title[index],
                        daysOfWeek: [dayNumber],
                        startTime: data.blockedTimes.from[index],
                        endTime: data.blockedTimes.to[index],
                        backgroundColor: 'red',
                        borderColor: 'red',
                        display: 'background',
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
                end: date,
                backgroundColor: '#ffcc00',
                borderColor: '#ffcc00',
                allDay: true,
                display: 'background',
            });
        });
    }

   if (data.blockFittingTypeDays) {
    const daysMapping = {
        Sunday: 0,
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6,
    };

       data.blockFittingTypeDays.forEach(block => {
        const day = block.days;
        const startTime = block.from;
        const endTime = block.to;
        const title = block.block_title;

        if (day === 'all') {

            blockedEvents.push({
                title: title,
                daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                startTime: startTime,
                endTime: endTime,
                backgroundColor: 'rgba(255, 0, 0, 0.29)',
                borderColor: 'rgba(255, 0, 0, 0.36)',
                display: 'background',
            });

        } else {
            const dayNumber = daysMapping[day];
            if (dayNumber !== undefined) {
                blockedEvents.push({
                    title: title,
                    daysOfWeek: [dayNumber],
                    startTime: startTime,
                    endTime: endTime,
                    backgroundColor: 'rgba(255, 0, 0, 0.29)',
                    borderColor: 'rgba(255, 0, 0, 0.36)',
                    display: 'background',
                });
            } else {
                console.error(`Invalid day name: ${day}`);
            }
        }
    });
}


if (data.fullyBookedSlots) {
    data.fullyBookedSlots.forEach(slot => {
        blockedEvents.push({
            title: 'Fully Booked',
            start: `${slot.date}T${slot.start}`,
            end: `${slot.date}T${slot.end}`,
            backgroundColor: 'rgb(127 255 7)',
            borderColor: 'rgb(127 255 7)',
            display: 'background',
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
// function isSlotBlocked(start, end, blockedEvents) {
//     return blockedEvents.some(blockedEvent => {
//         let blockedStart, blockedEnd;

//         if (blockedEvent.startTime && blockedEvent.endTime) {
//             // For recurring blocked times (e.g., lunch breaks)
//             const today = new Date(start).toISOString().split('T')[0];
//             blockedStart = new Date(`${today}T${blockedEvent.startTime}`);
//             blockedEnd = new Date(`${today}T${blockedEvent.endTime}`);

//             // Check if the blocked event applies to the selected day
//             if (blockedEvent.daysOfWeek) {
//                 const selectedDay = start.getDay();
//                 if (!blockedEvent.daysOfWeek.includes(selectedDay)) {
//                     return false;
//                 }
//             }
//         } else if (blockedEvent.start && blockedEvent.end) {
//             // For one-time blocked events (e.g., holidays)
//             blockedStart = new Date(blockedEvent.start);
//             blockedEnd = new Date(blockedEvent.end);

//             // Check if the selected slot overlaps with the holiday
//             if (blockedEvent.allDay) {
//                 const selectedDate = start.toISOString().split('T')[0];
//                 const holidayDate = blockedStart.toISOString().split('T')[0];
//                 if (selectedDate === holidayDate) {
//                     return true;
//                 }
//             } else {
//                 // For non-all-day events, check for overlap
//                 return (
//                     (start >= blockedStart && start < blockedEnd) ||
//                     (end > blockedStart && end <= blockedEnd) ||
//                     (start <= blockedStart && end >= blockedEnd)
//                 );
//             }
//         } else {
//             return false; // Skip invalid blocked events
//         }

//         return (
//             (start >= blockedStart && start < blockedEnd) ||
//             (end > blockedStart && end <= blockedEnd) ||
//             (start <= blockedStart && end >= blockedEnd)
//         );
//     });
// }

function isSlotBlocked(selectedStart, selectedEnd, blockedSlots, currentJobType) {
    for (let slot of blockedSlots) {
        if (slot.job_type && slot.job_type !== currentJobType) {
            continue;
        }

        const blockedStart = new Date(slot.start);
        const blockedEnd = new Date(slot.end);

        if (
            (selectedStart >= blockedStart && selectedStart < blockedEnd) ||
            (selectedEnd > blockedStart && selectedEnd <= blockedEnd) ||
            (selectedStart <= blockedStart && selectedEnd >= blockedEnd) // full overlap
        ) {
            return true;
        }
    }
    return false;
}

function getLondonDate() {
    const londonTimeString = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/London',
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
function initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration,validRangeStart,validRangeEnd) {
    let selectedEvent = null;

    const now = getLondonDate();
    const today = now;
    const currentHour = now.getHours();
    const currentDayIndex = now.getDay();

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        timeZone: 'Europe/London',
        initialDate: today,
        firstDay: currentDayIndex,
        validRange: { start: today },
        visibleRange: { start: today, end: formattedEnd },
        scrollTime: `${now.getHours()}:00:00`,

        slotMinTime: slotMinTime,
        slotMaxTime: slotMaxTime,
        slotDuration: `00:${slotDuration}:00`,
        selectable: true,
        selectOverlap: false,
        longPressDelay: 0,
        selectConstraint: { start: '00:00', end: '23:59' }, 
        allDaySlot: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek',
        },

        businessHours: businessHours,
        events: [...calculateBlockedTimes(businessHours), ...blockedEvents,  {
        start: new Date(),
        end: new Date(validRangeStart),
        display: 'background',
        backgroundColor: '#f8d7da', // Light red to indicate blocked area
        overlap: false
        }
        ], // Include blocked slots
        displayEventTime: false,

        dayHeaderContent: function (arg) {
            const date = new Date(arg.date);
            return date.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: '2-digit' }).replace(',', '');
        },

        selectAllow: function (info) {
            return info.start >= new Date(validRangeStart); // Prevent past slots
        },
        select: function (info) {
            const start = info.start;
            const startTime = info.startStr;
            const endTime = info.endStr;
            const end = new Date(start.getTime() + slotDuration * 60000); // Force selection of only one slot

            if (isSlotBlocked(start, end, blockedEvents)) {
                alert('This slot is blocked and cannot be booked.');
                return;
            }

            if (start < new Date()) {
                alert('You cannot select a past slot.');
                return;
            }

            // Remove previous selection before adding new one
            if (selectedEvent) selectedEvent.remove();

            // Add only ONE slot, preventing drag selection
            selectedEvent = calendar.addEvent({
                title: `${formatTimeFromISO(startTime)} - Available`,
                start: start,
                end: end,
                backgroundColor: '#6aeb53',
                borderColor: '#6aeb53',
                display: 'block',
                textColor: '#000',
            });

            document.getElementById('selectedSlot').textContent =
                `Selected Slot: ${formatDateTimeFromISO(startTime)} - ${formatTimeFromISO(endTime)}`;

            saveSelectedSlot(start, end);
        }     
        
    });

    calendar.render();
}

function formatDateTimeFromISO(isoString) {
    return DateTime.fromISO(isoString, { zone: 'Europe/London' }).toFormat('dd LLL yyyy, hh:mm a');
}

function formatTimeFromISO(isoString) {
    return DateTime.fromISO(isoString, { zone: 'Europe/London' }).toFormat('hh:mm a');
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