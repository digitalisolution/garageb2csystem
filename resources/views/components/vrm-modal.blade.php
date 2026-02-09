@php
    $id = 'vehicleDataModal';
    $title = 'Vehicle Details';
@endphp

<div class="modal fade" id="vehicleDataModal" tabindex="-1" aria-labelledby="vehicleDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="vehicleDataModalLabel">Confirm your tyre size</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="tyre-result p-3">
                                <h4>Your Vehicle</h4>
                                <div id="vehicleDataContent">
                                    <p>Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div id="tyreSizeSelection" class="p-3" style="display: none;">
                                <h4>Select Tyre Size</h4>
                                <div class="tyre-size-container" id="tyreSizeSelect" aria-label="Select Tyre Size">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 hidden-xs">
                      <h4><a class="toggle tyre_exp">How to find my tyre size <i class="fa fa-chevron-down"></i></a></h4>

                    <div class="collapse" id="show-hide">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="mt-3 text-center">
                                    <img src="frontend/themes/default/img/tyre_chart.png" alt="tyre chart" loading="lazy" height="170" width="auto">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <p>We endeavour to ensure we are displaying the correct tyres for your vehicle. However, we recommend all customers check the tyre size printed on the side wall of their tyres before proceeding with a tyre purchase as occasionally discrepancies do occur. By selecting the tyre sizes above and clicking 'Continue', you are indicating that you have checked your tyre sizes.</p>
                            </div>
                        </div>
            </div>
            </div>
            </div>
            

            <div class="modal-footer px-4">
                <button type="button" id="continueButton" class="btn btn-theme" style="display: none;">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$('.toggle').click(function() {
    $('#show-hide').toggle('fade');
});</script>