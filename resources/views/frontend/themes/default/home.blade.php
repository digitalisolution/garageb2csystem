@extends('layouts.app')
@section('content')
<div class="slider-area">
    <div class="container">
        <h2>Rolling you toward the <br class="hidden-xs">Perfect Tyres!</h2>
        <div class="tab-content">
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
<x-service-list />
<x-Html-template-list />
@endsection