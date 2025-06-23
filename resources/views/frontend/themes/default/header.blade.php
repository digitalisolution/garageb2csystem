<header class="header-area header-padding-1 header-res-padding clearfix">
    <div class="app-header-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-5 col-lg-5 col-md-6 col-6">
                    <div class="logo">
                        <?php
// Get the current domain
$domain = request()->getHost();
$domain = str_replace('.', '-', $domain);
// Set the path for domain-specific logo if it exists
$domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
$themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
$defaultLogoPath = public_path("frontend/themes/theme/img/logo/logo.png?v={{ time() }}");
?>

                        @if(!empty($garage->logo))
                            <!-- If domain-specific logo exists, use it -->
                            <a href="/">
                                <img src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo"
                                    loading="lazy">
                            </a>
                        @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                            <!-- If theme-specific logo exists, use it -->
                            <a href="/">
                                <img src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}?v={{ time() }}"
                                    alt="Logo" loading="lazy">
                            </a>
                        @else
                            <!-- Fallback logo if neither domain-specific nor theme-specific logo exists -->
                            <a href="/">
                                <img src="{{ asset('frontend/themes/theme/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" loading="lazy">
                            </a>
                        @endif


                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-6">
                    <div class="call-satting">
                        <a href="tel:{{ $garage->mobile }}"><i class="pe-7s-call"></i> {{ $garage->mobile }}</a>
                    </div>
                    <p class="app-address">{{ $garage->street }}, {{ $garage->city }}, {{ $garage->zone }},
                        {{ $garage->country }}
                    </p>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="header-right-wrap">
                        <div class="same-style account-setting">
                            <a class="account-satting-active" href="#">
                                @if (Auth::guard('customer')->check())
                                    {{ Auth::guard('customer')->user()->customer_name }} <i class="pe-7s-user"></i>
                                @else
                                    Account <i class="pe-7s-user"></i>
                                @endif
                            </a>
                            <div class="account-dropdown">
                                <ul>
                                    @if (Auth::guard('customer')->check())
                                        <li>
                                            <a href="{{ route('customer.myaccount') }}">My Account</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('customer.logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>
                                            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ route('customer.login') }}">Login</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('customer.register') }}">Register</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="same-style cart-wrap">
                            <button class="icon-cart">
                                Basket
                                <i class="pe-7s-shopbag"></i>
                                <span class="count-style">{{ $cartTotalQuantity }}</span>
                            </button>
                            <x-cart-top />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="menu_bank">
        <div class="container-fluid">
            <div class="row">
                <div class="mobile-menu-area">
                    <div class="mobile-menu">
                        @php
                            $menuPages = \App\Models\HeaderMenu::whereNull('parent_id')
                                ->with('children')
                                ->where('status', 1)
                                ->orderBy('sort', 'asc')
                                ->get();
                        @endphp
                        <nav id="mobile-menu-active">
                            <ul class="menu-overflow">
                                <li><a href="{{ route('home') }}">Home</a></li>
                                @foreach ($menuPages as $page)
                                    @if ($page && $page->status)
                                        {{-- Only show active pages that are included in the header menu --}}
                                        <li>
                                            <a href="{{ $page->children->count() ? 'javascript:void(0)' : url($page->slug) }}">
                                                {{ $page->title }}
                                                @if ($page->children->count())
                                                    {{-- If children exist, show the arrow --}}
                                                    <i class="fa fa-angle-down"></i>
                                                @endif
                                            </a>
                                            @if ($page->children->count())
                                                {{-- Render submenu if children exist --}}
                                                <ul class="submenu">
                                                    @foreach ($page->children->where('status', 1)->sortBy('sort') as $child)
                                                        {{-- Only active, included in header menu, and sorted --}}
                                                        <li>
                                                            <a href="{{ url($child->slug) }}">{{ $child->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach

                                <li><a href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 d-none d-lg-block">
                    <div class="main-menu">
                        <nav>
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a></li>
                                @foreach ($menuPages as $page)
                                   @if ($page && $page->status)
                                        {{-- Only show active pages that are included in the header menu --}}
                                        <li>
                                            <a href="{{ $page->children->count() ? 'javascript:void(0)' : url($page->slug) }}">
                                                {{ $page->title }}
                                                @if ($page->children->count())
                                                    {{-- If children exist, show the arrow --}}
                                                    <i class="fa fa-angle-down"></i>
                                                @endif
                                            </a>
                                            @if ($page->children->count())
                                                {{-- Render submenu if children exist --}}
                                                <ul class="submenu">
                                                    @foreach ($page->children->where('status', 1)->sortBy('sort') as $child)
                                                        {{-- Only active, included in header menu, and sorted --}}
                                                        <li>
                                                            <a href="{{ url($child->slug) }}">{{ $child->title }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endif
                                @endforeach
                                <li><a href="{{ route('contact') }}">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>