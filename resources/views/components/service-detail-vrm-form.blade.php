<div class="bg-white p-4 rounded">
    <div class="product-tab-list nav pt-10 pb-20 text-center">
        <a class="active" href="#servicebyreg" data-bs-toggle="tab">
            <h4>Service by Reg</h4>
        </a>
        <a href="#servicebymake" data-bs-toggle="tab">
            <h4>Service by Make</h4>
        </a>
    </div>
    <div class="tab-content jump">
        <!-- Form -->
        <div class="tab-pane active" id="servicebyreg">
            <form id="vrmSeviceForm" action="/vehicle-data" method="GET">
                <div class="plate_wrap mb-3">
                    <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon">
                    <input class="vehicle_plate" type="text" placeholder="Vehicle Reg" id="reg_service_number"
                        name="vrm" required>
                </div>
                <button type="submit" class="btn btn-theme btn-block">Find Services</button>
            </form>
        </div>

        <x-servicevrm-modal id="serviceVehicleDataModal" title="Vehicle Details">
            <div id="serviceVehicleDataContent">
                <p>Loading...123</p>
            </div>
        </x-servicevrm-modal>

        <x-search-car-service />
    </div>
</div>
