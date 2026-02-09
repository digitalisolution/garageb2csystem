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
                            <a href="customer/login" class="btn btn-theme btn-block">Login</a>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6 col-12">
                        <div class="short__item b-radius-0">
                            <div class="text-center bg-gray p-2 mb-3 rounded">
                                <h2 class="mb-0">Create an account</h2>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('customer.register') }}">
                                <h4>Personal Information</h4>
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>First Name<sup>*</sup></label>
                                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                                required minlength="2" maxlength="50">
                                            @error('customer_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Last Name<sup>*</sup></label>
                                            <input type="text" name="customer_last_name"
                                                value="{{ old('customer_last_name') }}" required minlength="2"
                                                maxlength="50">
                                            @error('customer_last_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Email Address<sup>*</sup></label>
                                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                                required>
                                            @error('customer_email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Phone<sup>*</sup></label>
                                            <input type="text" name="customer_contact_number"
                                                value="{{ old('customer_contact_number') }}" required minlength="10"
                                                maxlength="15">
                                            @error('customer_contact_number')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Password<sup>*</sup></label>
                                            <input type="password" name="password" required minlength="8">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Confirm Password<sup>*</sup></label>
                                            <input type="password" name="password_confirmation" required minlength="8">
                                        </div>
                                    </div>
                                </div>
                                <h4>Company Information</h4>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Company<sup>*</sup></label>
                                            <input type="text" name="company_name" value="{{ old('company_name') }}"
                                                required maxlength="100">
                                            @error('company_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Country<sup>*</sup></label>
                                            <select name="billing_address_country" class="form-control" required>
                                                <option value="United Kingdom" {{ old('billing_address_country', 'United Kingdom') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                            </select>
                                            @error('billing_address_country')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Street<sup>*</sup></label>
                                            <input type="text" name="billing_address_street"
                                                value="{{ old('billing_address_street') }}" required maxlength="100">
                                            @error('billing_address_street')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>City<sup>*</sup></label>
                                            <input type="text" name="billing_address_city"
                                                value="{{ old('billing_address_city') }}" required maxlength="50">
                                            @error('billing_address_city')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Postcode<sup>*</sup></label>
                                            <input type="text" name="billing_address_postcode"
                                                value="{{ old('billing_address_postcode') }}" required maxlength="10">
                                            @error('billing_address_postcode')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>County<sup>*</sup></label>
                                            <select name="billing_address_county" class="form-control" required>
                                                <option value="">Select</option>
                                                @foreach ($counties as $county)
                                                    <option value="{{ $county->zone_id }}" {{ old('billing_address_county') == $county->zone_id ? 'selected' : '' }}>
                                                        {{ $county->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('billing_address_county')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <x-recaptcha />
                                </div>
                                <button type="submit" class="btn btn-theme btn-block">Register</button>
                            </form>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if ($errors->has('captcha'))
                            <div class="alert alert-danger mt-2">{{ $errors->first('captcha') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection