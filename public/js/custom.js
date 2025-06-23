/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./resources/js/custom.js ***!
  \********************************/
function updateGrandTotal(response) {
  console.log(response);
  var subTotal = parseFloat(response.cartSubTotal.replace(',', '')) || 0;
  var vatTotal = parseFloat(response.vatTotal.replace(',', '')) || 0;
  var grandTotal = parseFloat(response.cartTotalPrice.replace(',', '')) || 0;

  // Update shipping costs dynamically
  var shippingPostcode = response.shippingPostcode || '';
  var shippingPricePerJob = parseFloat(response.shippingPricePerJob.replace(',', '')) || 0;
  var shippingPricePerTyre = parseFloat(response.shippingPricePerTyre.replace(',', '')) || 0;
  var shippingVAT = parseFloat(response.shippingVAT.replace(',', '')) || 0;

  // Update the displayed totals
  $('#totalbill h4 #sub-total').text('£' + subTotal.toFixed(2));
  $('#totalbill h4 #shippingPrice').text('£' + (shippingPricePerJob + shippingPricePerTyre).toFixed(2));
  $('#totalbill h4 #vat-total').text('£' + vatTotal.toFixed(2));
  $('#totalbill h4 #grand-total').text('£' + grandTotal.toFixed(2));
  $('.shopping-cart-total h4 #sub-total').text('£' + subTotal.toFixed(2));
  $('.shopping-cart-total h4 #shippingPrice').text('£' + (shippingPricePerJob + shippingPricePerTyre).toFixed(2));
  $('.shopping-cart-total h4 #vat-total').text('£' + vatTotal.toFixed(2));
  $('.shopping-cart-total h4 #grand-total').text('£' + grandTotal.toFixed(2));
  $('.count-style').text(response.remainingItems);
}
// Handle cart updates (increase/decrease quantity)
$(document).on('click', '.update-cart', function () {
  var id = $(this).data('id');
  var action = $(this).data('action');
  $.ajax({
    url: "{{ route('cart.update') }}",
    method: "POST",
    data: {
      _token: "{{ csrf_token() }}",
      id: id,
      action: action
    },
    success: function success(response) {
      if (response.success) {
        // console.log(response); // Debugging: Log the server response
        // Update the quantity in the UI
        var row = $('button[data-id="' + id + '"]').closest('tr');
        var quantityElement = row.find('.quantity');
        var totalElement = row.find('.total');
        var price = parseFloat(row.find('.price .amount').text().replace('£', '')) || 0;
        var taxClassId = row.data('tax-class-id') || 0;
        var currentQuantity = parseInt(quantityElement.text());
        if (action === 'increase') {
          currentQuantity++;
        } else if (action === 'decrease' && currentQuantity > 1) {
          currentQuantity--;
        }
        quantityElement.text(currentQuantity);

        // Recalculate the total for the updated item
        var vatRate = taxClassId == 9 ? 1.2 : 1;
        var itemPriceWithVAT = price * vatRate;
        var itemTotal = price * currentQuantity;

        // Update the total for the item in the UI
        totalElement.text('£' + itemTotal.toFixed(2));

        // Update the subtotal, VAT, and grand total from the server response
        updateGrandTotal(response);
      } else {
        alert(response.message || 'Failed to update the cart.');
      }
    },
    error: function error(xhr, status, _error) {
      console.error('Error:', status, _error);
    }
  });
});

// Handle item deletion
// Handle item deletion
$(document).on('click', '.delete-item', function () {
  var id = $(this).data('id');
  $.ajax({
    url: "/cart/delete",
    method: "POST",
    data: {
      _token: "{{ csrf_token() }}",
      id: id
    },
    success: function success(response) {
      if (response.success) {
        // Remove the deleted item from the UI
        $('#cart-item-' + id).remove();
        updateCartUI(response);

        // Redirect to home if the cart is empty
        if ($('#cart-items-list').children().length === 0) {
          window.location.href = "{{ route('home') }}";
        }
        $('a[data-id="' + id + '"]').closest('tr').remove();

        // Update the totals
        updateGrandTotal(response);

        // Redirect to home if the cart is empty
        if (response.remainingItems === 0) {
          window.location.href = "{{ route('home') }}";
        }
      } else {
        alert(response.message || 'Failed to delete the item.');
      }
    },
    error: function error(xhr, status, _error2) {
      console.error('AJAX Error:', {
        status: status,
        error: _error2,
        xhr: xhr
      });
    }
  });
});
/******/ })()
;