<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Inquiry</title>
</head>
<body>
    <style type="text/css">p{color:#333;}</style>
    <div style="background:#ddd; padding:50px;">
        <!-- Logo -->
            @php
                $domain = request()->getHost();
                $domain = str_replace('.', '-', $domain);
                $domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
                $themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
                $defaultLogoPath = public_path("frontend/themes/default/img/logo/logo.png?v={{ time() }}");
            @endphp
            @if(!empty($garage->logo))
                <a href="{{ url('/') }}">
                    <img src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo" loading="lazy">
                </a>
            @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                <a href="{{ url('/') }}">
                    <img src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo" loading="lazy">
                </a>
            @else
                <a href="{{ url('/') }}">
                    <img src="{{ asset('frontend/themes/default/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" loading="lazy">
                </a>
            @endif
    	<div style="background:#fff;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.3);margin:0 auto;max-width:400px;overflow:hidden;">
            <div style="background:#1b1b1b;text-align:center;padding:15px;">
            </div>
            <div style="padding:25px;">
            <p><strong>Dear {{ $name }},</strong></p>
            <p>Thank you for reaching out to us. We confirm that we have received your message regarding the following:</p>
            <div style="background:#eee;padding:5px 10px;border-radius:4px;">
                <p>{{ $user_message }}</p>
            </div>
            <p>Our team is currently reviewing your inquiry and will get back to you shortly.</p>
            </div>
            <div style="background:#1b1b1b;text-align:center;padding:10px;color:#fff;">
            <p style="color:#fff;">Best regards,<br>{{ config('mail.from.name') }}</p>
        </div>
        </div>
    </div>
</body>
</html>