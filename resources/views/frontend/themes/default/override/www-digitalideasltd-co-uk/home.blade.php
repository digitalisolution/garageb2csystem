@extends('layouts.app')

@section('content')

<div class="slider-area pt-20 pb-20">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-7 m-auto">
                <div class="product-tab-list nav text-center">
                    <a class="active" href="#tyres" data-bs-toggle="tab">
                        <h3>Tyres</h3>
                    </a>
                    <a href="#services" data-bs-toggle="tab">
                        <h3>Services</h3>
                    </a>
                </div>
                <div class="tab-content jump">
                    <!-- Form -->
                    <div class="tab-pane active" id="tyres">
                        <x-search-tyres />
                    </div>
                    <!-- Result Modal -->
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
@endsection
<style type="text/css">.header-area{display:none;}</style>