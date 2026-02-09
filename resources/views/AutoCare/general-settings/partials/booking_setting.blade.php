<h5 class="mb-3">Calendar Status Colors</h5>
<p>Set colors for different booking statuses:</p>

<form action="{{ route('AutoCare.booking.update') }}" method="POST">
    @csrf
    @method('POST')

    <div class="row">

    <!-- Confirmed Status Color -->
    <div class="col-3 mb-3">
        <label for="calendar_booking_color_completed" class="form-label">Completed Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_completed" name="calendar_booking_color_completed" value="{{ get_option('calendar_booking_color_completed', '#00ff00') }}">
    </div>

    <!-- Pending Status Color -->
    <div class="col-3 mb-3">
        <label for="calendar_booking_color_pending" class="form-label">Pending Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_pending" name="calendar_booking_color_pending" value="{{ get_option('calendar_booking_color_pending', '#ffcc00') }}">
    </div>

    <!-- Booked Status Color -->
    <div class="col-3 mb-3">
        <label for="calendar_booking_color_booked" class="form-label">Booked Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_booked" name="calendar_booking_color_booked" value="{{ get_option('calendar_booking_color_booked', '#2ed943') }}">
    </div>
 <!-- Booked Status Color -->
 <div class="col-3 mb-3">
        <label for="calendar_booking_color_canceled" class="form-label">Canceled Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_canceled" name="calendar_booking_color_canceled" value="{{ get_option('calendar_booking_color_canceled', '#2ed943') }}">
    </div>
     <!-- Booked Status Color -->
     <div class="col-3 mb-3">
        <label for="calendar_booking_color_failed" class="form-label">Failed Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_failed" name="calendar_booking_color_failed" value="{{ get_option('calendar_booking_color_failed', '#2ed943') }}">
    </div>
     <!-- Booked Status Color -->
     <div class="col-3 mb-3">
        <label for="calendar_booking_color_awaiting" class="form-label">Awaiting Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_awaiting" name="calendar_booking_color_awaiting" value="{{ get_option('calendar_booking_color_awaiting', '#2ed943') }}">
    </div>
    <!-- Default Status Color -->
    <div class="col-3 mb-3">
        <label for="calendar_booking_color_default" class="form-label">Default Status Color</label>
        <input type="color" class="form-control form-control-color" id="calendar_booking_color_default" name="calendar_booking_color_default" value="{{ get_option('calendar_booking_color_default', '#0000ff') }}">
    </div>
    </div>
    <!-- Show Admin Bookings on Calendar -->
    <div class="mb-3">
        <label class="form-label">Show Admin Bookings on Calendar</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="calendar_admin_booking_active" id="calendar_admin_booking_active_yes" value="1" {{ get_option('calendar_admin_booking_active', 1) == 1 ? 'checked' : '' }}>
            <label class="form-check-label pl-1" for="calendar_admin_booking_active_yes">Yes</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="calendar_admin_booking_active" id="calendar_admin_booking_active_no" value="0" {{ get_option('calendar_admin_booking_active', 1) == 0 ? 'checked' : '' }}>
            <label class="form-check-label pl-1" for="calendar_admin_booking_active_no">No</label>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>