@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Welcome, {{ $customer->customer_name }}</h1>

    <h2>Your Profile</h2>
    <form action="{{ route('customer.updateProfile') }}" method="POST">
        @csrf

        <label for="customer_name">Name:</label>
        <input type="text" name="customer_name" value="{{ $customer->customer_name }}" required>

        <label for="customer_email">Email:</label>
        <input type="email" name="customer_email" value="{{ $customer->customer_email }}" required>

        <label for="customer_contact_number">Contact Number:</label>
        <input type="text" name="customer_contact_number" value="{{ $customer->customer_contact_number }}" required>

        <label for="customer_address">Address:</label>
        <textarea name="customer_address">{{ $customer->customer_address }}</textarea>

        <button type="submit">Update Profile</button>
    </form>
</div>
@endsection