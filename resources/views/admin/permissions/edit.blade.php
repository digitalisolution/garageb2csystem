@extends('samples')

@section('content')
<div class="container py-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white p-2">
            <h5 class="mb-1">Edit Permission</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('permissions.update', $permission->id) }}">
                <div class="row">
                @csrf
                @method('PUT')

                {{-- MODULE --}}
                <div class="col-md-6 col-12">
                        <div class="form-group mb-3">
                    <label class="form-label fw-bold">Module / Category</label>

                    <select class="form-control" id="moduleSelect">
                        <option value="">-- Select Module --</option>

                        @foreach($modules as $module)
                            <option value="{{ $module->name }}"
                                {{ $permission->module == $module->name ? 'selected' : '' }}>
                                {{ $module->label }}
                            </option>
                        @endforeach

                        <option value="custom"
                            {{ !in_array($permission->module, $modules->pluck('name')->toArray()) ? 'selected' : '' }}>
                            Other (Custom)
                        </option>
                    </select>

                    <input class="form-control" type="hidden" name="custom_module" id="customModuleHidden"
                        value="{{ !in_array($permission->module, $modules->pluck('name')->toArray()) ? $permission->module : '' }}">

                    <input class="form-control" type="hidden" name="is_custom" id="isCustomInput"
                           value="{{ !in_array($permission->module, $modules->pluck('name')->toArray()) ? 1 : '' }}">

                    {{-- Custom module field --}}
                    <input type="text" id="customModuleInput"
                           class="form-control mt-2 {{ !in_array($permission->module, $modules->pluck('name')->toArray()) ? '' : 'd-none' }}"
                           value="{{ !in_array($permission->module, $modules->pluck('name')->toArray()) ? $permission->module : '' }}"
                           placeholder="Enter custom module name">
                </div>
            </div>

                {{-- PERMISSION NAME --}}
                <div class="col-md-6 col-12">
                        <div class="form-group mb-3">
                    <label class="form-label fw-bold">Permission Slug</label>

                    <div class="input-group">
                        <span class="input-group-text bg-light btn-sm" id="modulePrefix">{{ $permission->module }}</span>
                        <input type="text" class="form-control" id="actionInput"
                            value="{{ explode('.', $permission->name)[1] }}">
                        <input type="hidden" name="module" id="moduleInput" value="{{ $permission->module }}">
                    </div>

                    <input type="hidden" name="name" id="finalPermission" value="{{ $permission->name }}">

                    <small class="text-muted">
                        Final permission:
                        <span id="previewText" class="fw-bold text-success">{{ $permission->name }}</span>
                    </small>
                </div>
            </div>

            {{-- ICON --}}
                <div class="col-md-6 col-12">
                        <div class="form-group">
                <input type="text" name="icon" class="form-control mb-3" value="{{ $permission->icon }}">
            </div>
        </div>

                {{-- DESCRIPTION --}}
                <div class="col-md-6 col-12">
                        <div class="form-group">
                <textarea name="description" class="form-control mb-3">{{ $permission->description }}</textarea>
            </div>
        </div>

                <div class="col-12"><button class="btn btn-success">Update Permission</button></div>
            </div>
            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const moduleSelect      = document.getElementById("moduleSelect");
    const customModuleInput = document.getElementById("customModuleInput");
    const customModuleHidden = document.getElementById("customModuleHidden");
    const isCustomInput     = document.getElementById("isCustomInput");

    function updatePreview() {

        let module = moduleSelect.value.toLowerCase();
        let action = document.getElementById("actionInput").value.toLowerCase();

        if (module === "custom") {
            module = customModuleInput.value.toLowerCase();
            customModuleHidden.value = module;
        }

        if (module && action) {
            document.getElementById("moduleInput").value = module;
            document.getElementById("finalPermission").value = module + "." + action;
            document.getElementById("previewText").textContent = module + "." + action;
            document.getElementById("modulePrefix").innerText = module;
        }
    }

    moduleSelect.addEventListener("change", function () {
        if (this.value === "custom") {
            customModuleInput.classList.remove("d-none");
            isCustomInput.value = 1;
        } else {
            customModuleInput.classList.add("d-none");
            customModuleInput.value = "";
            customModuleHidden.value = "";
            isCustomInput.value = "";
        }
        updatePreview();
    });

    document.getElementById("actionInput").addEventListener("keyup", updatePreview);
    customModuleInput.addEventListener("keyup", updatePreview);

});
</script>

@endsection
