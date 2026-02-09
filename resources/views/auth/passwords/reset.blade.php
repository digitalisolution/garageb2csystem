@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6 col-12 text-center"><img src="frontend/themes/default/img/forgot_password.jpg" class="img-adjust" alt="forgot password"></div>
            <div class="col-lg-5 col-md-6 col-12">
                <div class="short__item">
                    <h4>{{ __('Reset Password') }}</h4>

                        <form method="POST" action="{{ route('webmaster.password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label for="email"
                                    class="col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <input id="email" type="email"
                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                        value="{{ $email ?? old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group">
                                <label for="password"
                                    class="col-form-label text-md-right">{{ __('Password') }}</label>

                                    <input id="password" type="password"
                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                        name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                            </div>

                            <div class="form-group">
                                <label for="password-confirm"
                                    class="col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required>
                            </div>

                            <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-theme btn-block">
                                        {{ __('Reset Password') }}
                                    </button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection