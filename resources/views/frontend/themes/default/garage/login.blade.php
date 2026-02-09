@extends('layouts.app')
@section('content')
    <div class="pt-60 pb-60">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6 col-12"><img src="frontend/themes/default/img/garagelogin-infographic1.webp"
                        class="img-adjust" alt="login infographic"></div>
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="short__item">
                        <h3>Garage Login</h3>
                        <small>Doesn't have an account yet? <a href="garage/auth/register">Create garage account</a></small>
                        <form method="POST" action="{{ route('garage.login.submit') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="garage_email" value="{{ old('garage_email') }}" required>
                                @error('garage_email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @if (Route::has('garage.password.request'))
                                <a href="{{ route('garage.password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                            <div class="col-lg-12">
                                <x-recaptcha />
                            </div>
                            <button type="submit" class="btn btn-theme btn-block">Garage Login</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection