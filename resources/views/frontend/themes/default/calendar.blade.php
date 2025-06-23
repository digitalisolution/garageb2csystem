<style>
    /* Optional: Add custom styling for modal */
    #bookingModal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    #calendar {
        touch-action: manipulation;
        /* Prevent zooming or panning on touch */
    }

    /* Highlight day headers with different colors */
    .fc-day-mon .fc-scrollgrid-sync-inner {
        background-color: #ffdddd !important;
    }

    /* Light Red */
    .fc-day-tue .fc-scrollgrid-sync-inner {
        background-color: #ddffdd !important;
    }

    /* Light Green */
    .fc-day-wed .fc-scrollgrid-sync-inner {
        background-color: #ddddff !important;
    }

    /* Light Blue */
    .fc-day-thu .fc-scrollgrid-sync-inner {
        background-color: #ffffcc !important;
    }

    /* Light Yellow */
    .fc-day-fri .fc-scrollgrid-sync-inner {
        background-color: #ffccff !important;
    }

    /* Light Pink */
    .fc-day-sat .fc-scrollgrid-sync-inner {
        background-color: #ccffff !important;
    }

    /* Light Cyan */
    .fc-day-sun .fc-scrollgrid-sync-inner {
        background-color: #ffcc99 !important;
    }

    /* Light Orange */

    /* Ensure text contrast */
    .fc-col-header-cell-cushion {
        font-weight: bold;
        color: black !important;
    }

    .fc-v-event .fc-event-title {
        line-height: normal;
        text-align: center;
    }
    @media screen and (max-width:600px){
        .fc table {font-size: 0.85em;line-height: 1.7em;}
        .fc-col-header-cell-cushion{font-weight:600;}
        .fc .fc-col-header-cell-cushion{padding:2px 2px;}
    }
</style>
<h3 class="cart-page-title">Booking Calendar</h3>
<div id="calendar"></div>
{{-- Check if there's session data and display the booking slot --}}
{{-- Check if there's session data and display the booking slot --}}
@if (!empty(session('bookingDetails')))
    @php
        $start = \Carbon\Carbon::parse(session('bookingDetails.start'));
        $end = \Carbon\Carbon::parse(session('bookingDetails.end'));
    @endphp
    <div id="selectedSlot" class="booked_slot">
        Selected Slot: {{ $start->format('jS M Y, h:i A') }} - {{ $end->format('h:i A') }}
    </div>
@else
    <div id="selectedSlot" class="booked_slot">No booking details available.</div>
@endif
<input type="hidden" id="selected_slot_details" name="selected_slot_details">