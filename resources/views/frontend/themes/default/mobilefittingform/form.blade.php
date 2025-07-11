@extends('layouts.app')

@section('content')

    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li>
                        <a href="/">Home</a>
                    </li>
                    <li class="active">Mobile Fitting Availablitiy</li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <div class="page-content">
            <div class="mobile-fitting-module">
                <div class="p-4 text-center">
                <img src="frontend/themes/default/img/gps-map.webp" alt="gps map" class="img-adjust">
                    <div class="mapinfo">
                    <h2>Great News !!!</h2>
                    <h3>MOBILE TYRE FITTING AVAILABLE NEAR YOU</h3>
                    <h4>We have 4 fitters active in your area</h4>
                    <p>Last Updated: <strong>Just Now</strong></p>
                    </div>
                 </div>
                 <div class="row hidden">
                    <div class="col-6 text-center"><div class="mobilevan"><div class="value">3</div><img src="frontend/themes/default/img/red-van.webp" alt="Busy Van" class="img-adjust"></div></div>
                    <div class="col-6 text-center"><div class="mobilevan"><div class="value">3</div><img src="frontend/themes/default/img/green-van.webp" alt="Available Van" class="img-adjust"></div></div>
                 </div>
                 @php
                    $firstChar = strtoupper(substr($postcode, 0, 1));

                    // Map of letter => array of image filenames
                    $vanImagesMap = [
                        'A' => ['available-van.webp', 'available-van.webp', 'busy-van.webp',  'busy-van.webp'],
                        'B' => ['available-van.webp', 'busy-van.webp'],
                        'C' => ['busy-van.webp', 'available-van.webp', 'busy-van.webp'],
                        'D' => ['busy-van.webp','busy-van.webp', 'available-van.webp'],
                        'N' => ['available-van.webp','busy-van.webp', 'available-van.webp'],
                        // Add more as needed
                    ];

                    $vanImages = $vanImagesMap[$firstChar] ?? ['available-van.webp','busy-van.webp'];
                    @endphp

                    <div class="row text-center">
                    @foreach($vanImages as $img)
                        <div class="col-6 col-md-3 mb-3">
                            <img src="{{ asset('frontend/themes/default/img/' . $img) }}" alt="Van" class="img-adjust">
                        </div>
                    @endforeach
                    </div>

            </div>

            <div class="row my-5">
                <div class="col-lg-4 col-12">
                    <div class="bg-light border p-4 rounded">
                        <h4>Ready for fast, reliable tyre service? Contact FastTyre today.</h4>
                        <a href="tel:{{ $garage->mobile }}" class="btn btn-theme btn-block"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-5 w-5 mr-2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> Call: {{ $garage->mobile }}</a>
                    </div>
                </div>
                <div class="col-lg-8 col-12">
                    <div class="bg-light border p-4 rounded">
                        <h3>Book Postcode Mobile Fitting Availablitiy</h3>
                        <form method="POST" action="{{ route('mobilefittingform.store') }}">
                            @csrf
                            <div class="mobilefittingform_wrap">
                                <div class="form-bg">
                                    <div class="row">
                                        <h4>Vehicle Information</h4>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <select class="form-control" name="vehicle_type" required>
                                                    <option value="">Select Vehicle*</option>
                                                    <option value="CAR">CAR</option>
                                                    <option value="4X4">4X4</option>
                                                    <option value="VAN">VAN</option>
                                                    <option value="MOTORBIKE">MOTORBIKE</option>
                                                    <option value="TRUCK">TRUCK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input name="postcode" type="text" class="form-control" placeholder="Postcode" value="{{ old('postcode', $postcode ?? '') }}">


                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input class="form-control" name="tyresize"
                                                    placeholder="Your Tyre Size (exp. 205/55R16)" type="tyresize">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h4>Personal Information</h4>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input type="text" name="first_name" placeholder="Full Name*" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input class="form-control" name="email" placeholder="Email*" type="email"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input class="form-control" name="phone" placeholder="Telephone" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <textarea name="message" placeholder="Message (optional)"
                                                    class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <x-recaptcha />
                                            </div>
                                        </div> 
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <button class="btn btn-theme" type="submit">SUBMIT</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection