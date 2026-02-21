@if($moduleConsumableEnabled)
<div class="modal fade" id="addEditConsumableModal" tabindex="-1" role="dialog"
    aria-labelledby="addEditConsumableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEditConsumableModalLabel">Add/Edit Consumable</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="alert alert-danger d-none" id="consumableModalErrorAlert">
                        <ul id="consumableModalErrorList" class="mb-0"></ul>
                    </div>
                    <div class="alert alert-success d-none" id="consumableModalSuccessAlert">
                        <span id="consumableModalSuccessMessage"></span>
                    </div>

                    <form id="consumableForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="POST" id="consumableFormMethod">

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="modal_consumable_name">Name *</label>
                                <input type="text" name="consumable_name" id="modal_consumable_name" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="modal_cost_price">Cost Price *</label>
                                <input type="number" name="cost_price" id="modal_cost_price" class="form-control" required
                                    step="0.01" min="0">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="modal_tax_class_id">VAT:</label>
                                <select name="tax_class_id" id="modal_tax_class_id" class="form-control" required>
                                    <option value="9">VAT</option>
                                    <option value="0">No Vat</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12 form-group">
                                <label for="modal_status">Status</label>
                                <select name="status" id="modal_status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                             <div class="col-lg-6 col-md-6 col-12 form-group d-none">
                                <label for="modal_display_status">Display Status</label>
                                <select name="display_status" id="modal_display_status" class="form-control" required>
                                    <option value="1" selected>Displayed</option>
                                    <option value="0">Hidden</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 form-group">
                                <label for="modal_content">Content</label>
                                <textarea name="content" rows="4" id="modal_content" class="form-control"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                        </div>
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveConsumableBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endif