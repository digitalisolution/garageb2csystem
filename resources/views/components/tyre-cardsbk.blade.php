<div class="row">
    @if($tyres && $tyres->count() > 0)
        @foreach ($tyres as $tyre)
            <div class="col-md-4 mb-4">
                <div class="card" style="width: 18rem;">
                    <img src="{{ $tyre->image_url ?? 'https://via.placeholder.com/150' }}" class="card-img-top"
                        alt="{{ $tyre->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tyre->title }}</h5>
                        <p class="card-text">
                            SKUs: {{ $tyre->sku }}<br>
                            EAN: {{ $tyre->ean }}<br>
                            Brand: {{ $tyre->brand }}<br>
                            desc: {{ $tyre->description }}<br>
                            Price: ${{ $tyre->price }}<br>
                            Width: {{ $tyre->tyre_width }}<br>
                            Profile: {{ $tyre->tyre_profile }}<br>
                            Diameter: {{ $tyre->tyre_diameter }}
                        </p>
                        <a href="#" id="add-to-cart" class="btn btn-primary add-to-cart" data-id="{{ $tyre->product_id }}">
                            Add to Cart
                        </a>

                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p>No Tyres available at the moment.</p>
    @endif
</div>