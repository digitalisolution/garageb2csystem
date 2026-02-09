<div id="tyreModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tyreModalLabel">
    <div class="modal-dialog" style="max-width:90% !important" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tyreModalLabel">Select a Tyre</h5>
                <a href="{{ route('AutoCare.tyres.edit', ['product_id' => 'new']) }}" class="btn btn-primary ml-3" target="_blank">Add Tyre</a>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Advanced search filter -->
                <div id="searchContainer" class="mb-3">
                    <div class="add_tyres_filter">
                        <div class="item"><input type="text" id="widthFilter" class="form-control" placeholder="Width">
                        </div>
                        <div class="item"><input type="text" id="profileFilter" class="form-control"
                                placeholder="Profile"></div>
                        <div class="item"><input type="text" id="diameterFilter" class="form-control"
                                placeholder="Diameter"></div>
                        <div class="item"><input type="text" id="eanFilter" class="form-control" placeholder="EAN">
                        </div>
                        <!-- <div class="item"><input type="text" id="skuFilter" class="form-control" placeholder="SKU"> -->

                        <div class="item"><input type="text" id="brandFilter" class="form-control" placeholder="Brand">
                        </div>
                        <div class="item">
                            <select id="supplier" class="form-control">
                                <option value="">-- Select Supplier --</option>
                            </select>
                        </div>
                        <div class="item">
                            <input type="checkbox" id="rftFilter" value="1"> <label for="rftFilter">Runflat</label>
                        </div>

                        <div class="item">
                            <select id="fittingtype" class="form-control">
                            </select>
                        </div>
                        <!-- <div class="item"><input type="text" id="priceFilter" class="form-control" placeholder="Price"> -->
                        <!-- </div> -->
                        <div class="item"><button id="searchButton"
                                class="btn btn-primary btn-sm btn-block">Search</button>
                        </div>

                    </div>
                </div>

                <!-- Tyre list -->
                <div id="tyreListContainer">
                    <table class="table table-hover">
                        <thead class="thead-dark text-capitalize">
                            <tr>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Eco</th>
                                <th>disfr</th>
                                <th>db</th>
                                <th>Vehicle Type</th>
                                <th>Runflat</th>
                                <th>Season</th>
                                <th>available</th>
                                <th>Price</th>
                                <th>SP inc. VAT</th>
                                <th>Trade Price</th>
                                <th>Supplier</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody id="tyreList">
                            <!-- List of tyres will be populated here -->
                        </tbody>
                    </table>
                    <div id="paginationControls" class="pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>