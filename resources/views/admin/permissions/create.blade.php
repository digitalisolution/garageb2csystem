@extends('samples')

@section('content')
    <div class="container py-4">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white p-2">
                <h5 class="mb-1">Create New Permission</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('permissions.store') }}">
                    <div class="row">
                    @csrf
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                        <label class="form-label fw-bold">Module / Category</label>
                        <select class="form-control" id="moduleSelect">
                            <option value="">-- Select Module --</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->name }}">{{ $module->label }}</option>
                            @endforeach

                            <option value="custom">Other (Create New)</option>
                        </select>
                         <input type="hidden" name="custom_module" id="customModuleHidden">
                         <input type="hidden" name="is_custom" id="isCustomInput">
                        {{-- Custom module input --}}
                        <input type="text" id="customModuleInput" class="form-control mt-2 d-none"
                            placeholder="Enter new module name">

                        <small class="text-muted">Choose the module this permission belongs to.</small>
                    </div>
                </div>


                    {{-- PERMISSION NAME --}}
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                        <label class="form-label fw-bold">Permission Name (Slug)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light btn-sm" id="modulePrefix">module</span>
                            <input type="text" class="form-control" id="actionInput" placeholder="create / edit / delete" />
                            <input type="hidden" name="module" id="moduleInput">
                        </div>
                        <input type="hidden" name="name" id="finalPermission" />
                        <small class="text-muted">Final generated permission:
                            <span id="previewText" class="fw-bold text-success">module.action</span>
                        </small>
                    </div>
                </div>

                    {{-- AUTO CRUD BUTTONS --}}
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                        <label class="form-label fw-bold">Quick Create</label>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="create">Create</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="view">View</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="edit">Edit</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="delete">Delete</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="install">Install</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="uninstall">Uninstall</button>
                            <button type="button" class="btn btn-primary btn-sm autoCrudBtn"
                                data-action="buy">Buy</button>
                        </div>
                        <small class="text-muted">Click to auto-generate permissions instantly.</small>
                    </div>
                </div>

                    {{-- ICON --}}
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                        <label class="form-label fw-bold">Icon (Optional)</label>
                        <input name="icon" type="text" class="form-control"
                            placeholder="e.g. fa fa-user, la la-edit, etc" />
                    </div>
                </div>

                    {{-- DESCRIPTION --}}
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Explain what this permission does (optional)"></textarea>
                    </div>
                </div>
                <div class="col-12">
                    {{-- SUBMIT --}}
                    <button class="btn btn-primary mt-2">Create Permission</button>
                </div>
                </div>
                </form>

            </div>
        </div>
    </div>

    <script>
       document.addEventListener("DOMContentLoaded", function () {

    const moduleSelect = document.getElementById('moduleSelect');
    const customModuleInput = document.getElementById('customModuleInput');
    const customModuleHidden = document.getElementById('customModuleHidden');

    function updatePreview() {
        let module = moduleSelect.value.toLowerCase();
        let action = document.getElementById('actionInput').value.toLowerCase();

        // If "custom" selected, use custom module input
        if (module === "custom") {
            let custom = customModuleInput.value.toLowerCase();
            module = custom;
            customModuleHidden.value = custom;
        }

        if (module && action) {
            document.getElementById('moduleInput').value = module;
            document.getElementById('previewText').textContent = module + "." + action;
            document.getElementById('finalPermission').value = module + "." + action;
            document.getElementById('modulePrefix').innerText = module;
        }
    }
moduleSelect.addEventListener('change', function () {

    if (this.value === "custom") {
        customModuleInput.classList.remove("d-none");
        isCustomInput.value = "1";
    } else {
        customModuleInput.classList.add("d-none");
        customModuleInput.value = "";
        customModuleHidden.value = "";
        isCustomInput.value = "";
    }

    updatePreview();
});

    // When module dropdown changes
    // moduleSelect.addEventListener('change', function () {
    //     if (this.value === "custom") {
    //         customModuleInput.classList.remove("d-none");
    //     } else {
    //         customModuleInput.classList.add("d-none");
    //         customModuleInput.value = "";
    //         customModuleHidden.value = "";
    //     }

    //     updatePreview();
    // });

    // Update preview when typing action or custom module
    document.getElementById('actionInput').addEventListener('keyup', updatePreview);
    customModuleInput.addEventListener('keyup', updatePreview);

    // Auto CRUD Buttons
    document.querySelectorAll('.autoCrudBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('actionInput').value = this.dataset.action;
            updatePreview();
        });
    });

});

    </script>

@endsection