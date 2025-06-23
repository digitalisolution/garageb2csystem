<!-- Footer Section -->
<footer>
    <div class="footer-area pt-70 pb-0 bg-dark">
        <div class="container">
            <div class="col-lg-12 ms-auto me-auto">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-6">
                        <a href="{{ $garage->google_reviews_link }}"><img src="frontend/themes/default/img/google-reviews.png"
                                alt="google reviews" class="img-adjust"></a>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <!-- <h3 class="mb-4">Get in Touch</h3> -->
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-widget">
                                    <i class="pe-7s-rocket"></i>
                                    <span><a href="mailto:{{ $garage->email }}">{{ $garage->email }}</a></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-widget">
                                    <i class="pe-7s-call"></i>
                                    <span><a href="tel:{{ $garage->mobile }}">{{ $garage->mobile }}</a></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="contact-widget">
                                    <i class="pe-7s-map-marker"></i>
                                    <span><a href="{{ $garage->google_map_link }}" target="_blank">{{ $garage->street }}, {{ $garage->city }}, {{ $garage->zone }},
                                        {{ $garage->country }}</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-area bg-dark pt-70 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-4">
                    <div class="copyright mb-30">
                        <!-- <div class="footer-logo mt-0">
                            <a href="index.html">
                                <img alt="" src="frontend/themes/default/img/logo/logo.png">
                            </a>
                        </div> -->
                        <p>© 2025 <a href="/">{{ $garage->garage_name }}</a><br> All Rights Reserved</p>
                    </div>
                    <div class="footer-widget">
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
                </div>

                <div class="col-lg-3 col-md-4 col-sm-4">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>TYRES</h3>
                        </div>
                        <div class="footer-list">
                            <ul>
                                <li><a href="#">Road Tyres</a></li>
                                <li><a href="#">SUV Tyres</a></li>
                                <li><a href="#">Track Tyres</a></li>
                                <li><a href="#">Drift Tyres</a></li>
                                <li><a href="#">Off Road Tyres</a></li>
                                <li><a href="#">Accelera Tyres</a></li>
                                <li><a href="#">Armstrong Tyres</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>POPULAR TYRE SIZES</h3>
                        </div>
                        <div class="footer-list">
                            <ul>
                                <li><a href="#">225 40 R18 tyres</a></li>
                                <li><a href="#">225 45 R17 tyres</a></li>
                                <li><a href="#">235 40 R18 tyres</a></li>
                                <li><a href="#">275 40 R20 tyres</a></li>
                                <li><a href="#">275 40 R22 tyres</a></li>
                                <li><a href="#">205 45 R17 tyres</a></li>
                                <li><a href="#">205 55 R16 tyres</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="footer-widget mb-30 ml-30">
                        <div class="footer-title">
                            <h3>OPENING HOURS</h3>
                        </div>
                        <div class="opening-hours-widget">
                            {!! $garage->garage_opening_time !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-3 footer-sub-links">
            <div class="container">
                <div class="footer-widget">
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
        </div>
    </div>
</footer>