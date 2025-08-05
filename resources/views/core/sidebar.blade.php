@php
$role_id = Auth::user()->role_id;
@endphp
@if ($role_id == 1 || $role_id == 4 || $role_id == 5)
<div class="sidebar">
    <nav class="sidebar-nav">
    <ul class="nav">
    @if ($role_id == 1 || $role_id == 4)
        <li class="nav-item">
            <a class="nav-link active" href="{{ asset('/') }}dashboard"><i class="icon-home"></i> Dashboard </a>
            </li>
            <li class="nav-title">Options</li>
            <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-gear"></i> Workshop</a>
            <ul class="nav-dropdown-items">
                <!-- <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/workshop/add') }}">Add</a>
                </li> -->
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/workshop/search') }}">Bookings</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/estimate/search') }}">Estimates</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/workshop/search-invoice') }}">Invoices</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/payment-record') }}">Payments</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/workshop/delete') }}">Trash</a>
                </li>

                <!-- <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/sale/add') }}"><i class="fa fa-dot-circle-o"
                aria-hidden="true"></i>Sale Spare</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/sale/sale_return') }}"><i class="fa fa-building"
                aria-hidden="true"></i></i>Return Spare Log</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href=" {{ asset('/CustomerCreditDebitLog/search') }}"><i class="fa fa-inr"
                aria-hidden="true"></i></i> Customer Log </a>
                </li> -->
            </ul>
        </li>
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-user"></i>Customers</a>
            <ul class="nav-dropdown-items">
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/') }}AutoCare/customer/add ">Manage</a>
                </li>
            </ul>
        </li>
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-gift"
            aria-hidden="true"></i> Api
            Orders</a>
            <ul class="nav-dropdown-items">
                <li class="nav-item">
                <a class="nav-link" href="{{ asset('/AutoCare/api-orders') }}">Api Orders</a>
                </li>
            </ul>
        </li>
        <!-- html templae end -->
        <!-- information pages -->

        <!-- information pages end -->
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-circle-o"
        aria-hidden="true"></i>
        Tyres</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/tyres/search') }}">Search Tyres</a>
        </li>
        </ul>
        </li>
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-gift"
        aria-hidden="true"></i>Vehicles</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/vehicles') }}">Manage</a>
        </li>
        </ul>
        </li>
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-tag"></i>Brands
        Pages</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('AutoCare/brand') }}">Brands</a>
        </li>
        </ul>
        </li>
        @if ($role_id == 1)
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-line-chart"
        aria-hidden="true"></i>
        Supplier</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/supplier/add') }}">Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href=" {{ asset('/AutoCare/supplier/search') }}">Manage</a>
        </li>
        <!-- <li class="nav-item">
        <a class="nav-link" href=" {{ asset('/SupplierCreditDebitLog/search') }}"><i
        class="fa fa-snowflake-o" aria-hidden="true"></i> Supplier Log </a>
        </li> -->
        <li class="nav-item">
        <a class="nav-link" href=" {{ asset('/AutoCare/supplier/delete') }}">Trash</a>
        </li>
        </ul>
        </li>
        @endif

        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-money"
        aria-hidden="true"></i>Pricing</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/pricing/manage') }}">Tyre Pricing</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/mobile_fitting_pricing/create') }}">Mobile Fitting
        Pricing</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/mail_order_pricing/create') }}">Mail Order
        Pricing</a>
        </li>
        </ul>
        </li>
        @endif
        @if(get_option('sidebar_service_module')==1)
        @if($role_id == 1 || $role_id == 4 || $role_id == 5)
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-cogs"
        aria-hidden="true"></i></i>Service Pages</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/services') }}">Manage</a>
        </li>
        <!-- <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/service/search') }}"><i class="fa fa-search"></i>
        Search </a>
        </li> -->
        <!-- <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/service/delete') }}"><i class="icon-trash"></i>
        Trash </a>
        </li> -->
        </ul>
        </li>
        @endif
        @endif
        @if ($role_id == 1)

        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-calendar"
        aria-hidden="true"></i>Calendar</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/calendar/1/edit') }}">Manage</a>
        </li>
        </ul>
        </li>
        @endif
        @if ($role_id == 1 || $role_id ==5)
        <!-- html template -->
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-laptop"></i>Manage
        Website</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/garage_details') }}">Garage Details</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('AutoCare/html-templates') }}">Html Template</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('AutoCare/pages') }}">Information Pages</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('AutoCare/headermenu') }}">Header Menu</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/meta-settings') }}">Meta Settings</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/blog') }}">Blogs</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/click-report') }}">Call Tracking</a>
        </li>
        </ul>
        </li>
        @endif
        @if ($role_id == 1)
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-wrench"
        aria-hidden="true"></i> Setting
        Details</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/general-settings') }}">General settings</a>
        </li>

        </ul>
        </li>

        <li class="nav-item nav-dropdown" style="display:none">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-book fa-fw"
        aria-hidden="true"></i>Marketing</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/marketing/add') }}">Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/marketing/search') }}">Search </a>
        </li>
        @if ($role_id == 1)
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/marketing/delete') }}">Delete</a>
        </li>
        @endif
        </ul>
        </li>
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-money"></i>Credit
        Debit Log</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/credit-debit/add') }}">Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/credit-debit/search') }}">Search</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/credit-debit/delete') }}">Delete</a>
        </li>
        </ul>
        </li>
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-users"
        aria-hidden="true"></i>Multi
        User</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/employee') }}">Add User</a>
        </li>
        </ul>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/employee-list') }}">User List</a>
        </li>
        </ul>
        </li>
        @endif

        <!-- <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-shopping-basket"
        aria-hidden="true"></i>Spare</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/product/add') }}"><i class="fa fa-user"></i>
        Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/product/search') }}"><i class="fa fa-search"></i>
        Search </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/product/delete') }}"><i class="icon-trash"></i>
        Trash </a>
        </li>
        </ul>
        </li> -->

        <!-- <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-shopping-bag"
        aria-hidden="true"></i>Purchase</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/purchase/add') }}"><i class="fa fa-user"></i>
        Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/purchase/search') }}"><i class="fa fa-search"></i>
        Search </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/purchase/delete') }}"><i class="icon-trash"></i>
        Trash </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/AutoCare/purchase/purhase_return') }}"><i
        class="fa fa-snowflake-o" aria-hidden="true"></i> Purchase Return Log </a>
        </li>
        </ul>
        </li> -->

        <!-- <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="fa fa-universal-access fa-spin"
        aria-hidden="true"></i>Master Entery</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/master/brands') }}"><i class="fa fa-user"></i>
        Brands</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/master/modal') }}"><i class="fa fa-search"></i>
        Model </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/master/service_name') }}"><i class="icon-trash"></i>
        Service Name </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="{{ asset('/master/service_type') }}"><i class="icon-trash"></i> Serice
        Type </a>
        </li>
        </ul>
        </li> -->



        <!-- <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="icon-puzzle"></i>Stock</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="/sample/buttons"><i class="icon-puzzle"></i> Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/sample/cards"><i class="icon-puzzle"></i> Search </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/sample/forms"><i class="icon-trash"></i> Trash </a>
        </li>
        </ul>
        </li>
        <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="javascript:void(0);"><i class="icon-puzzle"></i>User</a>
        <ul class="nav-dropdown-items">
        <li class="nav-item">
        <a class="nav-link" href="/sample/buttons"><i class="icon-puzzle"></i> Add</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/sample/cards"><i class="icon-puzzle"></i> Search </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/sample/forms"><i class="icon-puzzle"></i> Trash </a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/sample/forms"><i class="icon-puzzle"></i> Permission </a>
        </li>
        </ul>
        </li> -->

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
@endif
<script>
    $(document).ready(function () {
    $('.nav-dropdown-toggle').on('click', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('open');
    });
    });
</script>