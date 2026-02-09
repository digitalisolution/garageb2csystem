<!-- resources/views/components/customer-menu.blade.php -->
<div class="border rounded mb-4 overflow-hidden">
    <ul class="customer_menu">
        <li><a href="{{ route('garage.myaccount') }}"
                class="{{ request()->routeIs('garage.myaccount') ? 'active' : '' }}"><i class="fa fa-user"></i> My
                Account</a></li>
        <li><a href="{{ route('garage.orders') }}"
                class="{{ request()->routeIs('garage.orders') ? 'active' : '' }}"><i class="fa fa-briefcase"></i>
                Orders</a></li>
        <li><a href="{{ route('garage.invoices') }}"
                class="{{ request()->routeIs('garage.invoices') ? 'active' : '' }}"><i class="fa fa-files-o"></i>
                Invoices</a></li>
        <li><a href="{{ route('garage.statement') }}"
                class="{{ request()->routeIs('garage.statement') ? 'active' : '' }}"><i class="fa fa-files-o"></i>
                Statement</a></li>
        @if (Auth::guard('garage')->check())
            <li class="logout-btn">
                <a href="{{ route('garage.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                        class="fa fa-recycle"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('garage.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        @endif
    </ul>
</div>