@extends('layouts.app')

@section('content')
    <div class="login-register-area pt-100 pb-100">
        <div class="container">

            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6 col-12"><img src="frontend/themes/default/img/login__infographic.webp" class="img-adjust" alt="login infographic"></div>
                <div class="col-lg-5 col-md-6 col-12">
                    <div class="short__item">
                        <h3>{{ __('Login') }}</h3>
                        <p class="mt-3">Don't have an account? <a href="{{ route('webmaster.register') }}">Register an account</a></p>
                        
                        <form method="POST" action="{{ route('webmaster.login') }}">
                            <div class="form-group">
                                <label>Email</label>
                                @csrf
                                    <input id="email" type="email" placeholder="Email"
                                        class="{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                        value="{{ old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                    <input id="password" placeholder="Password" type="password"
                                        class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                                        required>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                    <div class="button-box">
                                        <div class="login-toggle-btn d-flex mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                            </div>
                                            @if (Route::has('webmaster.password.request'))
                                                <a href="{{ route('webmaster.password.request') }}" class="ml-auto">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            @endif
                                        </div>
                                        <div class="col-lg-12">
                                            <x-recaptcha />
                                        </div>
                                        <button type="submit" class="btn btn-theme btn-block">
                                            {{ __('Login') }}
                                        </button>
                                    </div>
                                    
                                </form>
                    </div>
                </div>
            </div>




        </div>
    </div>

@endsection