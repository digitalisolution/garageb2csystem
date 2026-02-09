@if ($services && $services->count() > 0)
    @php
        $domain = str_replace(['http://', 'https://'], '', request()->getHost());
        $customImagePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service_icon/';
        $defaultImagePath = 'frontend/themes/default/img/service/';
    @endphp

    <div class="product-area pt-80 pb-60 bg-gray-5">
        <div class="container">
            <div class="section-title text-center mb-55">
                <h2>Expert Services</h2>
            </div>

            <div class="product-slider-active owl-carousel owl-dot-none">

                @foreach ($services as $service)
                    @php
                        $imageName = $service->image;

                        $customImage = $imageName ? public_path($customImagePath . $imageName) : null;
                        $defaultImage = $imageName ? public_path($defaultImagePath . $imageName) : null;
                        $fallbackNoImage = public_path($defaultImagePath . 'no-image.png');

                        if ($imageName && file_exists($customImage)) {
                            $finalImage = asset($customImagePath . $imageName);
                        } elseif ($imageName && file_exists($defaultImage)) {
                            $finalImage = asset($defaultImagePath . $imageName);
                        } else {
                            $finalImage = asset($defaultImagePath . 'no-image.png');
                        }
                    @endphp

                    <div class="product-wrap-2 mb-25">
                        <a href="{{ route('slug.handle', $service->slug) }}">
                            <div class="product-img">
                                <img class="default-img" src="frontend/themes/default/img/service/sbg.webp"
                                    alt="service background" loading="lazy" width="460" height="261">
                                <img class="hover-img" src="frontend/themes/default/img/service/sbg.webp"
                                    alt="service background" loading="lazy" width="460" height="261">
                            </div>
                            <div class="product-content-2">
                                <h3>{{ $service->name }}</h3>
                                <div class="price-2 mb-3"></div>

                                <!-- Render image with fallback -->
                                <img src="{{ $finalImage }}" alt="{{ $service->name }}" class="img-bank" onerror="this.onerror=null; this.src='{{ asset('frontend/themes/default/img/service/no-image.png') }}';">
                            </div>
                        </a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endif