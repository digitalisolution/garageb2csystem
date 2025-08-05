<div class="bg-white p-4 rounded">
    <div class="product-tab-list nav pt-10 pb-20 text-center">
        <a class="active" href="#search-by-reg" data-bs-toggle="tab">
            <h4>Search by Reg</h4>
        </a>
        <a href="#search-by-size" data-bs-toggle="tab">
            <h4>Search by Size</h4>
        </a>
    </div>
    <div class="tab-content jump">
        <!-- Form -->
        <div class="tab-pane active" id="search-by-reg">
            <form id="vrmSearchForm" action="/vehicle-data" method="GET">
                <div class="plate_wrap mb-3">
                    <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" width="42" height="48" loading="lazy">
                    <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="reg_number" name="vrm"
                        required>
                </div>
                <div class="text-center tyre_diagram"><img src="frontend/themes/default/img/tyrechart.jpg" width="300" height="99" loading="lazy"
                        alt="tyre chart"></div>
                <div class="hero_radio">
                    @foreach ($fittingTypes as $index => $type)
                        <div class="form-check-inline">
                            <input class="form-check-input" type="radio" name="fitting_type"
                                id="fitting_type_{{ strtolower($type->ordertype_name) }}"
                                value="{{ strtolower($type->ordertype_name) }}" required {{ $index === 0 ? 'checked' : '' }}>
                            <label class="form-check-label" for="fitting_type_{{ strtolower($type->ordertype_name) }}">
                                {{ strtoupper(str_replace('_', ' ', $type->ordertype_name)) }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <button type="submit" id="vrm-find-tyres" class="btn btn-theme btn-block">Find Tyres</button>
            </form>
        </div>

        <!-- Result Modal -->
        <x-vrm-modal id="vehicleDataModal" title="Vehicle Details">
            <div id="vehicleDataContent">
                <p>Loading...</p>
            </div>
        </x-vrm-modal>

        <div class="tab-pane" id="search-by-size">
            <form id="tyreSearchForm" action="{{ route('tyreslist') }}" method="GET"
                onsubmit="return validateTyreSearchForm()">
                <div class="search_size_area">
                    <div class="column">
                        <label>Width</label>
                        <input type="number" class="form-control" id="width" name="width" placeholder="205" required>
                    </div>
                    <div class="column">
                        <label>Profile</label>
                        <input type="number" class="form-control" id="profile" name="profile" placeholder="55" required>
                    </div>
                    <div class="column">
                        <label>Rim</label>
                        <input type="number" class="form-control" id="diameter" name="diameter" placeholder="16"
                            required>
                    </div>
                </div>
                <div class="text-center mt-2 tyre_diagram"><img src="frontend/themes/default/img/tyrechart.jpg" width="300" height="99" loading="lazy" alt="tyre chart"></div>
                <div class="mb-3">
                    <div class="hero_radio">
                        @foreach ($fittingTypes as $index => $type)
                            <div class="form-check-inline">
                                <input class="form-check-input" type="radio" name="fitting_type"
                                    id="fitting_type_{{ strtolower($type->ordertype_name) }}"
                                    value="{{ strtolower($type->ordertype_name) }}" required {{ $index === 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="fitting_type_{{ strtolower($type->ordertype_name) }}">
                                    {{ strtoupper(str_replace('_', ' ', $type->ordertype_name)) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn btn-theme btn-block">Find Tyres</button>
            </form>
        </div>
    </div>
</div>