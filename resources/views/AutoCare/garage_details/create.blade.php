@extends('samples')

@section('content')
    <div class="container-fluid">
        <div class="bg-white p-3">

            <form
                action="{{ isset($garage) ? route('AutoCare.garage_details.update', $garage->id) : route('AutoCare.garage_details.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($garage))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="garage_name">Garage Name:</label>
                        <input class="form-control" type="text" id="garage_name" name="garage_name"
                            value="{{ old('garage_name', $garage->garage_name ?? '') }}" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="company_number">Company Number:</label>
                        <input class="form-control" type="text" id="company_number" name="company_number"
                            value="{{ old('company_number', $garage->company_number ?? '') }}">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="vat_number">Vat Number:</label>
                        <input value="{{ old('vat_number', $garage->vat_number ?? '') }}" class="form-control" type="text"
                            id="vat_number" name="vat_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="eori_number">EORI Number:</label>
                        <input value="{{ old('eori_number', $garage->eori_number ?? '') }}" class="form-control" type="text"
                            id="eori_number" name="eori_number">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="phone">Phone:</label>
                        <input value="{{ old('phone', $garage->phone ?? '') }}" class="form-control" type="text" id="phone"
                            name="phone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="mobile">Mobile:</label>
                        <input value="{{ old('mobile', $garage->mobile ?? '') }}" class="form-control" type="text"
                            id="mobile" name="mobile" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="email">Email:</label>
                        <input value="{{ old('email', $garage->email ?? '') }}" class="form-control" type="email" id="email"
                            name="email" required>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="street">Street:</label>
                        <input value="{{ old('street', $garage->street ?? '') }}" class="form-control" type="text"
                            id="street" name="street">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="city">City:</label>
                        <input value="{{ old('city', $garage->city ?? '') }}" class="form-control" type="text" id="city"
                            name="city">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="zone">Zone:</label>
                        <input value="{{ old('zone', $garage->zone ?? '') }}" class="form-control" type="text" id="zone"
                            name="zone">
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 form-group">
                        <label for="country">Country:</label>
                        <input value="{{ old('country', $garage->country ?? '') }}" class="form-control" type="text"
                            id="country" name="country">
                    </div>

                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        <label for="garage_opening_time">Garage Opening Time:</label>
                        <input value="{{ old('garage_opening_time', $garage->garage_opening_time ?? '') }}"
                            class="form-control" type="text" id="garage_opening_time" name="garage_opening_time">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="logo">Logo:</label>
                        <input value="{{ old('logo', $garage->logo ?? '') }}" class="form-control" type="file" id="logo"
                            name="logo">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="banner">Banner:</label>
                        <input value="{{ old('banner', $garage->banner ?? '') }}" class="form-control" type="file"
                            id="banner" name="banner">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="favicon">Favicon:</label>
                        <input value="{{ old('favicon', $garage->favicon ?? '') }}" class="form-control" type="file"
                            id="favicon" name="favicon">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_facebook">Facebook:</label>
                        <input value="{{ old('social_facebook', $garage->social_facebook ?? '') }}" class="form-control"
                            type="url" id="social_facebook" name="social_facebook">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_instagram">Instagram:</label>
                        <input value="{{ old('social_instagram', $garage->social_instagram ?? '') }}" class="form-control"
                            type="url" id="social_instagram" name="social_instagram">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_twitter">Twitter:</label>
                        <input value="{{ old('social_twitter', $garage->social_twitter ?? '') }}" class="form-control"
                            type="url" id="social_twitter" name="social_twitter">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="social_youtube">Youtube:</label>
                        <input value="{{ old('social_youtube', $garage->social_youtube ?? '') }}" class="form-control"
                            type="url" id="social_youtube" name="social_youtube">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_map_link">Google Map Link:</label>
                        <input value="{{ old('google_map_link', $garage->google_map_link ?? '') }}" class="form-control"
                            type="url" id="google_map_link" name="google_map_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="longitude">Longitude:</label>
                        <input value="{{ old('longitude', $garage->longitude ?? '') }}" class="form-control" type="text"
                            id="longitude" name="longitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="latitude">Latitude:</label>
                        <input value="{{ old('latitude', $garage->latitude ?? '') }}" class="form-control" type="text"
                            id="latitude" name="latitude">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_link">Google Reviews Link:</label>
                        <input value="{{ old('google_reviews_link', $garage->google_reviews_link ?? '') }}"
                            class="form-control" type="url" id="google_reviews_link" name="google_reviews_link">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_stars">Google Reviews Stars:</label>
                        <input value="{{ old('google_reviews_stars', $garage->google_reviews_stars ?? '') }}"
                            class="form-control" type="text" id="google_reviews_stars" name="google_reviews_stars">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="google_reviews_count">Google Reviews Count:</label>
                        <input value="{{ old('google_reviews_count', $garage->google_reviews_count ?? '') }}"
                            class="form-control" type="text" id="google_reviews_count" name="google_reviews_count">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="website_url">Website URL:</label>
                        <input value="{{ old('website_url', $garage->website_url ?? '') }}" class="form-control" type="url"
                            id="website_url" name="website_url">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ isset($garage) && $garage->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ isset($garage) && $garage->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"
                            class="form-control">{{ $garage->description ?? old('description') }}</textarea>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="notes">Notes:</label>
                        <textarea name="notes" id="notes"
                            class="form-control">{{ $garage->notes ?? old('notes') }}</textarea>
                    </div>

                </div>
                <div class="text-right"><button class="btn btn-primary mt-2"
                        type="submit">{{ isset($garage) ? 'Update' : 'Save' }}</button></div>

            </form>
        </div>
    </div>
@endsection