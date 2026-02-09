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
