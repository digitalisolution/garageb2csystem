@props(['garage', 'services'])

<div class="garage-checkout-services-component">
    @if($services && $services->count() > 0)
    <h4>Available Services</h4>
        <div class="service_bank">
            @foreach ($services as $service)
                @php
                    // --- Image Handling ---
                    $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                    $customImagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon/';
                    $defaultImagePath = 'frontend/themes/default/img/service/';
                    $custominnerImagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-inner-img/';
                    $defaultinnerImagePath = 'frontend/themes/default/img/service-inner-img/';

                    $imageName = $service->image;
                    $innerimageName = $service->inner_image;

                    $customImage = $imageName ? public_path($customImagePath . $imageName) : null;
                    $defaultImage = $imageName ? public_path($defaultImagePath . $imageName) : null;

                    $custominnerImage = $innerimageName ? public_path($custominnerImagePath . $innerimageName) : null;
                    $defaultinnerImage = $innerimageName ? public_path($defaultinnerImagePath . $innerimageName) : null;

                    if ($innerimageName && file_exists($custominnerImage)) {
                        $finalImage = versioned_asset($custominnerImagePath . $innerimageName);
                    } elseif ($innerimageName && file_exists($defaultinnerImage)) {
                        $finalImage = versioned_asset($defaultinnerImagePath . $innerimageName);
                    } elseif ($imageName && file_exists($customImage)) {
                        $finalImage = versioned_asset($customImagePath . $imageName);
                    } elseif ($imageName && file_exists($defaultImage)) {
                        $finalImage = versioned_asset($defaultImagePath . $imageName);
                    } else {
                        $finalImage = versioned_asset($defaultImagePath . 'no-image.webp');
                    }

    $cart = session('cart', []);
    $cartKey = 'service_' . $service->service_id;
    $inCart = isset($cart[$cartKey]);

                @endphp

                <div class="service_card">
                    <img src="{{ $finalImage }}" alt="{{ $service->name }}" class="service_image"
                         onerror="this.onerror=null; this.src='{{ versioned_asset('frontend/themes/default/img/service/no-image.webp') }}';">

                    <h4>{{ $service->name }}</h4>

                    <div class="mt-4">
                        @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                            <span class="cost_price">
                                &pound;{{ $service->tax_class_id == 9 ? number_format($service->cost_price * 1.20, 2) : number_format($service->cost_price, 2) }}
                            </span>
                        @elseif($service->price_type == 'free')
                            <span class="cost_price">Free</span>
                        @endif
                    </div>

                    <p><i class="pe-7s-timer"></i> Work Time</p>

                    <div class="mt-4">
                        <!-- Details Link -->
                        <a href="{{ route('slug.handle', $service->slug) }}" class="btn btn-dark">View Details</a>

                        <!-- Action Buttons based on Price Type -->
                        @if($service->price_type == 'fixed-price' && $service->cost_price > 0)
                            <a href="javascript:void(0);" 
                               class="btn btn-theme-select add-to-cart-btn {{ $inCart ? 'added' : '' }}"
                               data-id="{{ $service->service_id }}"
                               data-name="{{ $service->name }}"
                               data-price="{{ $service->cost_price }}"
                               data-type="service"
                               @if($inCart) disabled @endif>
                               {{ $inCart ? 'Added' : 'Add to Cart' }}
                            </a>
                        @elseif($service->price_type == 'free')
                            <a href="javascript:void(0);" 
                               class="btn btn-theme-select add-to-cart-btn {{ $inCart ? 'added' : '' }}"
                               data-id="{{ $service->service_id }}"
                               data-name="{{ $service->name }}"
                               data-price="0"
                               data-type="service"
                               @if($inCart) disabled @endif>
                               {{ $inCart ? 'Added' : 'Add to Cart' }}
                            </a>
                        @elseif($service->price_type == 'call-now')
                            <a href="tel:{{ $garage->garage_mobile ?? $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                        @elseif($service->price_type == 'quote-now')
                            <a href="javascript:void(0);" class="btn btn-theme-select btn-enquiry-modal"
                               data-id="{{ $service->service_id }}"
                               data-name="{{ $service->name }}"
                               data-bs-toggle="modal"
                               data-bs-target="#quoteEnquiryModal">
                                Quote Now
                            </a>
                        @else
                            <a href="tel:{{ $garage->garage_mobile ?? $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@once
<style>
.add-to-cart-btn.added {
    background-color: #28a745 !important;
    color: #fff !important;
    border-color: #28a745 !important;
    cursor: not-allowed;
}
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.garage-checkout-services-component .add-to-cart-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (this.classList.contains('added')) return;

            const itemId = this.getAttribute('data-id');
            const itemType = this.getAttribute('data-type');
            const serviceName = this.getAttribute('data-name');
            const fitting_type = 'fully_fitted';
            const itemPrice = this.getAttribute('data-price');
            const btn = this;

            const originalText = btn.textContent;
            btn.textContent = 'Adding...';
            btn.disabled = true;

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: itemId,
                    type: itemType,
                    qty: 1,
                    serviceName: serviceName,
                    fitting_type: fitting_type,
                    itemPrice: itemPrice,
                    garage_id: {{ $garage->id ?? 'null' }}
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = 'Added';
                    btn.classList.add('added');
                    btn.disabled = true;

                    // Update cart UI
                    const countElements = document.querySelectorAll('.count-style, .cart-count');
                    countElements.forEach(el => el.textContent = data.totalQuantity);

                    const totalElements = document.querySelectorAll('.shop-total, .cart-total');
                    totalElements.forEach(el => el.textContent = '£' + data.cartTotalPrice);

                    Swal.fire({
                        title: 'Service added!',
                        text: serviceName + ' has been added to your cart.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to add.');
                }
            })
            .catch(err => {
                console.error(err);
                btn.textContent = originalText;
                btn.disabled = false;
                Swal.fire('Error', 'Could not add service. Please try again.', 'error');
            });
        });
    });
});
</script>
@endpush
@endonce
