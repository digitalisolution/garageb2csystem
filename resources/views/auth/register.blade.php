@extends('layouts.app')

@section('content')
<div class="pt-60 pb-60">
    <div class="container">
        <div class="register_bg">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-6 col-12">
                        <div class="p-3 ml-30 text-center">
                            <h2 class="text-white">Welcome Back!</h2>
                            <p class="text-white">To keep connected with us please login with your personal info</p>
                            <a href="webmaster/login" class="btn btn-theme btn-block">Login</a>
                        </div>
                    </div>
            <div class="col-lg-8 col-md-6 col-12">
                <div class="short__item b-radius-0">
                    <div class="text-center bg-gray p-2 mb-3 rounded">
                        <h2 class="mb-0">{{ __('Register') }}</h2>
                    </div>
                            <form method="POST" action="{{ route('webmaster.register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Name<sup>*</sup></label>
                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="Name" required autofocus>
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Email<sup>*</sup></label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Email" required>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Password<sup>*</sup></label>
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <label>Confirm Password<sup>*</sup></label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                                <x-recaptcha />
                            </div>
                            <div class="button-box">
                                <button type="submit" class="btn btn-theme btn-block">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div> 
</div> 
@endsection
