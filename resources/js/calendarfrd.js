require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { DateTime } from 'luxon';
import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        fetchCalendarSettings()
            .then(data => {
                const businessHours = formatBusinessHours(data.businessHours);
                const { slotMinTime, slotMaxTime } = calculateSlotTimes(businessHours);
                const blockedEvents = createBlockedEvents(data);
                const slotDuration = data.duration || 30;
                const validRangeStart = data.businessHours[0].validRangeStart;
                const validRangeEnd = data.businessHours[0].validRangeEnd;
                initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration, validRangeStart, validRangeEnd);
            })
            .catch(error => {
                console.error('Error loading calendar settings:', error);
                alert('Failed to load calendar settings. Please try again later.');
            });
    }
});

/**
 * Fetch calendar settings from the server.
 * @returns {Promise<Object>}
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
        }));
}

/**
 * Calculate the earliest start time and latest end time from business hours.
 * @param {Array} businessHours - Formatted business hours.
 * @returns {Object} Object containing `slotMinTime` and `slotMaxTime`.
 */

function calculateSlotTimes(businessHours) {
    let slotMinTime = '23:59';
    let slotMaxTime = '00:00';

    businessHours.forEach(hour => {
        if (hour.startTime < slotMinTime) {
            slotMinTime = hour.startTime;
        }
        if (hour.endTime > slotMaxTime) {
            slotMaxTime = hour.endTime;
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
                blockedEvents.push({
                    title: data.blockedTimes.block_title[index],
                    daysOfWeek: [0, 1, 2, 3, 4, 5, 6],
                    startTime: data.blockedTimes.from[index],
                    endTime: data.blockedTimes.to[index],
                    backgroundColor: 'red',
                    borderColor: 'red',
                    display: 'background',
                });
            } else {
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

    if (data.blockedSpecificDateTimes.date) {
        data.blockedSpecificDateTimes.date.forEach((date, index) => {
            const title = data.blockedSpecificDateTimes.block_title[index] || 'Blocked';
            const from = data.blockedSpecificDateTimes.from[index];
            const to = data.blockedSpecificDateTimes.to[index];

            if (date && from && to) {
                const formatTo12Hour = (timeStr) => {
                if (!timeStr) return '';
                const [hourStr, minuteStr] = timeStr.split(':');
                let hour = parseInt(hourStr, 10);
                const minute = minuteStr.padStart(2, '0');
                const ampm = hour >= 12 ? 'PM' : 'AM';
                hour = hour % 12 || 12;
                return `${hour}:${minute} ${ampm}`;
            };
            const formattedFrom = formatTo12Hour(from);
            const formattedTo = formatTo12Hour(to);
            const displayTitle = `${title} (${formattedFrom} - ${formattedTo})`;
                blockedEvents.push({
                    title: displayTitle,
                    start: `${date}T${from}`,
                    end: `${date}T${to}`,
                    backgroundColor: 'rgba(255, 0, 0, 0.3)',
                    borderColor: 'rgba(255, 0, 0, 0.4)',
                    display: 'background',
                    allDay: false,
                });
            }
        });
    }

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
            const fittingType = block.jobtype;
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

    if (data.blockFittingTypeDateTime && Array.isArray(data.blockFittingTypeDateTime)) {
        data.blockFittingTypeDateTime.forEach(block => {
            if (!block.date || !block.from || !block.to) {
                console.warn('Invalid blockFittingTypeDateTime entry:', block);
                return;
            }
            const startTime = block.from;
            const endTime = block.to;
            const jobType = block.jobtype || null;
            const title = block.block_title || "Job Blocked";

            blockedEvents.push({
                title: `${title}`,
                start: `${block.date}T${startTime}`,
                end: `${block.date}T${endTime}`,
                backgroundColor: "rgba(255, 0, 0, 0.3)",
                borderColor: "rgba(255, 0, 0, 0.4)",
                display: "background",
                extendedProps: {
                    type: "job",
                    jobType: jobType,
                },
            });
        });
    }

    if (data.blockServicePerhours) {
        const daysMapping = {
            Sunday: 0,
            Monday: 1,
            Tuesday: 2,
            Wednesday: 3,
            Thursday: 4,
            Friday: 5,
            Saturday: 6,
        };
        data.blockServicePerhours.forEach(block => {
            const dayNumber = block.days === 'all' ? [0, 1, 2, 3, 4, 5, 6] : [daysMapping[block.days]];
            if (dayNumber !== undefined) {
                blockedEvents.push({
                    title: block.block_title,
                    daysOfWeek: dayNumber,
                    startTime: block.from,
                    endTime: block.to,
                    backgroundColor: 'rgba(255, 0, 0, 0.29)',
                    borderColor: 'rgba(255, 0, 0, 0.36)',
                    display: 'background',
                });
            } else {
                console.error(`Invalid day name: ${block.days}`);
            }
        });
    }

    if (data.blockServiceDateTime && Array.isArray(data.blockServiceDateTime)) {
        data.blockServiceDateTime.forEach(block => {
            if (!block.date || !block.from || !block.to) {
                console.log('Invalid blockServiceDateTime entry:', block);
                return;
            }
            blockedEvents.push({
                title: block.block_title || 'Blocked',
                start: `${block.date}T${block.from}`,
                end: `${block.date}T${block.to}`,
                backgroundColor: 'rgba(255, 0, 0, 0.3)',
                borderColor: 'rgba(255, 0, 0, 0.3)',
                display: 'background',
                allDay: false,
                service_type: block.service_type || null,
                job_type: block.job_type || null,
            });
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
    if (data.motBookedSlots) {
        data.motBookedSlots.forEach(slot => {
            blockedEvents.push({
                title: 'MOT Fully Booked',
                start: `${slot.date}T${slot.start}`,
                end: `${slot.date}T${slot.end}`,
                backgroundColor: 'rgba(241, 183, 101, 0.92)',
                borderColor: 'rgba(255, 136, 0, 0.6)',
                display: 'background',
                extendedProps: {
                    serviceType: 'mot'
                }
            });
        });
    }

    return blockedEvents;
}


function timeStringToMinutes(t) {
    if (!t) return null;
    const parts = t.split(':').map(Number);
    return parts[0] * 60 + (parts[1] || 0);
}
/**
 * Check if the selected slot overlaps with any blocked events.
 * @param {Date} start - The start date and time of the selected slot.
 * @param {Date} end - The end date and time of the selected slot.
 * @param {Array} blockedEvents - Array of blocked events.
 * @returns {boolean} True if the slot is blocked, otherwise false.
 */

function isSlotBlocked(selectedStart, selectedEnd, blockedSlots, currentJobType = null, currentServiceType = null) {
    const selectedDay = selectedStart.getDay();
    const selectedStartMinutes = selectedStart.getHours() * 60 + selectedStart.getMinutes();
    const selectedEndMinutes = selectedEnd.getHours() * 60 + selectedEnd.getMinutes();
    for (let slot of blockedSlots) {
        if (slot.job_type && currentJobType && slot.job_type !== currentJobType) continue;
        if (slot.service_type && currentServiceType && slot.service_type !== currentServiceType) continue;
        if (slot.extendedProps && slot.extendedProps.serviceType === 'mot' && currentServiceType !== 'mot') {
            continue;
        }

        if (slot.start && slot.end) {
            const blockedStart = new Date(slot.start);
            const blockedEnd = new Date(slot.end);

            if (isNaN(blockedStart) || isNaN(blockedEnd)) {
                continue;
            }

            if (
                (selectedStart >= blockedStart && selectedStart < blockedEnd) ||
                (selectedEnd > blockedStart && selectedEnd <= blockedEnd) ||
                (selectedStart <= blockedStart && selectedEnd >= blockedEnd)
            ) {
                return true;
            }
        }

        if (Array.isArray(slot.daysOfWeek) && (slot.startTime || slot.start) && (slot.endTime || slot.end)) {
            if (!slot.daysOfWeek.includes(selectedDay)) {
                continue;
            }

            let blockStartMinutes = null;
            let blockEndMinutes = null;

            if (slot.startTime && slot.endTime) {
                blockStartMinutes = timeStringToMinutes(slot.startTime);
                blockEndMinutes = timeStringToMinutes(slot.endTime);
            } else if (slot.start && slot.end) {
                const s = new Date(slot.start);
                const e = new Date(slot.end);
                if (!isNaN(s) && !isNaN(e)) {
                    blockStartMinutes = s.getHours() * 60 + s.getMinutes();
                    blockEndMinutes = e.getHours() * 60 + e.getMinutes();
                }
            }

            if (blockStartMinutes !== null && blockEndMinutes !== null) {
                if (blockEndMinutes <= blockStartMinutes) {
                    if (
                        (selectedStartMinutes >= blockStartMinutes && selectedStartMinutes <= 24 * 60 - 1) ||
                        (selectedEndMinutes >= 0 && selectedEndMinutes <= blockEndMinutes)
                    ) {
                        return true;
                    }
                } else {
                    if (
                        (selectedStartMinutes >= blockStartMinutes && selectedStartMinutes < blockEndMinutes) ||
                        (selectedEndMinutes > blockStartMinutes && selectedEndMinutes <= blockEndMinutes) ||
                        (selectedStartMinutes <= blockStartMinutes && selectedEndMinutes >= blockEndMinutes)
                    ) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
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
                    backgroundColor: 'rgba(255, 0, 0, 0.2)',
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
                    backgroundColor: 'rgba(255, 0, 0, 0.2)',
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
                backgroundColor: 'rgba(255, 0, 0, 0.2)',
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
function initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration, validRangeStart, validRangeEnd) {
    let selectedEvent = null;

    const now = getLondonDate();
    const today = now;
    const currentHour = now.getHours();
    const currentDayIndex = now.getDay();

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        timeZone: 'local',
        initialDate: today,
        firstDay: currentDayIndex,
        validRange: { start: today },
        visibleRange: { start: today, end: formattedEnd },
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
        events: [...calculateBlockedTimes(businessHours), ...blockedEvents, {
            start: new Date(),
            end: new Date(validRangeStart),
            display: 'background',
            backgroundColor: '#f8d7da',
            overlap: false
        }
        ],
        displayEventTime: false,

        dayHeaderContent: function (arg) {
            const date = new Date(arg.date);
            return date.toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: '2-digit' }).replace(',', '');
        },

        selectAllow: function (info) {
            return info.start >= new Date(validRangeStart);
        },
        select: function (info) {
            const start = info.start;
            const startTime = info.startStr;
            const endTime = info.endStr;
            const end = new Date(start.getTime() + slotDuration * 60000);

            const currentJobType = document.getElementById('job_type_select')?.value || null;
            const currentServiceType = document.getElementById('service_type_select')?.value || null;

            if (isSlotBlocked(start, end, blockedEvents, currentJobType, currentServiceType)) {
                alert('This slot is blocked for the selected job or service type.');
                return;
            }
            if (start < new Date()) {
                alert('You cannot select a past slot.');
                return;
            }

            if (selectedEvent) selectedEvent.remove();
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
    return DateTime.fromISO(isoString, { zone: 'local' }).toFormat('dd LLL yyyy, hh:mm a');
}

function formatTimeFromISO(isoString) {
    return DateTime.fromISO(isoString, { zone: 'local' }).toFormat('hh:mm a');
}

/**
 * Save the selected slot in the session via AJAX.
 * @param {Date} start - The start date and time of the selected slot.
 * @param {Date} end - The end date and time of the selected slot.
 */
function saveSelectedSlot(start, end) {
   const formatLocalDate = (dateObj) =>
        `${dateObj.getFullYear()}-` +
        `${String(dateObj.getMonth() + 1).padStart(2, '0')}-` +
        `${String(dateObj.getDate()).padStart(2, '0')} ` +
        `${String(dateObj.getHours()).padStart(2, '0')}:` +
        `${String(dateObj.getMinutes()).padStart(2, '0')}:00`;

    const startLocal = formatLocalDate(start);
    const endLocal = formatLocalDate(end);

    fetch('/save-selected-slot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
