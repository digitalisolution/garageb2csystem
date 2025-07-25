@extends('layouts.app')
@section('meta_title', $metaTitle)
@section('meta_keywords', $metaKeywords)
@section('meta_description', $metaDescription)
@section('content')

        @php
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/service-banners/';
            $fallbackPath = 'frontend/themes/default/img/service-banners/';

            $bannerimagePath = $service->service_banner_path ?? 'sample-banner-image.png';
            $bannerImageUrl = asset($bannerPath . $bannerimagePath);
            $fallbackImageUrl = asset($fallbackPath . $bannerimagePath);
        @endphp
    <div class="breadcrumb-area brand_breadcrumb">
            <img src="{{ $bannerImageUrl }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $service->name }}" class="img-bank">

        <div class="brand_name">
            <h1>{{ $service->name }}</h1>
        </div>
    </div>


    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="text-uppercase">Book Your Service in Seconds</h2>
                    <h4>Schedule expert vehicle care at your convenience — quick, easy, and hassle-free.</h4>
                </div>
                <div class="col-md-4"><a href="#method2" data-bs-toggle="collapse" class="btn btn-theme btn-block">Book Your
                        Service</a></div>
            </div>
        </div>
    </div>

    <div id="method2" class="panel-collapse collapse" data-bs-parent="#accordion">
        <div class="container">
            <div class="bg-dark p-4 ityreForm">
                <span class="arrow-blade"></span>
                <div class="row align-items-center">
                    <div class="col-lg-7 px-5">
                        <h3 class="text-white mb-3">Book Your Service with Ease</h3>
                        <p class="text-white hidden-xs">
                            Keep your vehicle running at its best with our expert car care services. Whether it’s routine maintenance, tyre fitting, diagnostics, or emergency repairs — we’ve got you covered. Book your appointment online in just a few clicks and experience hassle-free service from trusted professionals.
                        </p>
                    </div>
                    <div class="col-lg-5">
                        @if($service->cost_price > 0 && $service->price_type == 'fixed-price')
                            <x-service-detail-vrm-form />
                        @elseif($service->price_type == 'call-now')
                            <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                        @elseif($service->price_type == 'quote-now')
                            <a href="javascript:void(0);" class="btn btn-theme-select btn-enquiry-modal"
                                data-id="{{ $service->service_id }}" data-name="{{ $service->name }}">Quote
                                Now</a>
                                @include('service/quote-form')
                        @elseif($service->cost_price = 0 && $service->price_type == 'free')
                            <a href="javascript:void(0);" class="btn btn-theme-select add-to-cart"
                                data-id="{{ $service->service_id }}" data-name="{{ $service->name }}"
                                data-price="0" data-type="service">Add to Cart</a>
                        @else
                            <a href="tel:{{ $garage->mobile }}" class="btn btn-theme-select">Call Now</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection