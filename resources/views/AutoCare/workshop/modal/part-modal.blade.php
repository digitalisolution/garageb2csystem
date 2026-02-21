@if($modulePartEnabled)
<div id="partModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="partModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partModalLabel">Select parts</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="partTable">
                    <div id="partList">
                        Loading parts...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="openAddPartFromList">+ Add New Part</button>
                <button type="button" class="btn btn-dark" id="refreshPartList"><i class="fa fa-dot-circle-o"></i> Refresh List</button>
                <button type="button" class="btn btn-primary" id="addSelectedParts"><i class="fa fa-check"></i> Add Selected Parts</button>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .table_box{display:flex;gap:30px;justify-content:space-between;border-bottom:solid 1px rgba(0,0,0,0.05);padding:10px;}
    .table_box:hover{background:rgba(0,0,0,0.05);}
    .table_box .item{width:25%;}
    
</style>
@endif
