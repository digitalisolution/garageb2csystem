<!--
 * CoreUI - Open Source Bootstrap Admin Template
 * @version v1.0.6
 * @link http://coreui.io
 * Copyright (c) 2017 creativeLabs Łukasz Holeczek
 * @license MIT
 -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Łukasz Holeczek">
  <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,AngularJS,Angular,Angular2,Angular 2,Angular4,Angular 4,jQuery,CSS,HTML,RWD,Dashboard,React,React.js,Vue,Vue.js">
  <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
  <title>CoreUI - Open Source Bootstrap Admin Template</title>
  <!-- Icons -->
  <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/simple-line-icons.css') }}" rel="stylesheet">
  <!-- Main styles for this application -->
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <!-- Styles required by this views -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">  
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
  @include('panel.navbar')
  
  <div class="app-body">
    @include('panel.sidebar')
    <!-- Main content -->
    <main class="main">

      <!-- Breadcrumb -->
      @include('panel.breadcrumb')

      @yield('content')
      <!-- /.container-fluid -->
    </main>

    @include('panel.asidemenu')

  </div>

  @include('panel.footer')

  @include('panel.scripts')
  @yield('myscript')

</body>
</html>