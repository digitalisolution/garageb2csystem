<div class="tab-pane fade" id="delivery_time">
    {{ Form::open(['url' => url("AutoCare/supplier/store/{$id}"), 'method' => 'POST']) }}
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#fully_fitted"
                data-toggle="tab"><strong>{{ __('Fully Fitted') }}</strong></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#mobile_fitted" data-toggle="tab"><strong>{{ __('Mobile Fitted') }}</strong></a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Fully Fitted --}}
        <div class="tab-pane active in" id="fully_fitted">
            <input type="hidden" name="fully_fitted_supplier" value="{{ $id }}">
            <div class="text-right mb-2">
                <a href="javascript:void(0)" class="btn btn-primary"
                    onclick="AddDeliveryRow('customFields', 'fully_fitted', 'fully_fitted_')">
                    <i class="fa fa-plus-circle"></i> Add Row
                </a>
            </div>
            <div class="pTab" id="customFields">
                @foreach($fullyFittedItems as $vals)
                    <div class="block-item d-flex gap-2 bg-light p-2 border">
                        <input type="hidden" name="fully_fitted_delivery_type[]" value="fully_fitted">
                        <input type="hidden" name="fully_fitted_row_id[]" value="{{ $vals['id'] ?? '' }}">
                        <select class='form-control' name='fully_fitted_day[]'>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <option value="{{ $day }}" {{ $vals['day'] == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>

                        @foreach (['start_hours', 'start_minutes', 'end_hours', 'end_minutes'] as $field)
                            <select class='form-control' name='fully_fitted_{{ $field }}[]'>
                                <option value="">{{ strtoupper(substr($field, -2)) }}</option>
                                @php
                                    $range = in_array($field, ['start_hours', 'end_hours']) ? range(0, 23) : range(0, 55, 5);
                                @endphp
                                @foreach ($range as $val)
                                    @php $formatted = str_pad($val, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $formatted }}" {{ $vals[$field] == $formatted ? 'selected' : '' }}>
                                        {{ $formatted }}</option>
                                @endforeach
                            </select>
                        @endforeach

                        <input type="number" class='form-control' minlength="0" name='fully_fitted_delivery_time[]'
                            value="{{ $vals['delivery_time'] }}">

                        <a href='javascript:void(0);' class='removerow btn btn-danger'>
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mobile Fitted --}}
        <div class="tab-pane fade" id="mobile_fitted">
            <input type="hidden" name="mobile_fitted_supplier" value="{{ $id }}">
            <div class="text-right mb-2">
                <a href="javascript:void(0)" class="btn btn-info"
                    onclick="AddDeliveryRow('customFieldsmobile', 'mobile_fitted', 'mobile_fitted_')">
                    <i class="fa fa-plus-circle"></i> Add Row
                </a>
            </div>
            <div class="pTab" id="customFieldsmobile">
                @foreach($mobileFittedItems as $vals)
                    <div class="block-item d-flex gap-2 bg-light p-2 border">
                        <input type="hidden" name="mobile_fitted_delivery_type[]" value="mobile_fitted">
                        <input type="hidden" name="mobile_fitted_row_id[]" value="{{ $vals['id'] ?? '' }}">

                        <select class='form-control' name='mobile_fitted_day[]'>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <option value="{{ $day }}" {{ $vals['day'] == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>

                        @foreach (['start_hours', 'start_minutes', 'end_hours', 'end_minutes'] as $field)
                            <select class='form-control' name='mobile_fitted_{{ $field }}[]'>
                                <option value="">{{ strtoupper(substr($field, -2)) }}</option>
                                @php
                                    $range = in_array($field, ['start_hours', 'end_hours']) ? range(0, 23) : range(0, 55, 5);
                                @endphp
                                @foreach ($range as $val)
                                    @php $formatted = str_pad($val, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $formatted }}" {{ $vals[$field] == $formatted ? 'selected' : '' }}>
                                        {{ $formatted }}</option>
                                @endforeach
                            </select>
                        @endforeach

                        <input type="number" class='form-control' minlength="0" name='mobile_fitted_delivery_time[]'
                            value="{{ $vals['delivery_time'] }}">

                        <a href='javascript:void(0);' class='removerow btn btn-danger'>
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-3 text-right">
            <button type="submit" class="btn btn-primary">Store</button>
        </div>
    </div>
    {{ Form::close() }}
</div>

<script>
    function AddDeliveryRow(containerId, deliveryType, prefix = '') {
        const container = document.getElementById(containerId);
        let hourOptions = `<option value="">HH</option>`;
        for (let i = 0; i < 24; i++) {
            let hh = i.toString().padStart(2, '0');
            hourOptions += `<option value="${hh}">${hh}</option>`;
        }

        let minuteOptions = `<option value="">MM</option>`;
        for (let i = 0; i < 60; i += 5) {
            let mm = i.toString().padStart(2, '0');
            minuteOptions += `<option value="${mm}">${mm}</option>`;
        }

        const newRow = document.createElement('div');
        newRow.className = 'block-item d-flex gap-2 bg-light p-2 border';
        newRow.innerHTML = `
        <input type="hidden" name="${prefix}delivery_type[]" value="${deliveryType}">
        <input type="hidden" name="${prefix}row_id[]" value="">

       
            <select class='form-control' name='${prefix}day[]'>
                <option value="">-- Select Day --</option>
                ${['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].map(day => `<option value="${day}">${day}</option>`).join('')}
            </select>
       
        <select class='form-control' name='${prefix}start_hours[]'>${hourOptions}</select>
        <select class='form-control' name='${prefix}start_minutes[]'>${minuteOptions}</select>
        <select class='form-control' name='${prefix}end_hours[]'>${hourOptions}</select>
        <select class='form-control' name='${prefix}end_minutes[]'>${minuteOptions}</select>
        <input type="number" minlength="0" class='form-control' name='${prefix}delivery_time[]'>
        <a href='javascript:void(0);' class='removerow btn btn-danger'><i class="fa fa-trash"></i></a>
    `;
        container.appendChild(newRow);
    }
    document.addEventListener('click', function (e) {
        if (e.target.closest('.removerow')) {
            e.target.closest('.block-item').remove();
        }
    });
</script>