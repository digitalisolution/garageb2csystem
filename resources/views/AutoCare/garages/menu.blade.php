<!-- resources/views/components/garages-menu.blade.php -->
<div class="border rounded mb-4 overflow-hidden">
    <ul class="customer_menu">
        <li><a href="AutoCare/garages/details/{{ $garages->id }}"
                class="{{ request()->routeIs('AutoCare.garages.details') ? 'active' : '' }}"><i
                    class="fa fa-user"></i>Profile</a></li>
        <li><a href="AutoCare/garages/details/{{ $garages->id }}/orders"
                class="{{ request()->routeIs('AutoCare.garages.orders') ? 'active' : '' }}"><i
                    class="fa fa-briefcase"></i>Orders</a></li>
        <li><a href="AutoCare/garages/details/{{ $garages->id }}/invoices"
                class="{{ request()->routeIs('AutoCare.garages.invoices') ? 'active' : '' }}"><i
                    class="fa fa-files-o"></i> Invoices</a></li>
        <li><a href="AutoCare/garages/details/{{ $garages->id }}/statements"
                class="{{ request()->routeIs('AutoCare.garages.statement') ? 'active' : '' }}"><i
                    class="fa fa-file-o"></i> Statements</a></li>
    </ul>
</div>