<div class="box-body">
    @if ($errors->any())
        <ul class="alert alert-danger" style="list-style:none">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if (session()->has('message.level'))
        <div class="alert alert-{{ session('message.level') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h6><i class="icon fa fa-check"></i> {{ ucfirst(session('message.level')) }}!</h6>
            {!! session('message.content') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="due_in">Due In <span class="text-red">*</span></label>
                {{ Form::input('datetime-local', 'due_in', old('due_in', $selectedDueIn ?? \Carbon\Carbon::now('Europe/London')->format('Y-m-d\TH:i')), [
                    'class' => 'form-control ' . ($errors->has('due_in') ? 'is-invalid' : ''),
                    'id' => 'due_in',
                    'required' => true,
                ]) }}
                @error('due_in')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="due_out">Due Out <span class="text-red">*</span></label>
                {{ Form::input('datetime-local', 'due_out', old('due_out', $selectedDueOut ?? \Carbon\Carbon::now('Europe/London')->addHours(2)->format('Y-m-d\TH:i')), [
                    'class' => 'form-control ' . ($errors->has('due_out') ? 'is-invalid' : ''),
                    'id' => 'due_out',
                    'required' => true,
                ]) }}
                @error('due_out')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <label class="control-label">&nbsp;</label>
            <button type="button" id="goToWorkshopBtn" class="btn btn-primary btn-round btn-block">
                + Create Workshop
            </button>
        </div>
    </div>
</div>

<script>
    document.getElementById('goToWorkshopBtn').addEventListener('click', function() {
        const dueIn = document.getElementById('due_in').value;
        const dueOut = document.getElementById('due_out').value;

        if (!dueIn || !dueOut) {
            alert('Please select both Due In and Due Out dates.');
            return;
        }

        const url = '{{ route("AutoCare.workshop.create") }}?due_in=' + encodeURIComponent(dueIn) + '&due_out=' + encodeURIComponent(dueOut);
        window.open(url, '_self');
    });

    document.getElementById('due_in').addEventListener('change', function() {
        const dueIn = this.value;
        if (dueIn) {
            const dueOutInput = document.getElementById('due_out');
            const dueInDate = new Date(dueIn);
            dueInDate.setHours(dueInDate.getHours() + 2);
            dueOutInput.value = dueInDate.toISOString().slice(0, 16);
        }
    });
</script>