require('./bootstrap');
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import { DateTime } from 'luxon';
import interactionPlugin from '@fullcalendar/interaction';
import { Modal } from 'bootstrap';
import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';

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

document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
        base_url: '/js/tinymce',
        selector: '#email_body',
        license_key: 'gpl',
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
        valid_elements: '*[*]',
        valid_children: '+body[style]',
    });
});

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

async function fetchCalendarSettings() {
    const response = await fetch('/calendar-settings');
    if (!response.ok) {
        throw new Error('Failed to fetch calendar settings');
    }
    return response.json();
}

function formatBusinessHours(businessHours) {
    return businessHours
        .filter(hour => !hour.closed)
        .map(hour => ({
            daysOfWeek: hour.daysOfWeek,
            startTime: hour.startTime,
            endTime: hour.endTime,
        }));
}

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

function createBlockedEvents(data) {
    const blockedEvents = [];
    if (data.blockedTimes && data.blockedTimes.days && data.blockedTimes.apply_on_dashboard === "1") {
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
                    extendedProps: { type: 'blocked' },
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
                        extendedProps: { type: 'blocked' },
                    });
                } else {
                    console.error(`Invalid day name: ${day}`);
                }
            }
        });
    }

    if (data.holidays && data.holidays.holidayDate && data.holidays.apply_on_dashboard === "1") {
    data.holidays.holidayDate.forEach((date, index) => {
        const nextDay = new Date(date);
        nextDay.setDate(nextDay.getDate() + 1);

        blockedEvents.push({
            title: data.holidays.holiday_name[index],
            start: date,
            end: nextDay.toISOString().split('T')[0],
            backgroundColor: '#ff0000',
            borderColor: '#ff0000',
            allDay: true,
            display: 'background',
            extendedProps: { type: 'blocked' },
        });
    });
    }
    
    if (data.blockedSpecificDateTimes.date && data.holidays.apply_on_dashboard === "1") {
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


    return blockedEvents;
}

function timeStringToMinutes(t) {
    if (!t) return null;
    const parts = t.split(':').map(Number);
    return parts[0] * 60 + (parts[1] || 0);
}

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
        if (slot.display === 'background' && !slot.start && slot.daysOfWeek) {
            const day = selectedStart.getDay();
            if (slot.daysOfWeek.includes(day)) {
                const blockStart = timeStringToMinutes(slot.startTime);
                const blockEnd = timeStringToMinutes(slot.endTime);
                const selStart = selectedStart.getHours() * 60 + selectedStart.getMinutes();
                const selEnd = selectedEnd.getHours() * 60 + selectedEnd.getMinutes();
                if (selStart < blockEnd && selEnd > blockStart) {
                    return true;
                }
            }
        }

        // Handle all-day holiday blocks
        if (slot.allDay && slot.start && !slot.end) {
            const blockedDate = new Date(slot.start).toDateString();
            if (selectedStart.toDateString() === blockedDate) {
                return true;
            }
        }

        // at top of isSlotBlocked loop
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

