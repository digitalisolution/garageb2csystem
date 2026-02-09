<!-- Modal for Customer Selection -->
<div class="modal fade" id="customerSelectionModal" tabindex="-1" role="dialog" aria-labelledby="customerSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerSelectionModalLabel">Select Customer</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="customerList" class="list-group">
                    <!-- Customer options will be dynamically populated here -->
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
   document.getElementById('lookupButton').addEventListener('click', async function () {
    const lookupButton = document.getElementById('lookupButton');
    const vrm = document.getElementById('vehicle_reg_number').value.trim();
    lookupButton.disabled = true;
    lookupButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Fetching...';

    if (!vrm) {
        alert('Please enter a valid VRM.');
        lookupButton.disabled = false;
        lookupButton.innerHTML = 'Lookup';
        return;
    }

    try {
        // Fetch related customers
        const customerResponse = await fetch(`${window.location.origin}/get-customers-by-vehicle?vehicle_reg_number=${vrm}`);
        const customerResult = await customerResponse.json();
        if (customerResponse.ok && customerResult.length > 0) {
            // Show the modal and populate the customer list
            const customerList = document.getElementById('customerList');
            customerList.innerHTML = ''; // Clear existing list

            customerResult.forEach(customer => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.innerHTML = `
                    ${customer.customer_name} (${customer.customer_email || 'No Email'})
                    <button class="btn btn-sm btn-primary select-customer" data-customer-id="${customer.id}">
                        Select
                    </button>
                `;
                customerList.appendChild(listItem);
            });

            // Show the modal
            const customerSelectionModal = new bootstrap.Modal(document.getElementById('customerSelectionModal'));
            customerSelectionModal.show();
        }
    } catch (error) {
        console.error('Fetch Error:', error.message);
        alert('An error occurred while fetching customer details.');
    } finally {
        lookupButton.disabled = false;
    }
});
</script>
<script>
document.addEventListener('click', async function (event) {
    if (event.target.classList.contains('select-customer')) {
        const customerId = event.target.getAttribute('data-customer-id');

        try {
            // Fetch customer details
            const customerResponse = await fetch(`${window.location.origin}/ajax/getCustomerForWorkshop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ customer_id: customerId }),
            });

            const customerResult = await customerResponse.json();

            if (customerResponse.ok) {
                // Extract customer details
                const customer = customerResult;

                // Auto-fill contact details
                document.querySelector('[name="name"]').value = customer.customer_name || '';
                document.querySelector('[name="email"]').value = customer.customer_email || '';
                document.querySelector('[name="mobile"]').value = customer.customer_contact_number || '';
                document.querySelector('[name="company_name"]').value = customer.company_name || '';

                // Auto-fill address details
                document.querySelector('[name="shipping_address_street"]').value = customer.shipping_address_street || '';
                document.querySelector('[name="shipping_address_city"]').value = customer.shipping_address_city || '';
                document.querySelector('[name="shipping_address_postcode"]').value = customer.shipping_address_postcode || '';
                document.querySelector('[name="shipping_address_county"]').value = customer.shipping_address_county || '';
                document.querySelector('[name="shipping_address_country"]').value = customer.shipping_address_country || '';

                // Set fields to readonly
                document.querySelector('[name="name"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="email"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="mobile"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="company_name"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="shipping_address_street"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="shipping_address_city"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="shipping_address_postcode"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="shipping_address_county"]').setAttribute('readonly', 'readonly');
                document.querySelector('[name="shipping_address_country"]').setAttribute('readonly', 'readonly');

                // Close the modal
                document.activeElement.blur();
                document.querySelector('#customerSelectionModal .btn-close').click();

                
            } else {
                alert('Failed to fetch customer details.');
            }
        } catch (error) {
            console.error('Error fetching customer details:', error.message);
            alert('An error occurred while fetching customer details.');
        }
    }
});
</script>