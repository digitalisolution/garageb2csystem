<!DOCTYPE html>
<html>
<head>
    <title>Contact Form Inquiry</title>
</head>
<body>
    <div style="background:#ddd; padding:50px;">
        <div style="background:#fff;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.3);margin:0 auto;max-width:500px;overflow:hidden;">
            <div style="background:#1b1b1b;text-align:center;padding:15px;">
                <!-- Logo -->
                    @php
                        // Get the current domain
                        $domain = request()->getHost();
                        $domain = str_replace('.', '-', $domain);
                        // Set the path for domain-specific logo if it exists
                        $domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
                        $themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
                        $defaultLogoPath = public_path("frontend/themes/default/img/logo/logo.png?v={{ time() }}");
                        @endphp

                @if(!empty($garage->logo))
                            <!-- If domain-specific logo exists, use it -->
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo"
                                    loading="lazy">
                            </a>
                        @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                            <!-- If theme-specific logo exists, use it -->
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}?v={{ time() }}"
                                    alt="Logo" loading="lazy">
                            </a>
                        @else
                            <!-- Fallback logo if neither domain-specific nor theme-specific logo exists -->
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('frontend/themes/default/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" loading="lazy">
                            </a>
                        @endif
                        <div style="color:#fff;font-size:25px;font-weight:bold;">
                    Contact Form Inquiry
                </div>
            </div>
            <div style="text-align:center;margin-top:25px;"><img src="{{ asset('frontend/themes/default/img/enquiry_envelope.webp') }}?v={{ time() }}" alt="enquiry envelope"></div>
            <div style="padding:20px;border-radius:10px;background:#f2f2f2;margin:30px;">
                <table border="0" cellpadding="5" cellspacing="0" width="100%">
                    <tbody>
                        <tr style="border-collapse:collapse;">
                            <td width="25%" style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <strong>Name:</strong>
                            </td>
                            <td width="75%" style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                {{ $name }}</td>
                        </tr>
                        <tr style="border-collapse:collapse">
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <strong>Email Address:</strong>
                            </td>
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <a href="mailto:{{ $email }}" style="color:#0078be;font-weight:bold;text-decoration:none" target="_blank">{{ $email }}</a>
                            </td>
                        </tr>
                        <tr style="border-collapse:collapse">
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <strong>Subject:</strong>
                            </td>
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                {{ $subject }}
                            </td>
                        </tr>
                        <tr style="border-collapse:collapse">
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <strong>Message:</strong>
                            </td>
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                {{ $user_message }}</td>
                        </tr>
                         <tr style="border-collapse:collapse">
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                <strong>IP Address:</strong>
                            </td>
                            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                                {{ $ip }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    
</div>
</body>

</html>

