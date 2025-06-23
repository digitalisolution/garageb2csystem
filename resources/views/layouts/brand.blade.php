<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{ url('/') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
    <!-- logo -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/defaultApp.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/themes/default/css/plugins.css') }}">
    {{--
    <link href="{{ session('domain_css_style.css', asset('themes/default/css/style.css')) }}?v={{ time() }}"
        rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ theme_asset('css', 'style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ theme_asset('css', 'custom.css') }}?v={{ time() }}">

    <!-- JS -->
    {{--
    <script src="{{ theme_asset('js', 'main.js') }}" defer></script> --}}

    <!-- Frontend css end -->
    <title>{{ $brand->meta_title ?? 'Digital Garage' }}</title>
    <meta name="description" content="{{ $brand->meta_description ?? 'Digital Garage' }}">
    <meta name="keywords" content="{{ $brand->meta_keyword ?? 'Digital Garage' }}">
</head>

<body>

    {!! include_dynamic_view('header') !!}

    <div id="app.">
        <main>
            @yield('content')
            @yield('scripts')
            @stack('scripts')
        </main>
    </div>

    {{-- @include('frontend.themes.default.footer') --}}
    {!! include_dynamic_view('footer') !!}

    <script src="{{ asset('frontend/themes/default/js/vendor/modernizr-3.11.7.min.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/jquery-v3.6.0.min.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/jquery-migrate-v3.3.2.min.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/plugins.js') }}"></script>
    <script src="{{ asset('frontend/themes/default/js/main.js') }}"></script>
    <!-- Main JS -->
    {{--
    <script src="{{ theme_asset('js', 'main.js') }}"></script> --}}
</body>

</html>