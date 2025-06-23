@extends('layouts.app')
@section('meta_title', $metaTitle)
@section('meta_keywords', $metaKeywords)
@section('meta_description', $metaDescription)
@section('content')
    <div class="breadcrumb-area brand_breadcrumb">
        @if (!empty($brand->bannerimage))
            <img src="{{ asset('frontend/themes/default/img/banner/content-pages/' . $brand->bannerimage) }}"
                alt="{{ $brand->name }}" class="img-bank">
        @else

            <img src="{{ asset('frontend/themes/default/img/banner/content-pages/common_tyrebrand_pic.jpg') }}"
                alt="Default Banner" class="img-bank">
        @endif


        <div class="brand_name">
            <h1>{{ $brand->name }} Tyres</h1>
        </div>
        <div class="brand_logo">
            @if ($brand->image)
                <img src="{{ asset('frontend/themes/default/img/brand-logo/' . $brand->image) }}" alt="{{ $brand->name }}"
                    height="80">
            @endif

        </div>
    </div>
    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
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
                        <p class="text-white">Choosing the right tyre is essential for your vehicle’s safety, performance,
                            and fuel efficiency. Explore our extensive collection of tyres designed for various vehicles,
                            terrains, and driving styles. Whether you need tyres for daily commutes, long road trips, or
                            challenging off-road adventures, we have the ideal solution to ensure a smooth and reliable
                            ride. Let us help you make every journey safer and more comfortable.</p>
                    </div>
                    <div class="col-lg-5">
                        <x-search-tyres />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-70 pb-70 tyres_brand">
        <div class="container">
            <div class="description">
                <p>{!! $brand->description !!}</p>
            </div>

        </div>


    </div>
@endsection