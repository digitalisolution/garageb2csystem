@extends('layouts.app')

@section('content')

    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li>
                        <a href="#">Home</a>
                    </li>
                    <li class="active">Appointment</li>
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
            <div class="mb-4 mt-4">
                <h1>Book Appointment</h1>
                <form method="POST" action="{{ route('appointment.store') }}">
                    @csrf
                    <div class="appointment_wrap">
                        <div class="form-bg">
                            <div class="row">
                                <h3>Vehicle Information</h3>
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
                                        <input type="text" name="vehicle_make" placeholder="Make" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vehicle_model" placeholder="Model" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="vehicle_year" placeholder="Year" class="form-control">
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
                                <h3>Personal Information</h3>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="first_name" placeholder="First Name*" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="text" name="last_name" placeholder="Last Name*" class="form-control"
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
                                        <input class="form-control" name="phone" placeholder="Phone" type="text"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <textarea name="message" placeholder="Message (optional)"
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h3>Choose Date and Time</h3>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="date" name="choose_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="time" name="choose_time" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-12">
                                    <x-recaptcha />
                                </div>
                                <div class="col-lg-12">
                                    <button class="btn btn-theme border" type="submit">SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection