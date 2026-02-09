@extends('layouts.app')
@section('content')
<div class="pt-60 pb-60">
<div class="container">
    <div class="border rounded mb-4">
        <ul class="customer_menu">
            <li><a href="customer/myaccount"><i class="fa fa-user"></i> My Account</a></li>
            <li><a href="customer/forgot-password" class="active"><i class="fa fa-key"></i> Forgot Password</a></li>
            <li><a href="customer/orders"><i class="fa fa-briefcase"></i> Orders</a></li>
            <li><a href="customer/invoices"><i class="fa fa-files-o"></i> Invoices</a></li>
            <li><a href="customer/vehicles" class=""><i class="fa fa-car"></i> Vehicles</a></li>
            <li><a href="customer/statement"><i class="fa fa-file-o"></i> Statement</a></li>
            <li><a href="{{ route('customer.logout') }}"><i class="fa fa-recycle"></i> Logout</a></li>
        </ul>
    </div>
    <div class="row align-items-center">
        <div class="col-lg-7 col-md-6 col-12"><img src="frontend/themes/default/img/forgot_password.jpg" class="img-adjust" alt="forgot password"></div>
        <div class="col-lg-5 col-md-6 col-12">
            <div class="short__item">
                <h3>Forgot your password?</h3>
            <form method="POST" action="{{ route('customer.login') }}" class="mt-3">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-theme btn-block">Forgot Password</button>
            </form>
        </div>
        </div>
    </div>
</div>
</div>
@endsection