@php
    $recaptchaStatus = \App\Models\MetaSettings::where('name', 'google_recaptcha_status')->value('content');
    $siteKey = \App\Models\MetaSettings::where('name', 'google_recaptcha_site_key')->value('content');
@endphp

@if($recaptchaStatus === 'active')
    <div class="text-center">
        <div class="g-recaptcha" data-sitekey="{{ $siteKey }}"></div>
        @error('g-recaptcha-response')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
@endif

@if($recaptchaStatus === 'active')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif