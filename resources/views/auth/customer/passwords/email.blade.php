@extends('layouts.app')

@section('content')
    <div class="login-register-area pt-100 pb-100">
        <div class="container">
                        
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6 col-12 text-center"><img src="frontend/themes/default/img/forgot_password.jpg" class="img-adjust" alt="forgot password"></div>
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="short__item">
                        <h4>{{ __('Reset Password') }}</h4>
                            @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                    
                    <form method="POST" action="{{ route('customer.password.email') }}">
                        @csrf
                        <input id="email" type="email"
                            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                            value="{{ old('email') }}" placeholder="Email Address" required>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        <div class="col-lg-12 mt-3">
                            <x-recaptcha />
                        </div>
                        <div class="button-box">
                            <button type="submit" class="btn btn-theme btn-block">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
  
        </div>
    </div>
@endsection