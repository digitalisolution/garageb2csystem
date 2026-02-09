<header class="header-area header-padding-0 header-res-padding clearfix">
    <div class="app-header-area">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-4 col-lg-4 col-md-6 col-7">
                    <div class="logo">
                        <?php
                        // Get the current domain
                        $domain = request()->getHost();
                        $domain = str_replace('.', '-', $domain);
                        // Set the path for domain-specific logo if it exists
                        $domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
                        $themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
                        $defaultLogoPath = public_path("frontend/themes/default/img/logo/logo.png?v={{ time() }}");
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
                                <img src="{{ asset('frontend/themes/default/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" loading="lazy">
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 col-5">
                    <div class="linkwidget">
                        <a href="tel:{{ $garage->mobile }}">
                            Telephone
                            <span>{{ $garage->mobile }}</span>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                    <div class="linkwidget mail">
                        <a href="tel:{{ $garage->email }}">
                            Email
                         <span>{{ $garage->email }}</span>
                     </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-12">
                    <div class="header-right-wrap align-items-center">
                        <div class="same-style account-setting">
                            <a class="account-satting-active" href="#">
                                @if (Auth::guard('customer')->check())
                                    {{ Auth::guard('customer')->user()->customer_name }} <i class="pe-7s-user"></i>
                                @else
                                    <img src="frontend/www-garage-automation-co-uk/img/user_icon.webp" alt="user icon" width="30" height="30">
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
                                        <li>
                                            <a href="garage/auth/login">Garage Fitter Login</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="same-style cart-wrap">
                            <button class="icon-cart">
                                <i class="pe-7s-cart"></i>
                                Shopping Cart
                                <span class="count-style">{{ $cartTotalQuantity }}</span>
                            </button>
                            <x-cart-top />
                        </div>
                        <div class="same-style cart-wrap">
                            <div class="clickable-menu clickable-mainmenu-active">
                                <a href="#"><i class="pe-7s-menu"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="mobile-menu-area">
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
    </div> -->
<div class="clickable-mainmenu">
        <div class="clickable-mainmenu-icon">
            <button class="clickable-mainmenu-close">
                <span class="pe-7s-close"></span>
            </button>
        </div>
        <div class="side-logo">
            <?php
                // Get the current domain
                $domain = request()->getHost();
                $domain = str_replace('.', '-', $domain);
                // Set the path for domain-specific logo if it exists
                $domainLogoPath = public_path("frontend/{$domain}/img/logo/{$garage->logo}?v={{ time() }}");
                $themeLogoPath = public_path("frontend/themes/{$garage->theme}/img/logo/{$garage->logo}?v={{ time() }}");
                $defaultLogoPath = public_path("frontend/themes/default/img/logo/logo.png?v={{ time() }}");
                ?>

                @if(!empty($garage->logo))
                    <!-- If domain-specific logo exists, use it -->
                    <a href="/">
                        <img src="{{ asset('frontend/' . $domain . '/img/logo/' . $garage->logo) }}?v={{ time() }}" alt="Logo"
                            loading="lazy" class="img-adjust">
                    </a>
                @elseif(!empty($garage->logo) && file_exists($themeLogoPath))
                    <!-- If theme-specific logo exists, use it -->
                    <a href="/">
                        <img src="{{ asset('frontend/themes/' . $garage->theme . '/img/logo/' . $garage->logo) }}?v={{ time() }}"
                            alt="Logo" loading="lazy" class="img-adjust">
                    </a>
                @else
                    <!-- Fallback logo if neither domain-specific nor theme-specific logo exists -->
                    <a href="/">
                        <img src="{{ asset('frontend/themes/default/img/logo/logo.png') }}?v={{ time() }}" alt="Logo" loading="lazy" class="img-adjust">
                    </a>
                @endif
        </div>
        <div id="menu" class="text-left clickable-menu-style">
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
        </div>
        <div class="side-social">
            <ul>
                <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a class="dribbble" href="#"><i class="fa fa-dribbble"></i></a></li>
                <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a class="linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>
            </ul>
        </div>
    </div>
</header>
