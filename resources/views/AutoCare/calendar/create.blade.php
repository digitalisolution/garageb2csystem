@extends('samples')

@section('content')
<div class="container-fluid">
     
    <form method="POST"
        action="{{ isset($calendarSetting) ? route('calendar.update', $calendarSetting->calendar_setting_id) : route('calendar.store') }}">
        @csrf
        @if (isset($calendarSetting))
            @method('PUT')
        @endif
        <section class="bg-white p-3 rounded mb-3">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group required">
                    <label class="control-label">Calendar Type</label>
                    <select name="calendar_type" id="calendar_type" class="form-control">
                        <option value="calendar_ampm" {{ old('calendar_type', $calendarSetting->calendar_type ?? '') == 'calendar_ampm' ? 'selected' : '' }}>AM/PM Calendar</option>
                        <option value="calendar_hours" {{ old('calendar_type', $calendarSetting->calendar_type ?? '') == 'calendar_hours' ? 'selected' : '' }}>Day Calendar</option>
                        <option value="slot" {{ old('calendar_type', $calendarSetting->calendar_type ?? '') == 'slot' ? 'selected' : '' }}>Slot</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group required">
                    <label class="control-label">Name</label>
                    <input type="text" name="calendar_name"
                        value="{{ old('calendar_name', $calendarSetting->calendar_name ?? 'Default Time') }}"
                        class="form-control m-0">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group required">
                    <label class="control-label">Booking per Slot</label>
                    <select name="slot_perday_booking" class="form-control">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('slot_perday_booking', $calendarSetting->slot_perday_booking ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group required">
                    <label class="control-label">MOT Booking per Slot</label>
                    <select name="mot_booking_per_slot" class="form-control">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('mot_booking_per_slot', $calendarSetting->mot_booking_per_slot ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group required">
                    <label class="control-label">Slot Duration (in Minutes/Hours)</label>
                    <select name="duration" class="form-control">
                        @foreach([15, 30, 40, 45, 60, 120, 180, 300, 360] as $value)
                            <option value="{{ $value }}" {{ old('duration', $calendarSetting->duration ?? '') == $value ? 'selected' : '' }}>{{ $value }} Minutes</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>
     @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    <section class="bg-white p-3 rounded mb-3">
        <div id="day-calendar-section" class="schedule-section">
            <h5>Open/Close Hours</h5>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Day</th>
                        <th>Opening Time</th>
                        <th>Closing Time</th>
                        <th class="text-center">Closed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <tr>
                            <td>{{ ucfirst($day) }}</td>
                            <td>
                                <input type="time" name="open_close_hours[{{ $day }}][opening]"
                                    value="{{ old('open_close_hours.' . $day . '.opening', $calendarSetting->open_close_hours[$day]['opening'] ?? '08:30') }}"
                                    class="time-input form-control">
                            </td>
                            <td>
                                <input type="time" name="open_close_hours[{{ $day }}][closing]"
                                    value="{{ old('open_close_hours.' . $day . '.closing', $calendarSetting->open_close_hours[$day]['closing'] ?? '20:00') }}"
                                    class="time-input form-control">
                            </td>
                            <td align="center">
                                <input type="checkbox" name="open_close_hours[{{ $day }}][closed]" class="closed-checkbox"
                                    {{ old('open_close_hours.' . $day . '.closed', $calendarSetting->open_close_hours[$day]['closed'] ?? false) ? 'checked' : '' }} style="width:20px; height:20px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </section>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const checkboxes = document.querySelectorAll('.closed-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const row = this.closest('tr');
                        const timeInputs = row.querySelectorAll('.time-input');
                        timeInputs.forEach(input => {
                            input.disabled = this.checked;
                        });
                    });

                    // Initialize the disabled state based on the checkbox
                    const row = checkbox.closest('tr');
                    const timeInputs = row.querySelectorAll('.time-input');
                    timeInputs.forEach(input => {
                        input.disabled = checkbox.checked;
                    });
                });
            });
        </script>
        <section class="bg-white p-3 rounded mb-3" style="display: none;">
        <div id="ampm-calendar-section" class="schedule-section">
            <h5>AM/PM Settings</h5>
            <table>
                <thead class="thead-dark">
                    <tr>
                        <th>Day</th>
                        <th>AM</th>
                        <th>PM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <tr>
                            <td>{{ ucfirst($day) }}</td>
                            <td><input type="checkbox" name="open_close_hours[{{ $day }}][am]" {{ old('open_close_hours.' . $day . '.am', $calendarSetting->open_close_hours[$day]['am'] ?? 'on') == 'on' ? 'checked' : '' }}>
                            </td>
                            <td><input type="checkbox" name="open_close_hours[{{ $day }}][pm]" {{ old('open_close_hours.' . $day . '.pm', $calendarSetting->open_close_hours[$day]['pm'] ?? 'on') == 'on' ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <section class="bg-white p-3 rounded mb-3">
        <div class="holidays-section">
            <h5>Holidays</h5>
            <div id="holiday-fields">
                @foreach($calendarSetting->holidays['holiday_name'] ?? [] as $index => $name)
                    <div class="holiday-item">
                        <input type="text" name="holidays[holiday_name][]"
                            value="{{ old("holidays.holiday_name.$index", $name) }}" placeholder="Holiday Name">

                        <input type="date" name="holidays[holidayDate][]"
                            value="{{ old("holidays.holidayDate.$index", $calendarSetting->holidays['holidayDate'][$index] ?? '') }}"
                            placeholder="Holiday Date">

                        <button type="button" class="remove-holiday">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-holiday" class="btn btn-success">Add Holiday</button>
        </div>
    </section>
<section class="bg-white p-3 rounded mb-3">
        <div class="block-datetime-section">
            <h5>Block Date/Time</h5>
            <div id="block-datetime-fields">
                @if(isset($calendarSetting->block_date_time['days']))
                    @foreach($calendarSetting->block_date_time['days'] as $index => $day)
                        <div class="block-item">
                            <select name="block_date_time[days][]" class="block-day">
                                <option value="all" {{ $day == 'all' ? 'selected' : '' }}>All Days</option>
                                <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ $day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ $day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>

                            <input type="time" name="block_date_time[from][]"
                                value="{{ old("block_date_time.from.$index", $calendarSetting->block_date_time['from'][$index] ?? '') }}"
                                placeholder="From">

                            <input type="time" name="block_date_time[to][]"
                                value="{{ old("block_date_time.to.$index", $calendarSetting->block_date_time['to'][$index] ?? '') }}"
                                placeholder="To">

                            <input type="text" name="block_date_time[block_title][]"
                                value="{{ old("block_date_time.block_title.$index", $calendarSetting->block_date_time['block_title'][$index] ?? '') }}"
                                placeholder="Block Title">

                            <button type="button" class="remove-block btn btn-danger">Remove</button>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" id="add-block" class="btn btn-success">Add Block</button>
        </div>
    </section>
    <section class="bg-white p-3 rounded mb-3">
        <div class="block-datetime-section">
            <h5>Block Date/Time Job Type</h5>
            <div id="block-datetime-jobtype-fields">
                @if(isset($calendarSetting->block_fitting_type_days['days']))
                    @foreach($calendarSetting->block_fitting_type_days['days'] as $index => $day)
                        <div class="block-item-jobtype">
                            <select name="block_fitting_type_days[days][]" class="block-day">
                                <option value="all" {{ $day == 'all' ? 'selected' : '' }}>All Days</option>
                                <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ $day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ $day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>

                            <input type="time" name="block_fitting_type_days[from][]"
                                value="{{ old("block_fitting_type_days.from.$index", $calendarSetting->block_fitting_type_days['from'][$index] ?? '') }}"
                                placeholder="From">

                            <input type="time" name="block_fitting_type_days[to][]"
                                value="{{ old("block_fitting_type_days.to.$index", $calendarSetting->block_fitting_type_days['to'][$index] ?? '') }}"
                                placeholder="To">
                            <select name="block_fitting_type_days[jobtype][]" class="block-day">
                                <option value="all" {{ $calendarSetting->block_fitting_type_days['jobtype'][$index] == 'all' ? 'selected' : '' }}>All jobtype</option>
                                <option value="fully_fitted" {{ $calendarSetting->block_fitting_type_days['jobtype'][$index]  == 'fully_fitted' ? 'selected' : '' }}>Fully Fitted</option>
                                <option value="mobile_fitted" {{ $calendarSetting->block_fitting_type_days['jobtype'][$index]  == 'mobile_fitted' ? 'selected' : '' }}>Mobile Fitted</option>
                            </select>
                            <input type="text" name="block_fitting_type_days[block_title][]"
                                value="{{ old("block_fitting_type_days.block_title.$index", $calendarSetting->block_fitting_type_days['block_title'][$index] ?? '') }}"
                                placeholder="Block Title">

                            <button type="button" class="remove-block-jobtype btn btn-danger">Remove</button>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="button" id="add-block-jobtype" class="btn btn-success">Add Block</button>
        </div>
    </section>
    <section class="bg-white p-3 rounded mb-3 d-none">
        <div class="block-service-perdays-section">
            <h5>Block Service Per Days</h5>
            <div id="block-service-perdays-fields">
                @foreach($calendarSetting->block_service_perdays['days'] ?? [] as $index => $day)
                    <div class="block-service-item">
                        <select name="block_service_perdays[days][]" class="block-day">
                            <option value="all" {{ $day == 'all' ? 'selected' : '' }}>All Days</option>
                            <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                            <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                            <option value="Saturday" {{ $day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ $day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>

                        <input type="text" name="block_service_perdays[service_type][]"
                            value="{{ old("block_service_perdays.service_type.$index", $calendarSetting->block_service_perdays['service_type'][$index] ?? '') }}"
                            placeholder="Service Type">

                        <input type="text" name="block_service_perdays[block_title][]"
                            value="{{ old("block_service_perdays.block_title.$index", $calendarSetting->block_service_perdays['block_title'][$index] ?? '') }}"
                            placeholder="Block Title">

                        <button type="button" class="remove-block-service btn btn-danger">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-block-service-perdays" class="btn btn-success">Add</button>
        </div>
    </section>

<section class="bg-white p-3 rounded mb-3">
        <div class="block-service-perhours-section">
            <h5>Block Service Perhours</h5>
            <div id="block-service-perhours-fields">
                @foreach($calendarSetting->block_service_perhours['days'] ?? [] as $index => $day)
                    <div class="block-service-hour-item">
                        <!-- Select dropdown for days -->
                        <select name="block_service_perhours[days][]" class="block-day">
                            <option value="all" {{ $day == 'all' ? 'selected' : '' }}>All Days</option>
                            <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                            <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                            <option value="Saturday" {{ $day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="Sunday" {{ $day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>

                        <!-- Time input for 'from' -->
                        <input type="time" name="block_service_perhours[from][]"
                            value="{{ old('block_service_perhours.from.' . $index, $calendarSetting->block_service_perhours['from'][$index] ?? '') }}"
                            placeholder="From">

                        <!-- Time input for 'to' -->
                        <input type="time" name="block_service_perhours[to][]"
                            value="{{ old('block_service_perhours.to.' . $index, $calendarSetting->block_service_perhours['to'][$index] ?? '') }}"
                            placeholder="To">

                        <!-- Service type input -->
                            <select name="block_service_perhours[service_type][]" class="block-day">
                                <option value="">-- Select Service --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->service_id }}" 
                                        {{ old('block_service_perhours.service_type.' . $index, $calendarSetting->block_service_perhours['service_type'][$index] ?? '') == $service->service_id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        <!-- Block title input -->
                        <input type="text" name="block_service_perhours[block_title][]"
                            value="{{ old('block_service_perhours.block_title.' . $index, $calendarSetting->block_service_perhours['block_title'][$index] ?? '') }}"
                            placeholder="Block Title">

                        <!-- Remove button -->
                        <button type="button" class="remove-block-service-hour btn btn-danger">Remove</button>
                    </div>
                @endforeach
            </div>

            <!-- Add new block service button -->
            <button type="button" id="add-block-service-perhours" class="btn btn-success">Add</button>
        </div>
</section>




        <button type="submit" class="btn btn-primary mb-3">Save</button>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarTypeSelect = document.getElementById("calendar_type");
        const dayCalendarSection = document.getElementById("day-calendar-section");
        const ampmCalendarSection = document.getElementById("ampm-calendar-section");

        // Function to toggle visibility based on the calendar type
        function toggleCalendarSections() {
            const selectedType = calendarTypeSelect.value;

            // Show the appropriate section based on the selected calendar type
            if (selectedType === "calendar_hours") {
                dayCalendarSection.style.display = "block";
                ampmCalendarSection.style.display = "none";
            } else if (selectedType === "calendar_ampm") {
                dayCalendarSection.style.display = "none";
                ampmCalendarSection.style.display = "block";
            } else {
                dayCalendarSection.style.display = "none";
                ampmCalendarSection.style.display = "none";
            }
        }

        // Trigger the function on page load and when the calendar type changes
        toggleCalendarSections();  // Run on load
        calendarTypeSelect.addEventListener("change", toggleCalendarSections);  // Run on change
    });

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Add a new holiday field
        document.getElementById("add-holiday").addEventListener("click", function () {
            var holidayItem = document.createElement("div");
            holidayItem.classList.add("holiday-item");
            holidayItem.innerHTML = `
            <input type="text" name="holidays[holiday_name][]" placeholder="Holiday Name">
            <input type="date" name="holidays[holidayDate][]" placeholder="Holiday Date">
            <button type="button" class="remove-holiday btn btn-danger">Remove</button>
        `;
            document.getElementById("holiday-fields").appendChild(holidayItem);
        });

        // Remove a holiday field
        document.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-holiday")) {
                e.target.parentElement.remove();
            }
        });

        // Add a new block date/time field
        document.getElementById("add-block").addEventListener("click", function () {
            var blockItem = document.createElement("div");
            blockItem.classList.add("block-item");
            blockItem.innerHTML = `
             <select name="block_date_time[days][]" class="block-day">
                <option value="all">All Days</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="time" name="block_date_time[from][]" placeholder="From">
            <input type="time" name="block_date_time[to][]" placeholder="To">
            <input type="text" name="block_date_time[block_title][]" placeholder="Block Title">
            <button type="button" class="remove-block btn btn-danger">Remove</button>
        `;
            document.getElementById("block-datetime-fields").appendChild(blockItem);
        });

          // Add a new block date/time jobtype field
        document.getElementById("add-block-jobtype").addEventListener("click", function () {
            var blockItem = document.createElement("div");
            blockItem.classList.add("block-item-jobtype");
            blockItem.innerHTML = `
             <select name="block_fitting_type_days[days][]" class="block-day">
                <option value="all">All Days</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="time" name="block_fitting_type_days[from][]" placeholder="From">
            <input type="time" name="block_fitting_type_days[to][]" placeholder="To">
             <select name="block_fitting_type_days[jobtype][]" class="block-day">
                <option value="all">All jobtype</option>
                <option value="fully_fitted">Fully Fitted</option>
                <option value="mobile_fitted">Mobile Fitted</option>
            </select>
            <input type="text" name="block_fitting_type_days[block_title][]" placeholder="Block Title">
            <button type="button" class="remove-block-jobtype btn btn-danger">Remove</button>
        `;
            document.getElementById("block-datetime-jobtype-fields").appendChild(blockItem);
        });


        // Remove a block field
        document.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-block")) {
                e.target.parentElement.remove();
            }
        });

         // Remove a block field
        document.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-block-jobtype")) {
                e.target.parentElement.remove();
            }
        });

        // Add a new block service per day field
        document.getElementById("add-block-service-perdays").addEventListener("click", function () {
            var blockServiceItem = document.createElement("div");
            blockServiceItem.classList.add("block-service-item");
            blockServiceItem.innerHTML = `
           <select name="block_service_perdays[days][]" class="block-day">
                <option value="all">All Days</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="text" name="block_service_perdays[service_type][]" placeholder="Service Type">
            <input type="text" name="block_service_perdays[block_title][]" placeholder="Block Title">
            <button type="button" class="remove-block-service btn btn-danger">Remove</button>
        `;
            document.getElementById("block-service-perdays-fields").appendChild(blockServiceItem);
        });

        // Remove a block service per day field
        document.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-block-service")) {
                e.target.parentElement.remove();
            }
        });

        // Add a new block service per hour field
        document.getElementById("add-block-service-perhours").addEventListener("click", function () {
            var blockServiceHourItem = document.createElement("div");
            blockServiceHourItem.classList.add("block-service-hour-item");

            // Populate the service options dynamically
            var serviceOptions = {!! json_encode($services->map(function ($service) {
                return ['service_id' => $service->service_id, 'name' => $service->name];
            })) !!};

            var serviceSelectOptions = serviceOptions.map(function (service) {
                return `<option value="${service.service_id}">${service.name}</option>`;
            }).join('');

            blockServiceHourItem.innerHTML = `
                <select name="block_service_perhours[days][]" class="block-day">
                    <option value="all">All Days</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="block_service_perhours[from][]" placeholder="From">
                <input type="time" name="block_service_perhours[to][]" placeholder="To">
                <select name="block_service_perhours[service_type][]" class="block-day">
                    <option value="">-- Select Service --</option>
                    ${serviceSelectOptions}
                </select>
                <input type="text" name="block_service_perhours[block_title][]" placeholder="Block Title">
                <button type="button" class="remove-block-service-hour btn btn-danger">Remove</button>
            `;

            document.getElementById("block-service-perhours-fields").appendChild(blockServiceHourItem);
        });
        // Remove a block service per hour field
        document.addEventListener("click", function (e) {
            if (e.target && e.target.classList.contains("remove-block-service-hour")) {
                e.target.parentElement.remove();
            }
        });
    });


</script>
<style>
    .schedule-section,
    .holidays-section,
    .block-datetime-section {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    input[type="time"],
    input[type="text"],
    input[type="date"],
    button {
        padding: 5px;
        margin: 5px 0;
    }
.remove-holiday{color: #fff;background-color: #f86c6b;border-color: #f86c6b;border-radius:5px;padding:7px 15px;border:0;}
.block-day{padding:6px;}
</style>
@endsection