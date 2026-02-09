<!-- Footer Section -->
<footer class="footer_element">
    <div class="footer-area">
        <div class="container">
            <div class="footer-topele mb-5">
                <div class="foot_logo"><img src="frontend/www-garage-automation-co-uk/img/logo/foot-logo.png" loading="lazy"></div>
                <a href="{{ $garage->google_map_link }}" target="_blank">{{ $garage->street }}, <br class="hidden-xs">{{ $garage->city }}, {{ $garage->zone }},
                                        {{ $garage->country }}</a>
            </div>
        </div>
    </div>
    <div class="footer-area">
        <div class="container">
            <div class="row">
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>We are Open</h3>
                        </div>
                        <div class="opening-hours-widget">
                            {!! $garage->garage_opening_time !!}

                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>Helpful Links</h3>
                        </div>
                        <div class="footer-list">
                            <ul>
                                <li><a href="about-us">About Us </a></li>
                                <li><a href="contact">Contact Us </a></li>
                                <li><a href="terms">Terms and Conditions </a></li>
                                <li><a href="privacy-policy">Privacy Policy </a></li>
                                <li><a href="cookies">Cookies Policy</a></li>
                                <li><a href="sitemap">Sitemap </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>Tyres Online</h3>
                        </div>
                        <div class="footer-list">
                            <ul>
                                <li><a href="winter-tyres">Winter Tyres</a></li>
                                <li><a href="run-flat-tyres">Run Flat Tyres</a></li>
                                <li><a href="4x4-tyres">4x4 Tyres</a></li>
                                <li><a href="car-tyres">Car Tyres</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>Tyre Information</h3>
                        </div>
                        <div class="footer-list">
                            <ul>
                                <li><a href="brands">Best Tyre Brands</a></li>
                                <li><a href="#">Filter Registration</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>Contact Us</h3>
                        </div>
                        <div class="contact-widget">
                            <span>
                                Telephone<br>
                                <a href="tel:{{ $garage->mobile }}" class="telephone">{{ $garage->mobile }}</a></span>
                            </div>
                        <div class="contact-widget">
                            <span>
                                Email Us<br>
                                <a href="mailto:{{ $garage->email }}">{{ $garage->email }}</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-3 footer-sub-links">
            <div class="container">
                <div class="footer-end">
                    <div>
                        <p class="mb-0">© 2025 <a href="/">{{ $garage->garage_name }}</a> All Rights Reserved.</p>
                        <div class="social-list">
                            @if(!empty($garage->social_facebook))
                                <a href="{{ $garage->social_facebook }}" target="_blank">
                                    <img src="frontend/themes/default/img/social-media/facebook.png" alt="facebook">
                                </a>
                            @endif

                            @if(!empty($garage->social_instagram))
                                <a href="{{ $garage->social_instagram }}" target="_blank">
                                    <img src="frontend/themes/default/img/social-media/instagram.png" alt="instagram">
                                </a>
                            @endif

                            @if(!empty($garage->social_twitter))
                                <a href="{{ $garage->social_twitter }}" target="_blank">
                                    <img src="frontend/themes/default/img/social-media/yell.png" alt="twitter">
                                </a>
                            @endif

                            @if(!empty($garage->google_map_link))
                                <a href="{{ $garage->google_map_link }}" target="_blank">
                                    <img src="frontend/themes/default/img/social-media/map.png" alt="google map">
                                </a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <img src="frontend/themes/default/img/payment.webp" alt="pay icons">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>