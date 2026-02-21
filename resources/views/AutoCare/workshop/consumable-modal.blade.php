@if($moduleConsumableEnabled)
<div id="consumableModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="consumableModalLabel" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consumableModalLabel">Select Consumables</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="consumableTable">
                    <div id="consumableList">
                        Loading consumables...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="openAddConsumableFromList">+ Add New Consumable</button>
                <button type="button" class="btn btn-dark" id="refreshConsumableList"><i class="fa fa-dot-circle-o"></i> Refresh List</button>
                <button type="button" class="btn btn-primary" id="addSelectedConsumables"><i class="fa fa-check"></i> Add Selected Consumables</button>
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