function calculateBlockedTimes(businessHours) {
    const blockedTimes = [];

    const earliestTime = '00:00';
    const latestTime = '23:59';

    for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
        const dayHours = businessHours.find(hour => hour.daysOfWeek.includes(dayOfWeek));

        if (dayHours) {
            const { startTime, endTime } = dayHours;

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

function openBookingForm(start, end, blockedEvents, slotDuration) {
    const now = getLondonDate();
    if (start < now) {
        alert('You cannot create a new booking for a past time.');
        return;
    }

    if (isSlotBlocked(start, end, blockedEvents)) {
        alert('This slot is blocked and cannot be booked.');
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('bookingFormModal'));
    const startLocal = toLocalDateTimeString(start);
    const endTime = new Date(start.getTime() + slotDuration * 60000);
    const endLocal = toLocalDateTimeString(endTime);
    console.log(start);
    document.getElementById('due_in').value = startLocal;
    document.getElementById('due_out').value = endLocal;

    modal.show();
}


function toLocalDateTimeString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function initializeCalendar(calendarEl, businessHours, blockedEvents, slotMinTime, slotMaxTime, slotDuration, validRangeStart, validRangeEnd) {
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        timeZone: 'local',
        selectable: true,
        selectMirror: true,
        editable: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
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
        events: blockedEvents,
        slotMinTime: slotMinTime,
        slotMaxTime: slotMaxTime,
        slotDuration: `00:${slotDuration}:00`,

        eventSources: [
            {
                url: '/get-events',
                method: 'GET',
                failure: () => {
                    alert('Error loading events');
                },
            },
        ],

        eventClick: function (info) {
            if (info.event.display === 'background') {
                info.jsEvent.preventDefault();
                return false;
            }
            showBookingDetails(info.event);
        },

        selectOverlap: function (event) {
            return event.display !== 'background';
        },

        selectAllow: function (selectInfo) {
            const allEvents = calendar.getEvents();
            return !isSlotBlocked(selectInfo.start, selectInfo.end, allEvents);
        },
        eventDidMount: function (info) {
            if (info.event.display === 'background') return;

            const eventType = info.event.extendedProps.type;
            const titleEl = info.el.querySelector('.fc-event-title');

            if (eventType !== 'blocked' || eventType !== 'holiday') {
                if (titleEl) {
                    titleEl.innerHTML = info.event.title;
                }
            }
            if (eventType === 'blocked' || eventType === 'holiday') {
                if (titleEl) {
                    titleEl.innerHTML = `<i class="fa fa-ban text-danger"></i> ${info.event.title || ''}`;
                }
            }
        },
        dateClick: function (info) {
            const allEvents = calendar.getEvents();
            const end = new Date(info.date.getTime() + slotDuration * 60000);
            if (isSlotBlocked(info.date, end, allEvents)) {
                alert('This slot is blocked and cannot be booked.');
                return;
            }
            openBookingForm(info.date, end, allEvents, slotDuration);
        },

        select: function (info) {
            const allEvents = calendar.getEvents();
            if (isSlotBlocked(info.start, info.end, allEvents)) {
                alert('This slot is blocked and cannot be booked.');
                return;
            }
            openBookingForm(info.start, info.end, allEvents, slotDuration);
        },
    });

    calendar.render();
}


