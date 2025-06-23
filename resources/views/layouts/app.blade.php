<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? $garage->garage_name }}</title>
    <base href="{{ url('/') }}">
    <meta name="description" content="{{ $metaDescription ?? $garage->garage_name}}">
    <meta name="keywords" content="{{ $metaKeywords ?? '' }}">
    @php
        use App\Models\MetaSettings;
        // Fetch meta settings values
        $googleTagManager = MetaSettings::where('name', 'google_tag_manager')->where('status', 1)->value('content');
        $tagManager = MetaSettings::where('name', 'tag_manager')->where('status', 1)->value('content');
        $analytics = MetaSettings::where('name', 'analytics')->where('status', 1)->value('content');
        $googlesiteverification = MetaSettings::where('name', 'google-site-verification')->where('status', 1)->value('content');
        $google_customer_tracking = MetaSettings::where('name', 'google_customer_tracking')->where('status', 1)->value('content');
    @endphp
    @if ($googlesiteverification)
    <meta name="google-site-verification" content="{{ $googlesiteverification }}" />
    @endif
    <link rel="canonical" href="{{ $canonical }}">
@php
$domain = str_replace('.', '-', request()->getHost());
$favicon = null;

if (!empty($garage->favicon)) {
$domainFaviconPath = public_path("frontend/{$domain}/img/logo/{$garage->favicon}");
$themeFaviconPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->favicon}");

if (file_exists($domainFaviconPath)) {
$favicon = asset("frontend/{$domain}/img/logo/{$garage->favicon}") . '?v=' . time();
} elseif (file_exists($themeFaviconPath)) {
$favicon = asset("frontend/themes/{$garage->theme}/img/logo/{$garage->favicon}") . '?v=' . time();
}
}
// Default fallback
if (!$favicon) {
$favicon = asset("frontend/themes/default/img/favicon.png") . '?v=' . time();
}
@endphp
    <link rel="icon" type="image/png" href="{{ $favicon }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Nunito&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    </noscript>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/bootstrap.min.css') }}">
    <link rel="preload" href="{{ asset('frontend/themes/default/css/plugins.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('frontend/themes/default/css/icons.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/icons.min.css') }}">
    </noscript>
    <link rel="stylesheet" href="{{ theme_asset('css', 'style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ theme_asset('css', 'custom.css') }}?v={{ time() }}">
    <!-- JS -->
    @if (Request::is('booking*') || Request::is('calendar*') || Request::is('checkout*'))
        <script src="{{ mix('js/calendarfrd.js') }}" defer></script>
    @endif
    <!-- Bootstrap JS and Popper.js -->
    @if (!Request::is('/'))
    <script src="https://www.google.com/recaptcha/api.js" async></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?v={{time()}}"></script>
    <!-- Scripts -->
    @if ($googleTagManager)
    <!-- Google Tag Manager -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleTagManager }}" defer></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $googleTagManager }}');
    </script>
    @endif
    @if ($tagManager)
    <!-- Tag Manager -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $tagManager }}" defer></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $tagManager }}');
    </script>
    @endif
    @if ($analytics)
    <!-- Ahrefs Analytics -->
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="{{ $analytics }}" async></script>
    @endif
    @if ($google_customer_tracking)
    <script>{!! $google_customer_tracking !!}</script>
    @endif
</head>
<body>
    {!! include_dynamic_view('header') !!}
    <div id="app.">
        <main>
            @yield('content')
            @yield('scripts')   
        </main>
    </div>
    {!! include_dynamic_view('footer') !!}
    <script src="{{ asset('frontend/themes/default/js/vendor/modernizr-3.11.7.min.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/jquery-v3.6.0.min.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/jquery-migrate-v3.3.2.min.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/popper.min.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/plugins.js') }}" defer></script>
    <script src="{{ asset('frontend/themes/default/js/main.js') }}?v={{ time() }}" defer></script>
    @stack('scripts')
</body>
</html>