@extends('layouts.app')
@section('content')

<div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
    <div class="container">
        <div class="breadcrumb-content text-center">
            <ul>
                <li>
                    <a href="/">Home</a>
                </li>
                <li class="active">Brands</li>
            </ul>
        </div>
    </div>
</div>
<div class="pt-70 pb-70">
    <div class="container">
        <div class="brands_bank">
    @foreach ($brands as $brand)
            <a href="brand/{{$brand->slug}}">
                <div class="brand_logo">
        @if ($brand->image)
            <img src="{{ asset('frontend/themes/default/img/brand-logo/' . $brand->image) }}" alt="{{ $brand->name }}">
        @endif
    </div>
                {{$brand->name}}
            </a>
    @endforeach
    </div>
</div>
</div>
@endsection