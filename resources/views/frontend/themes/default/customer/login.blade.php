@extends('layouts.app')
@section('content')
    <div class="pt-60 pb-60">
        <div class="container">
            <div class="login_container">
                <div class="left-panel">
                    
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to<br>please login with your personal info</p>
                    <h4 class="text-white mb-3">Don't have an account?</h4>
                    <a href="customer/register" class="sign-in-btn">SIGN UP</a>
                    <div class="footer-text">or continue • scroll down</div>
                </div>
                <div class="right-panel">
                    <h2>Welcome</h2>
                    <p class="subtitle">Login to your account to continue</p>
                    <form method="POST" action="{{ route('customer.login') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required>
                                @error('customer_email')
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
                            <div class="forgot-password">
                            @if (Route::has('customer.password.request'))
                                <a href="{{ route('customer.password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                            <div class="col-lg-12">
                                <x-recaptcha />
                            </div>
                            <button type="submit" class="btn btn-theme btn-block">Login</button>
                        </form>
                        <div class="signup-text">
                    Don't have an account? <a href="customer/register">Sign Up</a>
                </div>
                </div>
            </div>
            <style>
                .login_container {background: white;border-radius: 20px;box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);overflow: hidden;max-width: 900px;width: 100%;display: flex;min-height: 500px;margin:auto;}
.left-panel {background:linear-gradient(135deg, #af021b 0%, #e61937 100%);color: white;padding: 60px 50px;flex: 1;display: flex;flex-direction: column;justify-content: center;align-items: center;text-align: center;position: relative;overflow: hidden;}
.left-panel::before {content: '';position: absolute;bottom: -50px;left: -50px;width: 200px;height: 200px;background: rgba(255, 255, 255, 0.1);border-radius: 50%;}
.left-panel::after {content: '';position: absolute;top: -50px;right: -50px;width: 300px;height: 300px;background: rgba(255, 255, 255, 0.05);border-radius: 50%;}
.brand-name {font-size: 18px;font-weight: 500;margin-bottom: 40px;position: relative;z-index: 1;}
.left-panel h1 {font-size: 32px;font-weight: 700;margin-bottom: 15px;position: relative;z-index: 1;color:#fff;}
.left-panel p {font-size: 14px;opacity: 0.9;margin-bottom: 40px;position: relative;z-index: 1;color:#fff;}
.sign-in-btn {background: transparent;border: 2px solid white;color: white;padding: 12px 50px;border-radius: 25px;font-size: 14px;font-weight: 600;cursor: pointer;transition: all 0.3s;position: relative;z-index: 1;}
.sign-in-btn:hover {background: white;color: #e61937;}
.footer-text {position: absolute;bottom: 30px;font-size: 11px;opacity: 0.7;}
.right-panel {flex: 1;padding: 60px 50px;display: flex;flex-direction: column;justify-content: center;}
.right-panel h2 {color:#e61937;font-size: 28px;font-weight: 700;margin-bottom: 10px;}
.subtitle {color: #666;font-size: 13px;margin-bottom: 30px;}
.form-group {margin-bottom: 20px;}
.form-group label {display: block;color: #e61937;font-size: 13px;font-weight: 500;margin-bottom: 8px;}
.form-group input {width: 100%;padding: 12px 15px;border: 1px solid #d1d5db;border-radius: 8px;font-size: 14px;transition: border-color 0.3s;background: #f9fafb;}
.form-group input:focus {outline: none;border-color: #e61937;background: white;}
.forgot-password {text-align: right;margin-bottom: 25px;}
.forgot-password a {color: #e61937;font-size: 12px;text-decoration: none;}
.forgot-password a:hover {text-decoration: underline;}
.login-btn {width: 100%;background: #e61937;color: white;border: none;padding: 12px;border-radius: 25px;font-size: 14px;font-weight: 600;cursor: pointer;transition: background 0.3s;}
.login-btn:hover {background: #0d5a3a;}
.signup-text {text-align: center;margin-top: 20px;font-size: 13px;color: #666;}
.signup-text a {color: #e61937;text-decoration: none;font-weight: 600;}
.signup-text a:hover {text-decoration: underline;}
@media (max-width: 768px) {
.login_container {flex-direction: column;}
.left-panel {padding: 40px 30px;min-height: 300px;}
.left-panel h1 {font-size: 26px;}
.right-panel {padding: 40px 30px;}
.right-panel h2 {font-size: 24px;}
.footer-text {position: relative;bottom: 0;margin-top: 20px;}
}

@media (max-width: 480px) {
.left-panel, .right-panel {padding: 30px 20px;}
.login_container {border-radius: 15px;}
}
            </style>
            
        </div>
    </div>
@endsection