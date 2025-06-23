<!-- Stock History Modal -->
<div class="modal fade" id="stockHistoryModal" tabindex="-1" aria-labelledby="stockHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="stockHistoryModalLabel">Stock History Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="stockHistoryTable">
            <thead class="thead-dark table-sm">
                <tr>
                    <th>EAN</th>
                    <th>Reference</th>
                    <th>Reason</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically populated here -->
            </tbody>
        </table>
    </div>
    <!-- Pagination Controls -->
    <div id="paginationControls" class="d-flex justify-content-center mt-3"></div>
</div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Variables to track the current EAN and page
let currentPage = 1;
let currentEan = null;

// Function to fetch stock history data
function fetchStockHistory(ean, page) {
    // Store the current EAN globally
    currentEan = ean;

    $.ajax({
        url: '/get-stock-history',
        method: 'GET',
        data: { ean: ean, page: page },
        success: function (response) {
            $('#stockHistoryTable tbody').empty();

            if (response.data.length > 0) {
                response.data.forEach(function (record) {
                    const reasonToShow = record.reason === 'Other' ? record.other_reason : record.reason;
                    const refIdCell = record.ref_id
                        ? `<a href="AutoCare/workshop/invoice/${record.ref_id}" class="btn btn-primary btn-sm" target="_blank">${record.ref_type}-${record.ref_id}</a>`
                        : '';

                    const row = `
                        <tr>
                            <td>${record.ean}</td>
                            <td>${refIdCell}</td> <!-- Conditional rendering for ref_id -->
                            <td>${reasonToShow}</td>
                            <td>${record.stock_type}</td>
                            <td>${record.qty}</td>
                            <td>£${record.cost_price}</td>
                            <td>${record.created_at}</td>
                        </tr>
                    `;
                    $('#stockHistoryTable tbody').append(row);
                });
            } else {
                $('#stockHistoryTable tbody').append('<tr><td colspan="7">No stock history found for this EAN.</td></tr>');
            }

            updatePaginationControls(response);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching stock history:', error);
            alert('An error occurred while fetching stock history.');
        }
    });
}

// Define updatePaginationControls globally
function updatePaginationControls(response) {
    const paginationContainer = $('#paginationControls');
    paginationContainer.empty();

    const totalPages = response.last_page;
    const currentPage = response.current_page;

    // Add "Previous" button
    if (currentPage > 1) {
        paginationContainer.append(`
            <button class="btn btn-secondary me-2" onclick="fetchStockHistory('${currentEan}', ${currentPage - 1})">Previous</button>
        `);
    }

    // Add page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationContainer.append(`
            <button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'} me-2" onclick="fetchStockHistory('${currentEan}', ${i})">${i}</button>
        `);
    }

    // Add "Next" button
    if (currentPage < totalPages) {
        paginationContainer.append(`
            <button class="btn btn-secondary" onclick="fetchStockHistory('${currentEan}', ${currentPage + 1})">Next</button>
        `);
    }
}

$(document).ready(function () {
    $('.ean-link').on('click', function (e) {
        e.preventDefault();
        const ean = $(this).data('ean'); // Get the EAN from the clicked link
        currentPage = 1; // Reset to the first page
        currentEan = ean; // Set the current EAN globally
        $('#stockHistoryModal').modal('show');
        fetchStockHistory(ean, currentPage); // Fetch data for the first page
    });
});
</script>