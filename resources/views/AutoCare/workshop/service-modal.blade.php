<div id="serviceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel">
    <div class="modal-dialog" style="max-width:90% !important" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Select Services</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="serviceTable">
                    <div id="serviceList">
                        Loading services...
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addSelectedServices">Add Selected Services</button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .table_box{display:flex;gap:30px;justify-content:space-between;border-bottom:solid 1px rgba(0,0,0,0.05);padding:10px;}
    .table_box:hover{background:rgba(0,0,0,0.05);}
    .table_box .service-checkbox{width:17px; height:17px;}
    .table_box .servicename{width:70%;}
    .table_box .cost{width:15%;}
    .table_box .check{width:15%;text-align:right;}
</style>