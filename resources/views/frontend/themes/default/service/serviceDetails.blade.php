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
    <div class="pt-70 pb-70 service_page_bank">
        <div class="container">
            <!-- <h1>{{ $service->name }}</h1> -->
            {!! $service->content !!}
            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->has('captcha'))
                <div class="alert alert-danger mt-2">{{ $errors->first('captcha') }}</div>
            @endif
            <div class="contact-form mt-5">
                <div class="contact-title mb-30">
                    <h2>Enquire Now</h2>
                </div>
                @php
                    $recaptchaStatus = \App\Models\MetaSettings::where('name', 'google_recaptcha_status')->value('content');
                    $siteKey = \App\Models\MetaSettings::where('name', 'google_recaptcha_site_key')->value('content');
                @endphp

                <form class="contact-form-style" id="contact-form" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="text" name="website" class="d-none">
                            <input name="name" placeholder="Name*" type="text" value="{{ old('name') }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-lg-6">
                            <input name="email" placeholder="Email*" type="email" value="{{ old('email') }}" required>
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-lg-12">
                            <input name="subject" placeholder="Subject*" type="text" value="{{ old('subject') }}" required>
                            @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-lg-12">
                            <textarea name="message" placeholder="Your Message*" required>{{ old('message') }}</textarea>
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
    <script>
        document.getElementById('contact-form').addEventListener('submit', function (event) {
            const inputs = document.querySelectorAll('#contact-form input, #contact-form textarea');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    isValid = false;
                    input.style.border = '1px solid red';
                } else {
                    input.style.border = '';
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all fields.');
            }
        });
    </script>
@endsection