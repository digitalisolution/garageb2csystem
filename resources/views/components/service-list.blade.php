@if ($services && $services->count() > 0)
    <div class="product-area pt-80 pb-60 bg-gray-5">
        <div class="container">
            <div class="section-title text-center mb-55">
                <h2>Expert Services</h2>
            </div>
            <div class="product-slider-active owl-carousel owl-dot-none">
                @foreach ($services as $service)
                    <div class="product-wrap-2 mb-25">
                        <a href="{{ route('slug.handle', $service->slug) }}">
                            <div class="product-img">
                                <img class="default-img" src="frontend/themes/default/img/service/sbg.webp" alt="service background" loading="lazy" width="460" height="261">
                                <img class="hover-img" src="frontend/themes/default/img/service/sbg.webp" alt="service background" loading="lazy" width="460" height="261">
                            </div>
                            <div class="product-content-2">
                                <h3>{{ $service->name }}</h3>
                                <div class="price-2 mb-3">
                                </div>
                                <img src="frontend/themes/default/img/service/{{ $service->image }}" alt="{{ $service->name }}" alt="icon" loading="lazy" width="56" height="56">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif