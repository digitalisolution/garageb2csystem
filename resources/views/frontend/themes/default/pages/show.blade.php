@extends('layouts.app')

@section('content')

        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/banner/content-pages/';
            $fallbackPath = 'frontend/themes/default/img/banner/content-pages/';

            $bannerimagePath = $page->page_banner_path ?? $page->slug.'.jpg';
            $bannerImageUrl = asset($bannerPath . $bannerimagePath);
            $fallbackImageUrl = asset($fallbackPath . $bannerimagePath);
        @endphp
@if(isset($page) && isset($page->schema_status) && $page->schema_status)
<script type='application/ld+json'>
{
  "@context": "http://www.schema.org",
  "@type": "TireShop",
  "name": "{{ $garage->garage_name }}",
  "url": "{{ $garage->website_url }}",
  "logo": "{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}",
   @if($page->page_banner_path)
  "image": "{{ $bannerImageUrl }}",
  @endif
  "description": "{{ $page->meta_description }}",
  "address": {
     "@type": "PostalAddress",
     "streetAddress": "{{ $garage->street }}",
     "addressRegion": "{{ $garage->city }}",
     "postalCode": "{{ $garage->zone }}",
     "addressCountry": "United Kingdom"
  },
  "geo": {
     "@type": "GeoCoordinates",
     "latitude": "{{ $garage->latitude }}",
     "longitude": "{{ $garage->longitude }}"
  },
  "hasMap": "{{ $garage->google_map_link }}",
  "openingHours": "{{ strip_tags($garage->garage_opening_time) }}",
  "telephone": "{{ $garage->phone }}"
}
</script>
@endif
<div class="breadcrumb-area brand_breadcrumb">
     @if($page->page_banner_path)
    <img src="{{ $bannerImageUrl }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $page->title }}" class="img-bank">
    @else
    <div></div>
    @endif
    <div class="brand_name">
        <h1>{{ $page->title }}</h1>
    </div>
</div>

@if(isset($page->tyre_search_form) && $page->tyre_search_form === 1)
    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3 {{ $page->slug }}">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="text-uppercase">Grip the Road with Confidence!</h2>
                    <h4>Find the right tyre for your vehicle.</h4>
                </div>
                <div class="col-md-4"><a href="#method2" data-bs-toggle="collapse" class="btn btn-theme btn-block">Find Your
                        Tyres</a></div>
            </div>
        </div>
    </div>

    <div id="method2" class="panel-collapse collapse" data-bs-parent="#accordion">
        <div class="container">
            <div class="bg-dark p-4 ityreForm">
                <span class="arrow-blade"></span>
                <div class="row align-items-center">
                    <div class="col-lg-7 px-5">
                        <h3 class="text-white mb-3">Discover the Perfect Tyre for Your Vehicle</h3>
                        <p class="text-white hidden-xs">
                            Choosing the right tyre is essential for your vehicle’s safety, performance, and fuel
                            efficiency.
                            Explore our extensive collection of tyres designed for various vehicles, terrains, and driving
                            styles.
                            Whether you need tyres for daily commutes, long road trips, or challenging off-road adventures,
                            we have the ideal solution to ensure a smooth and reliable ride. Let us help you make every
                            journey
                            safer and more comfortable.
                        </p>
                    </div>
                    <div class="col-lg-5">
                        <x-search-tyres />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="pt-70 pb-70 content_pages">
    <div class="container">
        <div class="page-content">
            {!! Blade::render($page->content, ['garage_name' => $garage->garage_name]) !!}
        </div>
    </div>
</div>

@endsection