@php
  $role_id = Auth::user()->role_id;
@endphp

@if ($role_id == 1 || $role_id == 4 || $role_id == 5)
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$garage->garage_name}}</title>

    <!-- logo -->
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
    <title>{{ config('app.name') }}</title>
    <script src="{{ asset('js/jQuery.min.js') }}"></script>
    <script src="{{ mix('js/app.js')}}"></script>
    <!-- Bootstrap 5 CSS (Ensure it's included in <head>) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ mix('js/app.js') }}"></script>
    <!-- Icons -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-line-icons.css') }}" rel="stylesheet">
    <base href="{{ url('/') }}">
    <!-- Main styles for this application -->
    <link rel="stylesheet" href="{{ asset('css/styleOriginal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/anothertemp.css') }}?v={{ time() }}"><!-- my csss  -->
    <!-- Styles required by this views -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!--  <link rel="stylesheet" href="{{ asset('bootstrap-4.1.3/dist/css/bootstrap.css') }}">  -->
    <!-- Data table CSS -->
    <link href="{{ asset('js/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css') }}"
    rel="stylesheet" type="text/css" />
    <!-- <link href="{{ asset('js/DataTables/DataTables-1.10.18/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/> -->

  </head>

  <body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    @include('core.navbar')

    <div class="app-body">
    @include('core.sidebar')
    <!-- Main content -->
    <main class="main">

      <!-- Breadcrumb -->
      @include('core.breadcrumb')

      @yield('content')
      <div class="dashboard-widgets">
      @yield('calendar-widget') <!-- Place the calendar widget here -->
      </div>

      <!-- /.container-fluid -->
    </main>

    @include('core.asidemenu')

    </div>

    @include('core.footer')

    @include('core.scripts')
    @yield('myscript')
    @yield('scripts')

    <script type="text/javascript">
    /****
    * MAIN NAVIGATION
    */

    $(document).ready(function ($) {

      // Add class .active to current link
      // $.navigation.find('a').each(function () {

      // var cUrl = String(window.location).split('?')[0];

      // if (cUrl.substr(cUrl.length - 1) == '#') {
      //   cUrl = cUrl.slice(0, -1);
      // }

      // if ($($(this))[0].href == cUrl) {
      //   $(this).addClass('active');

      //   $(this).parents('ul').add(this).each(function () {
      //   $(this).parent().addClass('open');
      //   });
      // }
      // });

      // // Dropdown Menu
      // $.navigation.on('click', 'a', function (e) {

      // if ($.ajaxLoad) {
      //   e.preventDefault();
      // }

      // if ($(this).hasClass('nav-dropdown-toggle')) {
      //   $(this).parent().toggleClass('open');
      //   resizeBroadcast();
      // }

      // });
      // function resizeBroadcast() {
      // // Trigger the resize event only once after a short delay
      // setTimeout(() => {
      //   window.dispatchEvent(new Event('resize'));
      // }, 62.5);
      // }
      // function resizeBroadcast() {
      // requestAnimationFrame(() => {
      //   window.dispatchEvent(new Event('resize'));
      // });
      // }

      /* ---------- Main Menu Open/Close, Min/Full ---------- */
      $('.sidebar-toggler').click(function () {
      $('body').toggleClass('sidebar-hidden');
      resizeBroadcast();
      });

      $('.sidebar-minimizer').click(function () {
      $('body').toggleClass('sidebar-minimized');
      resizeBroadcast();
      });

      $('.brand-minimizer').click(function () {
      $('body').toggleClass('brand-minimized');
      });

      $('.aside-menu-toggler').click(function () {
      $('body').toggleClass('aside-menu-hidden');
      resizeBroadcast();
      });

      $('.mobile-sidebar-toggler').click(function () {
      $('body').toggleClass('sidebar-mobile-show');
      resizeBroadcast();
      });

      $('.sidebar-close').click(function () {
      $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
      });

      /* ---------- Disable moving to top ---------- */
      $('a[href="#"][data-top!=true]').click(function (e) {
      e.preventDefault();
      });

    });

    /****
    * CARDS ACTIONS
    */

    $('.sidebar-toggler').click(function () {
      $('body').toggleClass('sidebar-hidden');
      resizeBroadcast();
    });

    $('.sidebar-minimizer').click(function () {
      $('body').toggleClass('sidebar-minimized');
      resizeBroadcast();
    });

    $('.brand-minimizer').click(function () {
      $('body').toggleClass('brand-minimized');
    });

    $('.aside-menu-toggler').click(function () {
      $('body').toggleClass('aside-menu-hidden');
      resizeBroadcast();
    });

    $('.mobile-sidebar-toggler').click(function () {
      $('body').toggleClass('sidebar-mobile-show');
      resizeBroadcast();
    });

    $('.sidebar-close').click(function () {
      $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
    });

    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function (e) {
      e.preventDefault();
    });
    </script>
  </body>

  </html>
@else
  <!DOCTYPE html>
  <html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="The page you requested cannot be found.">
    <meta name="author" content="Lukasz Holeczek">
    <meta name="keyword" content="The page you requested cannot be found.">
    <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->

    <title>The page you requested cannot be found.</title>

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
    <!-- Bootstrap 5 CSS (Ensure it's included in <head>) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Icons -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-line-icons.css') }}" rel="stylesheet">
    <base href="{{ url('/') }}">
    <!-- Main styles for this application -->
    <link rel="stylesheet" href="{{ asset('css/styleOriginal.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/anothertemp.css') }}?v={{ time() }}"><!-- my csss  -->
    <!-- Styles required by this views -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!--  <link rel="stylesheet" href="{{ asset('bootstrap-4.1.3/dist/css/bootstrap.css') }}">  -->
    <!-- Data table CSS -->
    <link href="{{ asset('js/vendors/bower_components/datatables/media/css/jquery.dataTables.min.css') }}"
    rel="stylesheet" type="text/css" />


  </head>

  <body class="app flex-row align-items-center">
    <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
      <div class="clearfix">
        <h1 class="float-left display-3 mr-4">404</h1>
        <h4 class="pt-3">Oops! You're lost.</h4>
        <p class="text-muted">The page you are looking for was not found.</p>
      </div>
      <div class="input-prepend input-group">
        <span class="input-group-addon"><i class="fa fa-search"></i></span>
        <input id="prependedInput" class="form-control" size="16" type="text" placeholder="What are you looking for?">
        <span class="input-group-btn">
        <button class="btn btn-info" type="button">Search</button>
        </span>
      </div>
      </div>
    </div>
    </div>
  </body>

  </html>
@endif