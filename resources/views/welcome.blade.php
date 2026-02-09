<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Free Guarage Management System with free technical support</title>
    <meta name="keywords" content="guarage management system,free guarage management system application,car garage management software,
    garage management software open source,
    garage management system project,
    best garage management software,
    free car garage management software,
    best garage software,
    automotive workshop management software,
    auto repair software free" />
    <meta name="description"
        content="Get full featured Free Guarage Management system, Best auto repair and garage management software for independent workshops and repair shop. Get top garage software features, reviews, pricing and free demo. Get free Support. " />
    <meta name="subject" content="Free Guarage Management System with free technical support">
    <meta name="author" content="ashutosh kumar choubey">
    <meta name="url" content="worldgyan.com">
    <meta name="identifier-URL" content="worldgyan.com">
    <meta name="coverage" content="Worldwide">
    <meta name="distribution" content="Global">
    <meta name="og:title" content="Free Guarage Management System with free technical support" />
    <meta name="og:url" content="worldgyan.com" />
    <meta name="og:image" content="public/assets/worldggyan for easy and simple.jpg" />
    <!-- logo -->
    <meta name="og:site_name" content="Free Guarage Management System with free technical support" />
    <meta name="og:description"
        content="Get full featured Free Guarage Management system, Best auto repair and garage management software for independent workshops and repair shop. Get top garage software features, reviews, pricing and free demo. Get free Support." />
    <meta property="og:type" content="Software Application" />
    <meta name="date" content="2020-01-13" scheme="YYYY-MM-DD">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/dashboard') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                Garage Management System
            </div>

            <!-- <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://nova.laravel.com">Nova</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div> -->
        </div>
    </div>
</body>

</html>