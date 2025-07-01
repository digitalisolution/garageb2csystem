@extends('layouts.plugin')
@section('content')

<div class="slider-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-7 m-auto">
                <div class="product-tab-list nav pt-10 pb-20 text-center">
                    <a class="active" href="#tyres" data-bs-toggle="tab">
                        <h4>Tyres</h4>
                    </a>
                    <a href="#services" data-bs-toggle="tab">
                        <h4>Services</h4>
                    </a>
                </div>
                <div class="tab-content jump">
                    <div class="tab-pane active" id="tyres">
                        @include('plugin/partials/search-tyres')
                    </div>
                    
                    <div class="tab-pane" id="services">
                         @include('plugin/partials/service-vrm-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection