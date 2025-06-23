<div class="calendar-widget">
    <h3 class="cart-page-title">Admin Booking Calendar</h3>
    <div id="calendar"></div>

    <!-- Modal for creating a job -->
    <div id="createJobModal" style="display: none;">
        <h4>Create Job for Booking</h4>
        <form id="createJobForm">
            <label>Job Title</label>
            <input type="text" id="jobTitle" required>
            <br>
            <label>Job Details</label>
            <textarea id="jobDetails"></textarea>
            <br>
            <button type="submit">Create Job</button>
        </form>
    </div>
</div>
