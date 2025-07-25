@extends('layouts.app')

@section('content')
    <div class="breadcrumb-area brand_breadcrumb">
        <img src="frontend/themes/default/img/banner/content-pages/contact-us.jpg" alt="Contact Us" class="img-bank">
        <div class="brand_name">
            <h1>Contact Us</h1>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->has('captcha'))
        <div class="alert alert-danger mt-2">{{ $errors->first('captcha') }}</div>
    @endif
    <div class="pt-70 pb-70">
        <div class="container">
            <div class="custom-row-2">
                <div class="col-lg-4 col-md-5">
                    <div class="contact-info-wrap">
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="contact-info-dec">
                                <p><a href="tel:{{ $garage->mobile }}">{{ $garage->mobile }}</a></p>
                            </div>
                        </div>
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-globe"></i>
                            </div>
                            <div class="contact-info-dec">
                                <p><a href="mailto:{{ $garage->email }}">{{ $garage->email }}</a></p>
                            </div>
                        </div>
                        <div class="single-contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-map-marker"></i>
                            </div>
                            <div class="contact-info-dec">
                                <p><a href="{{ $garage->google_map_link }}" target="_blank">{{ $garage->street }},
                                        {{ $garage->city }}, {{ $garage->zone }}, {{ $garage->country }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="contact-form">
                        <div class="contact-title mb-30">
                            <h2>Get In Touch</h2>
                        </div>
                        <form class="contact-form-style" id="contact-form" action="{{ route('contact.submit') }}"
                            method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <input type="text" name="website" class="d-none">
                                    <input name="name" placeholder="Name*" type="text" value="{{ old('name') }}" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-6">
                                    @include('components.otp-verification')
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    @error('otp_code') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-12">
                                    <input name="subject" placeholder="Subject*" type="text" value="{{ old('subject') }}"
                                        required>
                                    @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-12">
                                    <textarea name="message" placeholder="Your Message*"
                                        required>{{ old('message') }}</textarea>
                                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-lg-12">
                                    <x-recaptcha />
                                </div>

                                <div class="col-lg-12">
                                    <button class="submit" type="submit">SEND</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
