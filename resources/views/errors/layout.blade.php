<!-- resources/views/components/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Garage Management System')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('about.us') }}">About Us</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
                <li><a href="{{ route('services') }}">Services</a></li>
                <li><a href="{{ route('tyres') }}">Tyres</a></li>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2024 Garage Management System. All Rights Reserved.</p>
    </footer>
</body>

</html>