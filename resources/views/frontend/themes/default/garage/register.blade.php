@extends('layouts.app')
@section('content')
<div class="pt-60 pb-60">
    <div class="container">
        <div class="register_bg">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="p-3 ml-30 text-center">
                        <h2 class="text-white">Welcome Back!</h2>
                        <p class="text-white">To keep connected with us please login with your personal info</p>
                        <a href="{{ route('garage.login') }}" class="btn btn-theme btn-block">Garage Login</a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-12">
                    <div class="short__item b-radius-0">
                        <div class="text-center bg-gray p-2 mb-3 rounded">
                            <h2 class="mb-0">Create an garage account</h2>
                        </div>

                        {{-- Show Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        {{-- Register Form --}}
                        <form method="POST" action="{{ route('garage.register.submit') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="garage_name">Garage Name:</label>
                                    <input class="form-control" type="text" id="garage_name" name="garage_name"
                                           placeholder="Company Name"
                                           value="{{ old('garage_name', $garages->garage_name ?? '') }}" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="phone">Phone:</label>
                                    <input value="{{ old('garage_phone', $garages->garage_phone ?? '') }}"
                                           placeholder="Phone" class="form-control" type="text"
                                           id="phone" name="garage_phone">
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="mobile">Mobile:</label>
                                    <input value="{{ old('garage_mobile', $garages->garage_mobile ?? '') }}"
                                           placeholder="Mobile" class="form-control"
                                           type="text" id="mobile" name="garage_mobile" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="email">Email:</label>
                                    <input value="{{ old('garage_email', $garages->garage_email ?? '') }}"
                                           placeholder="Email" class="form-control"
                                           type="email" id="email" name="garage_email" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="street">Street:</label>
                                    <input value="{{ old('garage_street', $garages->garage_street ?? '') }}"
                                           placeholder="Street" class="form-control"
                                           type="text" id="street" name="garage_street" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="city">City:</label>
                                    <input value="{{ old('garage_city', $garages->garage_city ?? '') }}"
                                           class="form-control" placeholder="City" type="text"
                                           id="city" name="garage_city" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="zone">Zone:</label>
                                    <input value="{{ old('garage_zone', $garages->garage_zone ?? '') }}"
                                           class="form-control" placeholder="Zone" type="text"
                                           id="zone" name="garage_zone">
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="country">Country:</label>
                                    <input value="{{ old('garage_country', $garages->garage_country ?? 'United Kingdom') }}"
                                           readonly class="form-control" type="text"
                                           id="country" name="garage_country">
                                </div>

                                <div class="col-lg-4 col-md-6 col-12 form-group">
                                    <label for="website_url">Website URL:</label>
                                    <input value="{{ old('garage_website_url', $garages->garage_website_url ?? '') }}"
                                           placeholder="Website Url" class="form-control"
                                           type="url" id="website_url" name="garage_website_url">
                                </div>

                                <div class="col-lg-12 col-md-12 col-12 form-group">
                                    <label for="garage_opening_time">Garage Opening Time:</label>
                                    <input value="{{ old('garage_opening_time', $garages->garage_opening_time ?? '') }}"
                                           class="form-control" type="text" id="garage_opening_time"
                                           placeholder="Mon-Fri: 08:30-20:00, Sat: 08:30-19:00, Sun: 09:30-17:00"
                                           name="garage_opening_time">
                                </div>

                                <div class="col-lg-6 col-md-6 col-12 form-group">
                                    <label for="logo">Logo:</label>
                                    <input type="file" name="garage_logo" id="garage_logo" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12 form-group">
                                    <label>Select Order Types:</label>
                                    <div class="garage_order_types">
                                    @php
                                        $orderTypes = [
                                            'fully_fitted' => 'Fully Fitted',
                                            'mobile_fitted' => 'Mobile Fitted',
                                            'mailorder' => 'Mail Order',
                                            'delivery' => 'Delivery',
                                            'collection' => 'Collection',
                                        ];
                                        $selectedOrderTypes = old('garage_order_types', isset($garages) ? explode(',', $garages->garage_order_types ?? '') : []);
                                        if (empty($selectedOrderTypes)) {
                                            $selectedOrderTypes = ['fully_fitted'];
                                        }
                                    @endphp

                                    @foreach ($orderTypes as $key => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="garage_order_types[]"
                                                   id="order_type_{{ $key }}" value="{{ $key }}"
                                                   {{ in_array($key, $selectedOrderTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="order_type_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12 form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password"
                                           class="form-control" required minlength="8">
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6 col-md-6 col-12 form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="form-control" required minlength="8">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                    <x-recaptcha />
                                </div>
                            <div class="text-center">
                                <button class="btn btn-theme btn-block mt-2" type="submit">Register</button>
                            </div>
                        </form>
                        @if ($errors->has('captcha'))
                            <div class="alert alert-danger mt-2">{{ $errors->first('captcha') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
