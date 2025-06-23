<!-- resources/views/components/customer-menu.blade.php -->
<div class="border rounded mb-4 overflow-hidden">
    <ul class="customer_menu">
        <li><a href="{{ route('customer.myaccount') }}"
                class="{{ request()->routeIs('customer.myaccount') ? 'active' : '' }}"><i class="fa fa-user"></i> My
                Account</a></li>
        <li><a href="{{ route('customer.orders') }}"
                class="{{ request()->routeIs('customer.orders') ? 'active' : '' }}"><i class="fa fa-briefcase"></i>
                Orders</a></li>
        <li><a href="{{ route('customer.invoices') }}"
                class="{{ request()->routeIs('customer.invoices') ? 'active' : '' }}"><i class="fa fa-files-o"></i>
                Invoices</a></li>
        <li><a href="{{ route('customer.vehicles') }}"
                class="{{ request()->routeIs('customer.vehicles') ? 'active' : '' }}"><i class="fa fa-car"></i>
                Vehicles</a></li>
        @if (Auth::guard('customer')->check())
            <li class="logout-btn">
                <a href="{{ route('customer.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                        class="fa fa-recycle"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        @endif
    </ul>
</div>