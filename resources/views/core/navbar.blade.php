<header class="app-header navbar">
  <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
    <span class="navbar-toggler-icon"></span>
  </button>
  <?php
// Get the current domain
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
// Set the path for domain-specific logo if it exists
$domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
$themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
$defaultLogoPath = public_path("frontend/themes/theme/img/logo/logo.png");
?>

  @if(!empty($garage->logo))
    <!-- If domain-specific logo exists, use it -->
    <a href="/dashboard" class="admin-logo">
    <img src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo" width="auto" height="auto">
    </a>
  @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
    <!-- If theme-specific logo exists, use it -->
    <a href="/dashboard" class="admin-logo">
    <img src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo" width="auto"
      height="auto">
    </a>
  @else
    <!-- Fallback logo if neither domain-specific nor theme-specific logo exists -->
    <a href="/dashboard" class="admin-logo">
    <img src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" width="auto" height="auto">
    </a>
  @endif


  <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
    <span class="navbar-toggler-icon"></span>
  </button>
<div class="garage-name">{{ getGarageDetails()->garage_name }}</div>
  <ul class="nav navbar-nav d-md-down-none">
    {{--
    <li class="nav-item px-3">
      <a class="nav-link" href="#">Users</a>
    </li>
    <li class="nav-item px-3">
      <a class="nav-link" href="#">Settings</a>
    </li>
    --}}
  </ul>
  <ul class="nav navbar-nav ml-auto mr-4">
<li class="nav-item dropdown d-md-down-none">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="icon-bell"></i>
    <span class="badge badge-pill badge-danger" id="notification-count">
      {{ $newBookingsCount ?? 0 }}
    </span>
  </a>
  <div class="dropdown-menu dropdown-menu-right notification-dropdown" style="overflow:hidden;">
    <div class="bg-dark p-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="p-0 text-white">New Bookings</h5>
      <form method="POST" action="{{ route('notifications.markAsRead') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
      </form>
    </div>
    </div>

    <div id="notification-items" style="width:350px; max-height:400px; overflow-y: auto;">
      @foreach($newBookings as $booking)
  <div class="dropdown-item" id="notification-{{ $booking->id }}">
    <div class="notification-item">
      <div>
    <span class="text-danger mark-as-read-btn" data-id="{{ $booking->id }}" title="Mark as read">
      <i class="fa fa-check-circle"></i>
    </span>
    <span class="badge {{ $booking->paymentStatus }}">{{ $booking->paymentStatus }}</span>
  </div>
    <div>
      <a href="{{ route('workshop.job.view', $booking->id) }}">
        <strong>Workshop #{{ $booking->id }}</strong> ({{ $booking->vrm.' , '.$booking->name }})<br>
        @if($booking->description)
          <small>{{ $booking->description }}</small><br>
        @endif
        <div class="d-flex gap-2 align-items-center">
        <small>{{ $booking->quantity }} {{ $booking->type }}'s - £{{ $booking->grandTotal }}</small>
        <small class="badge badge-dark ml-auto">{{ $booking->date }}</small>
        </div>
      </a>
    </div>
    </div>
  </div>
@endforeach

    </div>
  </div>
</li>


    <li class="nav-item d-md-down-none">
      <a class="nav-link" href="#"><i class="icon-list"></i></a>
    </li>
    <li class="nav-item d-md-down-none">
      <a class="nav-link" href="#"><i class="icon-location-pin"></i></a>
    </li> 
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
        aria-expanded="false">
        @if (Auth::check())
      <img src="{{ asset('img/avatars/1.png') }}" class="img-avatar" alt="{{ Auth::user()->email }}" height="20">
    @else
    <img src="{{ asset('img/avatars/6.jpg') }}" class="img-avatar" alt="admin@bootstrapmaster.com" height="20">
  @endif
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-header text-center">
          <strong>{{ auth()->user()->name }}'s Account</strong>
        </div>
        <a class="dropdown-item" href="{{url('/')}}/employee-edit/{{ auth()->user()->id }}"><i class="fa fa-bell-o"></i>
          Update<span class="badge badge-info">42</span></a>
        {{-- <a class="dropdown-item" href="#"><i class="fa fa-envelope-o"></i> Messages<span
            class="badge badge-success">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-tasks"></i> Tasks<span
            class="badge badge-danger">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-comments"></i> Comments<span
            class="badge badge-warning">42</span></a> --}}
        <div class="dropdown-header text-center">
          <strong>Settings</strong>
        </div>
        <a class="dropdown-item" href="{{url('/')}}/employee/{{ auth()->user()->id }}/view"><i class="fa fa-user"></i>
          Profile</a>
        {{-- <a class="dropdown-item" href="#"><i class="fa fa-wrench"></i> Settings</a>
        <a class="dropdown-item" href="#"><i class="fa fa-usd"></i> Payments<span
            class="badge badge-secondary">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-file"></i> Projects<span
            class="badge badge-primary">42</span></a> --}}
        <div class="divider"></div>
        {{-- <a class="dropdown-item" href="#"><i class="fa fa-shield"></i> Lock Account</a> --}}
        <a class="dropdown-item" href="{{ route('logout') }}"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-lock"></i>
          Logout
        </a>
        {{-- <a class="dropdown-item" href="{{ route('logout') }}"><i class="fa fa-lock"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></i> Logout</a> --}}
      </div>
    </li>
  </ul>
  <!-- <button class="navbar-toggler aside-menu-toggler" type="button">
    <span class="navbar-toggler-icon"></span>
  </button> -->
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
  </form>
</header>
<script>
  $(document).on('click', '.mark-as-read-btn', function () {
    const bookingId = $(this).data('id');
    const row = $('#notification-' + bookingId);

    $.ajax({
      url: `/ajax/mark-as-read/${bookingId}`,
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}'
      },
      success: function (response) {
        row.fadeOut(300, function () {
          $(this).remove();
        });

        // Decrement counter
        let countElem = $('#notification-count');
        let count = parseInt(countElem.text());
        if (count > 1) {
          countElem.text(count - 1);
        } else {
          countElem.text(0);
        }
      },
      error: function () {
        alert('Something went wrong.');
      }
    });
  });
</script>

