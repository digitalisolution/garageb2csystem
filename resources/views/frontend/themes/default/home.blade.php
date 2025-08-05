@extends('layouts.app')
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
                        <x-search-tyres />
                    </div>
                    <x-vrm-modal id="vehicleDataModal" title="Vehicle Details">
                        <div id="vehicleDataContent">
                            <p>Loading...</p>
                        </div>
                    </x-vrm-modal>
                    <div class="tab-pane" id="services">
                        <x-service-vrm-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-service-list />
<x-Html-template-list />
@endsection