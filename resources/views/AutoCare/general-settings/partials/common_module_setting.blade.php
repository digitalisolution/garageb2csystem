<form action="{{ route('settings.common-module.update') }}" method="POST">
    @csrf

    <h4>Support Call</h4>
    <div class="row">
        <!-- Phone Number -->
        <div class="col-md-3 mb-3">
            <label class="form-label">Support Call No.</label>
            <input type="text"
                   name="support_call"
                   class="form-control"
                   value="{{ DB::table('general_settings')->where('name','support_call')->value('value') }}"
                   placeholder="Enter phone number">
        </div>

        <!-- Enable / Disable -->
        <div class="col-md-3 mb-3">
            <label class="form-label d-block">Support Call Status</label>

            @php
                $status = DB::table('general_settings')->where('name','support_call')->value('status') ?? 0;
            @endphp

            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="support_call_status"
                       value="1"
                       {{ $status == 1 ? 'checked' : '' }}>
                <label class="form-check-label pl-1">Enable</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="radio"
                       name="support_call_status"
                       value="0"
                       {{ $status == 0 ? 'checked' : '' }}>
                <label class="form-check-label pl-1">Disable</label>
            </div>
        </div>

    </div>
    <hr>
    <h4>Website Admin Closed</h4>

    <div class="row">
        @php
            $freeze = DB::table('general_settings')->where('name','website_admin_freeze')->value('value') ?? 0;
        @endphp

        <div class="col-md-12 mb-3">
            <label class="fw-bold d-flex gap-1">Freeze Status:  &nbsp;&nbsp; 
                <input type="radio" name="website_admin_freeze" value="1" {{ $freeze == 1 ? 'checked' : '' }}>Enable &nbsp; &nbsp; <input type="radio" name="website_admin_freeze" value="0" {{ $freeze == 0 ? 'checked' : '' }}>Disable</label>
        </div>
    </div>
    <hr>
    <h4>Add Ownstock Inventry</h4>
    <div class="row">
        @php
            $ownstock = DB::table('general_settings')->where('name','add_ownstock_inventry')->value('value') ?? 0;
        @endphp
        <div class="col-md-12 mb-3">
            <label class="fw-bold d-flex gap-1">Ownstock Inventry Status:  &nbsp;&nbsp; 
                <input type="radio" name="add_ownstock_inventry" value="1" {{ $ownstock == 1 ? 'checked' : '' }}>Enable &nbsp; &nbsp; <input type="radio" name="add_ownstock_inventry" value="0" {{ $ownstock == 0 ? 'checked' : '' }}>Disable</label>
        </div>
    </div>



    <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
</form>
