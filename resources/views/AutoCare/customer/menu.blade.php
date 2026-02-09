<!-- resources/views/components/customer-menu.blade.php -->
<div class="border rounded mb-4 overflow-hidden">
    <ul class="customer_menu">
        <li><a href="AutoCare/customer/details/{{ $customer->id }}"
                class="{{ request()->routeIs('AutoCare.customer.details') ? 'active' : '' }}"><i
                    class="fa fa-user"></i>Profile</a></li>
        <li>
            <a href="{{ route('AutoCare.customer.vehicles', ['id' => $customer->id]) }}"
                class="{{ request()->routeIs('AutoCare.customer.vehicles', 'AutoCare.customer.vehicles.create', 'AutoCare.customer.vehicles.edit') ? 'active' : '' }}">
                <i class="fa fa-car"></i> Vehicles
            </a>
        </li>

        <li><a href="AutoCare/customer/details/{{ $customer->id }}/orders"
                class="{{ request()->routeIs('AutoCare.customer.orders') ? 'active' : '' }}"><i
                    class="fa fa-briefcase"></i>Orders</a></li>
        <li><a href="AutoCare/customer/details/{{ $customer->id }}/invoices"
                class="{{ request()->routeIs('AutoCare.customer.invoices') ? 'active' : '' }}"><i
                    class="fa fa-files-o"></i> Invoices</a></li>
        <li><a href="AutoCare/customer/details/{{ $customer->id }}/statements"
                class="{{ request()->routeIs('AutoCare.customer.statement') ? 'active' : '' }}"><i
                    class="fa fa-file-o"></i> Statements</a></li>

        <!-- @if (Auth::guard('customer')->check())
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
        @endif -->
    </ul>
</div>