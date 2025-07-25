@php
    $id = 'svehicleDataModal'; // Unique ID for the modal
    $title = 'Vehicle Details'; // Title of the modal
@endphp
<!-- Modal for Vehicle Data and Tyre Size Selection -->
<div class="modal fade" id="svehicleDataModal" tabindex="-1" aria-labelledby="svehicleDataModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="svehicleDataModalLabel"><strong>Confirm your vehicle details</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 px-4">
                <div class="vehicle-result">
                    <h5><strong>Your Vehicle</strong></h5>
                    <div class="bg-light py-4 px-4 mb-3 border rounded">
                        <div class="your_vehicle_result d-flex justify-content-between align-items-center">
                            <div class="vrm_plate d-flex align-items-center">
                                <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" width="auto" height="35" loading="lazy">
                                <span class="ms-2 text-uppercase">av07gvk</span>
                            </div>
                           <div id="brandImageContainer"><img class="default-img" alt="Brand Logo" height="50" src="undefined"></div>
                        </div>
                        <div class="your_vehicle_data mt-4 d-flex flex-wrap gap-3">
                            <div class="item">
                                Model
                                <span>Galaxy Ghia TDCi 6g</span>
                            </div>
                            <div class="item">
                                Year
                                <span>2007</span>
                            </div>
                            <div class="item">
                                Engine Capacity
                                <span>1997 CC</span>
                            </div>
                            <div class="item">
                                Fuel
                                <span>Diesel</span>
                            </div>
                        </div>
                    </div>
                    <div id="serviceVehicleDataContent">
                        <!-- Content will be dynamically inserted here -->
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="serviceContinueButton" class="btn btn-theme" style="display: none;">Continue
                    to
                    Service</button>
            </div>
        </div>
    </div>
</div>
