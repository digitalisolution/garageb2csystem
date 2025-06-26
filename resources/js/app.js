require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import { DateTime } from 'luxon';
import interactionPlugin from '@fullcalendar/interaction';
import { Modal } from 'bootstrap'; // Import Bootstrap Modal
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver'; // Required theme

// Import plugins
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

// Initialize TinyMCE
document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
        base_url: '/js/tinymce', // Path to your local TinyMCE assets
        selector: '#email_body',
        license_key: 'gpl',
        // setup: function (editor) {
        //     console.log('TinyMCE initialized:', editor);
        // },
        height: 400,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: [
            'undo redo | formatselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify',
            'bullist numlist outdent indent | link image | restoredraft | help'
        ],
        font_formats: `
            Arial=arial,helvetica,sans-serif;
            Courier New=courier new,courier,monospace;
            Georgia=georgia,palatino,serif;
            Times New Roman=times new roman,times,serif;
            Verdana=verdana,geneva,sans-serif;
        `,
        fontsize_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        content_style: `
            body {
                font-family: Verdana, sans-serif;
                font-size: 12pt;
            }
        `,
        valid_elements: '*[*]', // Allows all elements but ensures proper sanitization
        valid_children: '+body[style]', // Ensures proper nesting of elements
    });
});



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

/**
 * Open the booking form modal and pre-fill the date and time.
 * @param {Date} start - The start date and time of the selected slot.
 * @param {Date} end - The end date and time of the selected slot.
 * @param {Array} blockedEvents - Array of blocked events.
 * @param {number} slotDuration
 */
function openBookingForm(start, end, blockedEvents, slotDuration) {
    // Check if the selected slot is blocked
    if (isSlotBlocked(start, end, blockedEvents,slotDuration)) {
        alert('This slot is blocked and cannot be booked.');
        return; // Exit the function if the slot is blocked
    }

    const modal = new bootstrap.Modal(document.getElementById('bookingFormModal'));

    // Convert the selected start time to local datetime format
    const startLocal = toLocalDateTimeString(start);

    // Calculate the end time by adding the slot duration to the start time
    const endTime = new Date(start.getTime() + slotDuration * 60000); // Convert slotDuration from minutes to milliseconds
    const endLocal = toLocalDateTimeString(endTime);
    // Set values for Due In and Due Out fields
    document.getElementById('due_in').value = startLocal;
    document.getElementById('due_out').value = endLocal;

    // Show the modal
    modal.show();
}


/**
 * Convert a Date object to a local date-time string compatible with datetime-local input.
 * @param {Date} date - The date to format.
 * @returns {string} The formatted date string in local time.
 */
function toLocalDateTimeString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
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
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth', // Use timeGridWeek for better visibility of hours
        timeZone: 'Europe/London',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        businessHours: businessHours, // Dynamic working hours
        events: blockedEvents, // Blocked times and holidays
        slotMinTime: slotMinTime, // Set the minimum time dynamically
        slotMaxTime: slotMaxTime, // Set the maximum time dynamically
        slotDuration: `00:${slotDuration}:00`, // Set slot duration dynamically
        eventSources: [
            {
                url: '/get-events', // Fetch bookings dynamically
                method: 'GET',
                failure: () => {
                    alert('Error loading events');
                },
            },
        ],
        eventClick: function (info) {
            // Handle event click
            showBookingDetails(info.event);
        },
        dateClick: function (info) {
            // Handle click on empty slots
            openBookingForm(info.date, info.date, blockedEvents,slotDuration); // Pass blockedEvents
        },
        selectable: true, // Enable selection of slots
        select: function (info) {
            // Handle selection of slots
            openBookingForm(info.start, info.end, blockedEvents,slotDuration); // Pass blockedEvents
        },
    });

    calendar.render();
}


/**
 * Show booking details in a modal.
 * @param {Object} event - The clicked event object.
 */
