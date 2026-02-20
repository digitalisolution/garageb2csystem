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
                                <!-- <option value="slot" {{ old('calendar_type', $calendarSetting->calendar_type ?? '') == 'slot' ? 'selected' : '' }}>Slot</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group required">
                            <label class="control-label">Select Garage</label>
                            <select name="garage_id" class="form-control" required>
                                <option value="">-- Select Garage --</option>
                                @foreach($garage_data as $garage)
                                    <option value="{{ $garage->id }}" {{ old('garage_id', $calendarSetting->garage_id ?? '') == $garage->id ? 'selected' : '' }}>
                                        {{ $garage->garage_name }}
                                    </option>
                                @endforeach
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
                            <label class="control-label">AM/PM Break Point</label>
                            <input type="time" name="am_pm_break_point"
                                value="{{ old('am_pm_break_point', $calendarSetting->am_pm_break_point ?? '12:00') }}"
                                class="form-control">
                            <small class="text-muted">This time splits the day into AM and PM slots</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group required">
                            <label class="control-label">Booking per Slot</label>
                            <select name="slot_perday_booking" class="form-control">
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('slot_perday_booking', $calendarSetting->slot_perday_booking ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group required">
                            <label class="control-label">MOT Booking per Slot</label>
                            <select name="mot_perday_booking" class="form-control">
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('mot_perday_booking', $calendarSetting->mot_perday_booking ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group required">
                            <label class="control-label">Slot Duration</label>
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
                                        <input type="checkbox" name="open_close_hours[{{ $day }}][closed]"
                                            class="closed-checkbox" {{ old('open_close_hours.' . $day . '.closed', $calendarSetting->open_close_hours[$day]['closed'] ?? false) ? 'checked' : '' }}
                                            style="width:20px; height:20px;">
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
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="holidays[apply_on_dashboard]"
                            id="holidays_apply_on_dashboard" value="1" {{ old('holidays.apply_on_dashboard', $calendarSetting->holidays['apply_on_dashboard'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="holidays_apply_on_dashboard">
                            Apply Holiday Block on Dashboard
                        </label>
                    </div>
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
                    <h5>Block Day/Time</h5>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="block_date_time[apply_on_dashboard]"
                            id="block_date_time_apply_on_dashboard" value="1" {{ old('block_date_time.apply_on_dashboard', $calendarSetting->block_date_time['apply_on_dashboard'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="block_date_time_apply_on_dashboard">
                            Apply Day/Time Block on Dashboard
                        </label>
                    </div>
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
            <!-- Ramps -->
            <section class="bg-white p-3 rounded mb-3">
                <div class="block-datetime-section">
                    <h5>Ramps Block Day/Time</h5>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="ramps_block_day_time[apply_on_dashboard]"
                            id="ramps_block_day_time_apply_on_dashboard" value="1" {{ old('ramps_block_day_time.apply_on_dashboard', $calendarSetting->ramps_block_day_time['apply_on_dashboard'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="ramps_block_day_time_apply_on_dashboard">
                            Apply Ramps Day/Time Block on Dashboard
                        </label>
                    </div>

                    <div id="ramps-block-day-time-fields">
                        @if(isset($calendarSetting->ramps_block_day_time['days']))
                            @foreach($calendarSetting->ramps_block_day_time['days'] as $index => $day)
                                <div class="ramp-block-item">
                                    {{-- Day --}}
                                    <select name="ramps_block_day_time[days][]" class="block-day">
                                        <option value="all" {{ $day == 'all' ? 'selected' : '' }}>All Days</option>
                                        <option value="Monday" {{ $day == 'Monday' ? 'selected' : '' }}>Monday</option>
                                        <option value="Tuesday" {{ $day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                        <option value="Wednesday" {{ $day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                        <option value="Thursday" {{ $day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                        <option value="Friday" {{ $day == 'Friday' ? 'selected' : '' }}>Friday</option>
                                        <option value="Saturday" {{ $day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                        <option value="Sunday" {{ $day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                                    </select>

                                    {{-- From --}}
                                    <input type="time" name="ramps_block_day_time[from][]"
                                        value="{{ $calendarSetting->ramps_block_day_time['from'][$index] ?? '' }}">

                                    {{-- To --}}
                                    <input type="time" name="ramps_block_day_time[to][]"
                                        value="{{ $calendarSetting->ramps_block_day_time['to'][$index] ?? '' }}">

                                    {{-- Ramp --}}
                                    <select name="ramps_block_day_time[ramp][]" class="block-day">
                                        <option value="">-- Select Ramps --</option>
                                        @foreach($ramps as $ramp)
                                            <option value="{{ $ramp->ramp_id }}" {{ ($calendarSetting->ramps_block_day_time['ramp'][$index] ?? '') == $ramp->ramp_id ? 'selected' : '' }}>
                                                {{ $ramp->ramp_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- Block Title --}}
                                    <input type="text" name="ramps_block_day_time[block_title][]"
                                        value="{{ $calendarSetting->ramps_block_day_time['block_title'][$index] ?? '' }}"
                                        placeholder="Block Title">

                                    <button type="button" class="remove-block btn btn-danger">Remove</button>

                                </div>
                            @endforeach
                        @endif


                    </div>

                    <button type="button" id="ramps-add-block" class="btn btn-success mt-2">Add Block</button>
                </div>
            </section>
            <!-- Ramps End -->

            <section class="bg-white p-3 rounded mb-3">
                <div class="block-specific-datetime-section">
                    <h5>Block Specific Date/Time</h5>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="block_specific_datetime[apply_on_dashboard]"
                            id="block_specific_datetime_apply_on_dashboard" value="1" {{ old('block_specific_datetime.apply_on_dashboard', $calendarSetting->block_specific_datetime['apply_on_dashboard'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="block_specific_datetime_apply_on_dashboard">
                            Apply Specific Date/Time Block on Dashboard
                        </label>
                    </div>
                    <div id="block-specific-datetime-fields">
                        @if(isset($calendarSetting->block_specific_datetime['date']))
                            @foreach($calendarSetting->block_specific_datetime['date'] as $index => $date)
                                <div class="block-item-specific">
                                    <input type="date" name="block_specific_datetime[date][]"
                                        value="{{ old("block_specific_datetime.date.$index", $date ?? '') }}" placeholder="Date">

                                    <input type="time" name="block_specific_datetime[from][]"
                                        value="{{ old("block_specific_datetime.from.$index", $calendarSetting->block_specific_datetime['from'][$index] ?? '') }}"
                                        placeholder="From">

                                    <input type="time" name="block_specific_datetime[to][]"
                                        value="{{ old("block_specific_datetime.to.$index", $calendarSetting->block_specific_datetime['to'][$index] ?? '') }}"
                                        placeholder="To">

                                    <input type="text" name="block_specific_datetime[block_title][]"
                                        value="{{ old("block_specific_datetime.block_title.$index", $calendarSetting->block_specific_datetime['block_title'][$index] ?? '') }}"
                                        placeholder="Block Title">

                                    <button type="button" class="remove-block-specific btn btn-danger">Remove</button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" id="add-block-specific" class="btn btn-success">Add Date/Time Block</button>
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
                                        <option value="fully_fitted" {{ $calendarSetting->block_fitting_type_days['jobtype'][$index] == 'fully_fitted' ? 'selected' : '' }}>Fully Fitted</option>
                                        <option value="mobile_fitted" {{ $calendarSetting->block_fitting_type_days['jobtype'][$index] == 'mobile_fitted' ? 'selected' : '' }}>Mobile Fitted</option>
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
            <section class="bg-white p-3 rounded mb-3">
                <div class="block-date-time-jobtype-section">
                    <h5>Block by Date/Time for Job Type</h5>
                    <div id="block-date-time-jobtype-fields">
                        @if(isset($calendarSetting->block_fitting_type_datetime['date']))
                            @foreach($calendarSetting->block_fitting_type_datetime['date'] as $index => $date)
                                <div class="block-item-jobtype-datetime">
                                    <input type="date" name="block_fitting_type_datetime[date][]"
                                        value="{{ old('block_fitting_type_datetime.date.' . $index, $date) }}" placeholder="Date">

                                    <input type="time" name="block_fitting_type_datetime[from][]"
                                        value="{{ old('block_fitting_type_datetime.from.' . $index, $calendarSetting->block_fitting_type_datetime['from'][$index] ?? '') }}"
                                        placeholder="From">

                                    <input type="time" name="block_fitting_type_datetime[to][]"
                                        value="{{ old('block_fitting_type_datetime.to.' . $index, $calendarSetting->block_fitting_type_datetime['to'][$index] ?? '') }}"
                                        placeholder="To">

                                    <select name="block_fitting_type_datetime[jobtype][]" class="block-day">
                                        <option value="all" {{ $calendarSetting->block_fitting_type_datetime['jobtype'][$index] == 'all' ? 'selected' : '' }}>All jobtype</option>
                                        <option value="fully_fitted" {{ $calendarSetting->block_fitting_type_datetime['jobtype'][$index] == 'fully_fitted' ? 'selected' : '' }}>Fully Fitted</option>
                                        <option value="mobile_fitted" {{ $calendarSetting->block_fitting_type_datetime['jobtype'][$index] == 'mobile_fitted' ? 'selected' : '' }}>Mobile Fitted</option>
                                    </select>

                                    <input type="text" name="block_fitting_type_datetime[block_title][]"
                                        value="{{ old('block_fitting_type_datetime.block_title.' . $index, $calendarSetting->block_fitting_type_datetime['block_title'][$index] ?? '') }}"
                                        placeholder="Block Title">

                                    <button type="button" class="remove-block-jobtype-datetime btn btn-danger">Remove</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-block-jobtype-datetime" class="btn btn-success">Add</button>
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
                                        <option value="{{ $service->service_id }}" {{ old('block_service_perhours.service_type.' . $index, $calendarSetting->block_service_perhours['service_type'][$index] ?? '') == $service->service_id ? 'selected' : '' }}>
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
            <section class="bg-white p-3 rounded mb-3">
                <div class="block-date-time-service-section">
                    <h5>Block by Date/Time for Service</h5>
                    <div id="block-date-time-service-fields">
                        @if(isset($calendarSetting->block_service_datetime['date']))
                            @foreach($calendarSetting->block_service_datetime['date'] as $index => $date)
                                <div class="block-item-service-datetime">
                                    <input type="date" name="block_service_datetime[date][]"
                                        value="{{ old('block_service_datetime.date.' . $index, $date) }}" placeholder="Date">

                                    <input type="time" name="block_service_datetime[from][]"
                                        value="{{ old('block_service_datetime.from.' . $index, $calendarSetting->block_service_datetime['from'][$index] ?? '') }}"
                                        placeholder="From">

                                    <input type="time" name="block_service_datetime[to][]"
                                        value="{{ old('block_service_datetime.to.' . $index, $calendarSetting->block_service_datetime['to'][$index] ?? '') }}"
                                        placeholder="To">

                                    <select name="block_service_datetime[service_type][]" class="block-day">
                                        <option value="">-- Select Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->service_id }}" {{ old('block_service_datetime.service_type.' . $index, $calendarSetting->block_service_datetime['service_type'][$index] ?? '') == $service->service_id ? 'selected' : '' }}>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="text" name="block_service_datetime[block_title][]"
                                        value="{{ old('block_service_datetime.block_title.' . $index, $calendarSetting->block_service_datetime['block_title'][$index] ?? '') }}"
                                        placeholder="Block Title">

                                    <button type="button" class="remove-block-service-datetime btn btn-danger">Remove</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-block-service-datetime" class="btn btn-success">Add</button>
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
                if (selectedType === "calendar_hours") {
                    dayCalendarSection.style.display = "block";
                    ampmCalendarSection.style.display = "none";
                } else if (selectedType === "calendar_ampm") {
                    dayCalendarSection.style.display = "block";
                    ampmCalendarSection.style.display = "none";
                } else {
                    dayCalendarSection.style.display = "none";
                    ampmCalendarSection.style.display = "none";
                }
            }
            toggleCalendarSections();
            calendarTypeSelect.addEventListener("change", toggleCalendarSections);
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

            // Add a Ramps block Day/time field
            var rampOptions = `
                    @foreach($ramps as $ramp)
                        <option value="{{ $ramp->ramp_id }}">{{ $ramp->ramp_name }}</option>
                    @endforeach
                `;
            document.getElementById("ramps-add-block").addEventListener("click", function () {

                var blockItem = document.createElement("div");
                blockItem.classList.add("ramp-block-item");

                blockItem.innerHTML = `
                        <select name="ramps_block_day_time[days][]" class="ramps-block-day">
                            <option value="all">All Days</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>

                        <input type="time" name="ramps_block_day_time[from][]" placeholder="From">
                        <input type="time" name="ramps_block_day_time[to][]" placeholder="To">

                        <select name="ramps_block_day_time[ramp][]" class="block-day">
                            <option value="">-- Select Ramps --</option>
                            ${rampOptions}
                        </select>

                        <input type="text" name="ramps_block_day_time[block_title][]" placeholder="Block Title">

                        <button type="button" class="remove-block btn btn-danger">Remove</button>
                    `;

                document.getElementById("ramps-block-day-time-fields").appendChild(blockItem);
            });


            // Add new Specific Date/Time Block
            document.getElementById("add-block-specific").addEventListener("click", function () {
                const blockItem = document.createElement("div");
                blockItem.classList.add("block-item-specific");
                blockItem.innerHTML = `
                            <input type="date" name="block_specific_datetime[date][]" placeholder="Date">
                            <input type="time" name="block_specific_datetime[from][]" placeholder="From">
                            <input type="time" name="block_specific_datetime[to][]" placeholder="To">
                            <input type="text" name="block_specific_datetime[block_title][]" placeholder="Block Title">
                            <button type="button" class="remove-block-specific btn btn-danger">Remove</button>
                        `;
                document.getElementById("block-specific-datetime-fields").appendChild(blockItem);
            });

            // Remove Specific Date/Time Block
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-block-specific")) {
                    e.target.closest(".block-item-specific").remove();
                }
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

            // Add new JobType DateTime block
            document.getElementById("add-block-jobtype-datetime").addEventListener("click", function () {
                const blockItem = document.createElement("div");
                blockItem.classList.add("block-item-jobtype-datetime");
                blockItem.innerHTML = `
                        <input type="date" name="block_fitting_type_datetime[date][]" placeholder="Date">
                        <input type="time" name="block_fitting_type_datetime[from][]" placeholder="From">
                        <input type="time" name="block_fitting_type_datetime[to][]" placeholder="To">
                        <select name="block_fitting_type_datetime[jobtype][]" class="block-day">
                            <option value="all">All jobtype</option>
                            <option value="fully_fitted">Fully Fitted</option>
                            <option value="mobile_fitted">Mobile Fitted</option>
                        </select>
                        <input type="text" name="block_fitting_type_datetime[block_title][]" placeholder="Block Title">
                        <button type="button" class="remove-block-jobtype-datetime btn btn-danger">Remove</button>
                    `;
                document.getElementById("block-date-time-jobtype-fields").appendChild(blockItem);
            });

            // Remove JobType DateTime block
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-block-jobtype-datetime")) {
                    e.target.closest(".block-item-jobtype-datetime").remove();
                }
            });

            // Add new Service DateTime block
            document.getElementById("add-block-service-datetime").addEventListener("click", function () {
                const blockItem = document.createElement("div");
                blockItem.classList.add("block-item-service-datetime");

                const serviceOptions = {!! json_encode($services->map(fn($s) => ['id' => $s->service_id, 'name' => $s->name])) !!};
                const serviceSelectHTML = serviceOptions.map(s => `<option value="${s.id}">${s.name}</option>`).join('');

                blockItem.innerHTML = `
                        <input type="date" name="block_service_datetime[date][]" placeholder="Date">
                        <input type="time" name="block_service_datetime[from][]" placeholder="From">
                        <input type="time" name="block_service_datetime[to][]" placeholder="To">
                        <select name="block_service_datetime[service_type][]" class="block-day">
                            <option value="">-- Select Service --</option>
                            ${serviceSelectHTML}
                        </select>
                        <input type="text" name="block_service_datetime[block_title][]" placeholder="Block Title">
                        <button type="button" class="remove-block-service-datetime btn btn-danger">Remove</button>
                    `;
                document.getElementById("block-date-time-service-fields").appendChild(blockItem);
            });

            // Remove Service DateTime block
            document.addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-block-service-datetime")) {
                    e.target.closest(".block-item-service-datetime").remove();
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

        .remove-holiday {
            color: #fff;
            background-color: #f86c6b;
            border-color: #f86c6b;
            border-radius: 5px;
            padding: 7px 15px;
            border: 0;
        }

        .block-day {
            padding: 6px;
        }
    </style>
@endsection