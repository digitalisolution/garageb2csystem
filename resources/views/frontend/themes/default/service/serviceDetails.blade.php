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
    
@endsection