function showBookingDetails(event) {
    const bookingId = event.id; // Assuming the event has an ID
    fetch(`/get-booking-details/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal with the fetched data
            populateModal(data);
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching booking details:', error);
            alert('Failed to load booking details.');
        });
}

/**
 * Populate the modal with booking, workshop, and item details.
 * @param {Object} booking - Booking details.
 */
function populateModal(bookingData) {
    const { booking, workshop, customer, vehicle } = bookingData;

    // Dynamically generate Booking Information
    const bookingInfoContainer = document.getElementById("booking_info");
    bookingInfoContainer.innerHTML = ''; // Clear previous content

    // Create a wrapper div for the booking information
    const bookingInfoWrapper = document.createElement('div');
    bookingInfoWrapper.className = "bg-white border rounded text-uppercase";

    // Add the heading
    const heading = document.createElement('h6');
    heading.textContent = "Vehicle Information";
    bookingInfoContainer.appendChild(heading);

    // New Table Structure for Vehicle Details
    const vehicleTable = document.createElement('div');
    vehicleTable.className = "table-responsive";
    vehicleTable.innerHTML = `
        <table class="table table-sm table-bordered mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>VRM</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Front Tyre Year</th>
                    <th>Rear Tyre</th>
                    <th>VIN</th>
                    <th>CC</th>
                    <th>Engine</th>
                    <th>Axle</th>
                    <th>Fuel Type</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="text-uppercase">${vehicle.vehicle_reg_number || 'N/A'}</span></td>
                    <td>${vehicle.vehicle_make || 'N/A'}</td>
                    <td>${vehicle.vehicle_model || 'N/A'}</td>
                    <td>${vehicle.vehicle_year || 'N/A'}</td>
                    <td>${vehicle.vehicle_front_tyre_size || 'N/A'}</td>
                    <td>${vehicle.vehicle_rear_tyre_size || 'N/A'}</td>
                    <td>${vehicle.vehicle_vin || 'N/A'}</td>
                    <td>${vehicle.vehicle_cc || 'N/A'}</td>
                    <td>${vehicle.vehicle_engine_number || 'N/A'}</td>
                    <td>${vehicle.vehicle_axle || 'N/A'}</td>
                    <td>${vehicle.vehicle_fuel_type || 'N/A'}</td>
                </tr>
            </tbody>
        </table>
    `;
    bookingInfoWrapper.appendChild(vehicleTable);

    // Append the wrapper to the container
    bookingInfoContainer.appendChild(bookingInfoWrapper);

    // Populate Customer Details
    document.getElementById("customerName").textContent = customer.customer_name || 'N/A';
    document.getElementById("customerPhone").textContent = customer.customer_contact_number || 'N/A';
    document.getElementById("customerEmail").textContent = customer.customer_email || 'N/A';
    document.getElementById("customerAddress").textContent = customer.customer_address || 'N/A';

    const startTime = DateTime.fromISO(booking.start).setZone('Europe/London');
    const endTime = DateTime.fromISO(booking.end).setZone('Europe/London');

    document.getElementById("bookingStartDate").textContent = startTime.toFormat('dd MMM yyyy');
    document.getElementById("bookingEndDate").textContent = endTime.toFormat('dd MMM yyyy');
    document.getElementById("bookingStartTime").textContent = startTime.toFormat('hh:mm a');
    document.getElementById("bookingEndTime").textContent = endTime.toFormat('hh:mm a');

    // Populate Workshop Information
    document.getElementById("jobid").textContent = 'JOB ID: ' + (workshop.id || 'N/A');
    document.getElementById("workshopName").textContent = workshop.name || 'N/A';
    document.getElementById("workshopDueIn").textContent = workshop.due_in || 'N/A';
    document.getElementById("workshopDueOut").textContent = workshop.due_out || 'N/A';
    document.getElementById("workshopGrandTotal").textContent = '£' + workshop.grandTotal || 'N/A';
    document.getElementById("workshopPaymentMethod").textContent = workshop.payment_method || 'N/A';
    document.getElementById("workshopStatus").textContent = workshop.status || 'N/A';

    // Populate Workshop Items and Services in Table
    const itemsTableBody = document.getElementById('workshopItems');
    itemsTableBody.innerHTML = ''; // Clear previous items
    let index = 1;

    // Initialize totals
    let subtotal = 0;
    let totalVAT = 0;
    let shippingPrice = 0; // Single shipping price
    let shippingVAT = 0; // VAT for shipping
    let hasMobileFitting = false;

    if (workshop.items && workshop.items.length > 0) {
        workshop.items.forEach((item) => {
            let quantity = item.quantity || 1;
            let rate = parseFloat(item.margin_rate) || 0;
            let vatPercentage = item.tax_class_id === 9 ? 20 : (parseFloat(item.vat) || 0);
            let vatAmount = (rate * vatPercentage) / 100;
            let total = (rate + vatAmount) * quantity;
           let fittingType = item.fitting_type.replace(/_/g, ' ') // Replace underscores with spaces
    .split(' ') // Split into words
    .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize each word
    .join(' ');
            // Add to totals
            subtotal += rate * quantity;
            totalVAT += vatAmount * quantity;

            // Check if the item has fitting_type as mobile_fitted
            if (item.fitting_type === 'mobile_fitted') {
                hasMobileFitting = true;
                // Add the shipping price once (single value)
                shippingPrice = parseFloat(item.shipping_price) || 0;
                // Calculate shipping VAT if shipping_tax_id is 9
                if (item.shipping_tax_id === 9) {
                    shippingVAT = shippingPrice * 0.2; // 20% VAT for shipping
                }
            }

            const row = `
                <tr>
                    <td>${index++}</td>
                    <td>
                        <strong>${item.model || item.description}</strong>
                        <span class="d-block">${item.description || 'N/A'}</span>
                    </td>
                    <td class="text-center">${quantity}</td>
                    <td class="text-right">£${rate.toFixed(2)}</td>
                    <td class="text-right">${vatPercentage}%</td>
                    <td class="text-right">£${total.toFixed(2)}</td>
                </tr>
            `;
             document.getElementById("fittingType").textContent = fittingType || 'N/A';
            itemsTableBody.innerHTML += row;
        });
    }

    if (workshop.services && workshop.services.length > 0) {
        workshop.services.forEach((service) => {
            let rate = parseFloat(service.service_price) || 0;
            let vatPercentage = service.tax_class_id === 9 ? 20 : (parseFloat(service.vat) || 0);
            let vatAmount = (rate * vatPercentage) / 100;
            let total = rate + vatAmount;

            // Add to totals
            subtotal += rate;
            totalVAT += vatAmount;

            const row = `
                <tr>
                    <td>${index++}</td>
                    <td>
                        <strong>${service.service_name || 'N/A'}</strong>
                        <span class="d-block">Service</span>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-right">£${rate.toFixed(2)}</td>
                    <td class="text-right">${vatPercentage}%</td>
                    <td class="text-right">£${total.toFixed(2)}</td>
                </tr>
            `;
            itemsTableBody.innerHTML += row;
        });
    }

    totalVAT += shippingVAT; // Add shipping VAT to total VAT

    // Calculate grand total
    const grandTotal = subtotal + totalVAT + shippingPrice;

    // Display shipping charges separately above the subtotal
    const shippingChargesElement = document.getElementById("calloutcharge");
    const shippingChargestrElement = document.getElementById("calloutchargetr");
    if (hasMobileFitting) {
        shippingChargesElement.textContent = `Callout Charges: £${shippingPrice.toFixed(2)}`;
        shippingChargesElement.style.display = "block"; // Show the shipping charges
    } else {
        shippingChargesElement.textContent = "";
        shippingChargesElement.style.display = "none"; // Hide the shipping charges
        shippingChargestrElement.style.display = "none";
    }

    // Display totals in the modal
    document.getElementById("subtotal").textContent = 'Sub-Total: £' + subtotal.toFixed(2);
    document.getElementById("totalvat").textContent = 'VAT (20%): £' + totalVAT.toFixed(2);
    document.getElementById("grandtotal").textContent = 'Grand Total: £' + grandTotal.toFixed(2);

    if (itemsTableBody.innerHTML === '') {
        itemsTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">No items or services found.</td>
            </tr>
        `;
    }

    // ✅ Set the dynamic edit button link
    if (document.getElementById("editBookingBtn")) {
        document.getElementById("editBookingBtn").href = `/AutoCare/workshop/add/${workshop.id}`;
    }

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
    modal.show();
}