function showBookingDetails(event) {
    const bookingId = event.id;
    fetch(`/get-booking-details/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            populateModal(data);
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching booking details:', error);
            alert('Failed to load booking details.');
        });
}

function populateModal(bookingData) {
    const { booking, workshop, customer, vehicle } = bookingData;

    const bookingInfoContainer = document.getElementById("booking_info");
    bookingInfoContainer.innerHTML = '';

    const heading = document.createElement('h6');
    heading.textContent = "Vehicle Information";
    bookingInfoContainer.appendChild(heading);

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
                    <td><span class="calendar_reg text-uppercase">${vehicle.vehicle_reg_number || 'N/A'}</span></td>
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
    bookingInfoContainer.appendChild(vehicleTable);

    document.getElementById("customerName").textContent = customer.customer_name || 'N/A';
    document.getElementById("customerPhone").textContent = customer.customer_contact_number || 'N/A';
    document.getElementById("customerEmail").textContent = customer.customer_email || 'N/A';
    document.getElementById("customerAddress").textContent = customer.customer_address || 'N/A';

    const startTime = DateTime.fromISO(booking.start).setZone('Europe/London');
    const endTime = DateTime.fromISO(booking.end).setZone('Europe/London');

    document.getElementById("bookingGarageName").textContent = booking.garage_name || 'N/A';
     document.getElementById("bookingStartDate").textContent = startTime.toFormat('dd MMM yyyy');
    document.getElementById("bookingEndDate").textContent = endTime.toFormat('dd MMM yyyy');
    document.getElementById("bookingStartTime").textContent = startTime.toFormat('hh:mm a');
    document.getElementById("bookingEndTime").textContent = endTime.toFormat('hh:mm a');

    document.getElementById("jobid").textContent = 'JOB ID: ' + (workshop.id || 'N/A');
    document.getElementById("workshopName").textContent = workshop.name || 'N/A';
    document.getElementById("workshopDueIn").textContent = workshop.due_in || 'N/A';
    document.getElementById("workshopDueOut").textContent = workshop.due_out || 'N/A';
    document.getElementById("workshopGrandTotal").textContent = '£' + workshop.grandTotal || 'N/A';
    document.getElementById("workshopPaymentMethod").textContent = workshop.payment_method || 'N/A';
    document.getElementById("workshopStatus").textContent = workshop.status || 'N/A';

    const itemsTableBody = document.getElementById('workshopItems');
    itemsTableBody.innerHTML = '';
    let index = 1;

    let subtotal = 0;
    let totalVAT = 0;
    let shippingPrice = 0;
    let shippingVAT = 0;
    let hasMobileFitting = false;
    let shippingPostcode = null;

    if (workshop.items && workshop.items.length > 0) {
        workshop.items.forEach((item, i) => {
            let quantity = item.quantity || 1;
            let rate = parseFloat(item.margin_rate) || 0;
            let vatPercentage = item.tax_class_id === 9 ? 20 : (parseFloat(item.vat) || 0);
            let vatAmount = (rate * vatPercentage) / 100;
            let total = (rate + vatAmount) * quantity;
            shippingPostcode = item.shipping_postcode;

            let fittingType = item.fitting_type
                ?.replace(/_/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');

            subtotal += rate * quantity;
            totalVAT += vatAmount * quantity;

            if (item.fitting_type === 'mobile_fitted') {
                hasMobileFitting = true;
                shippingPostcode = item.shipping_postcode;
                if (shippingPrice === 0) {
                    shippingPrice = parseFloat(item.shipping_price) || 0;
                    if (item.shipping_tax_id === 9) {
                        shippingVAT = shippingPrice * 0.2;
                    }
                }
            }
            const row = `
                <tr>
                    <td>${index++}</td>
                    <td>
                        <strong>${item.model || item.description}</strong>
                        <span class="d-block">${item.description || 'N/A'}</br>${item.product_ean} (${item.supplier})</span>
                    </td>
                    <td class="text-center">${quantity}</td>
                    <td class="text-right">£${rate.toFixed(2)}</td>
                    <td class="text-right">${vatPercentage}%</td>
                    <td class="text-right">£${total.toFixed(2)}</td>
                </tr>`;

            itemsTableBody.innerHTML += row;
            document.getElementById("fittingType").textContent = fittingType || 'N/A';
        });
    }

    if (workshop.services && workshop.services.length > 0) {
        workshop.services.forEach((service) => {
            let rate = parseFloat(service.service_price) || 0;
            let vatPercentage = service.tax_class_id === 9 ? 20 : (parseFloat(service.vat) || 0);
            let vatAmount = (rate * vatPercentage) / 100;
            let total = rate + vatAmount;

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

    if (hasMobileFitting && shippingPrice > 0) {
        const shippingTotal = shippingPrice + shippingVAT;

        const calloutRow = `
            <tr>
                <td></td>
                <td>
                    <strong>Mobile Fitting Callout Charge (${shippingPostcode || 'N/A'}) </strong>
                    <span class="d-block">Applied to Mobile Fitted service</span>
                </td>
                <td class="text-center">1</td>
                <td class="text-right">£${shippingPrice.toFixed(2)}</td>
                <td class="text-right">20%</td>
                <td class="text-right">£${shippingTotal.toFixed(2)}</td>
            </tr>`;

        itemsTableBody.innerHTML += calloutRow;
        totalVAT += shippingVAT;
    }

    const grandTotal = subtotal + totalVAT + shippingPrice;

    const shippingChargesElement = document.getElementById("calloutcharge");
    const shippingChargestrElement = document.getElementById("calloutchargetr");

    if (hasMobileFitting && shippingPrice > 0) {
        shippingChargesElement.textContent = `Callout Charges: £${shippingPrice.toFixed(2)}`;
        shippingChargesElement.style.display = "block";
        if (shippingChargestrElement) {
            shippingChargestrElement.style.display = "table-row";
        }
    } else {
        shippingChargesElement.textContent = "";
        shippingChargesElement.style.display = "none";
        if (shippingChargestrElement) {
            shippingChargestrElement.style.display = "none";
        }
    }

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

    if (document.getElementById("editBookingBtn")) {
        document.getElementById("editBookingBtn").href = `/AutoCare/workshop/add/${workshop.id}`;
    }

    const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
    modal.show();
}