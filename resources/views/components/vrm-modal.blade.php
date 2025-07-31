@php
    $id = 'vehicleDataModal';
    $title = 'Vehicle Details';
@endphp
<div class="modal fade" id="vehicleDataModal" tabindex="-1" aria-labelledby="vehicleDataModalLabel" aria-hidden="true"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="vehicleDataModalLabel"><strong>Confirm your tyre size</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="py-3 px-4">
                <div class="tyre-result">
                    <h5><strong>Your Vehicle</strong></h5>
                    <div id="vehicleDataContent">
                        <p>Loading...</p>
                    </div>
                </div>
                <div id="tyreSizeSelection" class="mt-4" style="display: none;">
                    <h5><strong>Select Tyre Size</strong></h5>
                    <select class="tyre-select" id="tyreSizeSelect" aria-label="Select Tyre Size">
                    </select>
                </div>
                </div>
            </div>
            <div class="px-5">
                <div class="row align-items-center">
                    <div class="col-lg-5">
                        <div class="mt-3 text-center"><img src="frontend/themes/default/img/tyre_chart.png"
                                alt="tyre chart" loading="lazy" height="170" width="auto"></div>
                    </div>
                    <div class="col-lg-7">
                        <p>We endeavour to ensure we are displaying the correct tyres for your vehicle. However, we
                            recommend all customers check the tyre size printed on the side wall of their tyres before
                            proceeding with a tyre purchase as occasionally discrepancies do occur. By selecting the
                            tyre sizes above and clicking 'Continue', you are indicating that you have checked your tyre
                            sizes.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="continueButton" class="btn btn-theme" style="display: none;">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

