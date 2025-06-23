@extends('layouts.app')

@section('content')
    <div class="breadcrumb-area pt-35 pb-35 bg-gray-3">
        <div class="container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li>
                        <a href="/">Home</a>
                    </li>
                    <li class="active">Sitemap</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="pt-70 pb-70">
        <div class="container">
            @if ($servicesList->isNotEmpty())
                <!-- Services Section -->
                <div class="sitemap_wrap mb-30">
                    <h3>Services List</h3>
                    <div class="sitemap_bank">

                        @foreach ($servicesList as $service)
                            <a href="{{ route('slug.handle', $service->slug) }}" class="">{{ $service->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
            @if ($infoPages->isNotEmpty())
                <!-- Information Pages Section -->
                <div class="sitemap_wrap mb-30">
                    <h3>Information Pages</h3>
                    <div class="sitemap_bank">

                        @foreach ($infoPages as $page)
                            <a href="{{ route('slug.handle', $page->slug) }}" class="">{{ $page->title }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($blogsList->isNotEmpty())
                <!-- Information Pages Section -->
                <div class="sitemap_wrap mb-30">
                    <h3>Blogs List</h3>
                    <div class="sitemap_bank">
                        <a href="blogs" class="">Blogs</a>
                        @foreach ($blogsList as $blogs)
                            <a href="{{ route('slug.handle', 'blogs/' .$blogs->slug) }}" class="">{{ $blogs->title }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($tyreBrandList->isNotEmpty())
                <!-- Tyre Brands Section -->
                <div class="sitemap_wrap mb-30">
                    <h3>Brands List</h3>
                    <div class="sitemap_bank">

                        @foreach ($tyreBrandList as $tyreBrand)
                            <a href="{{ route('slug.handle', 'brand/' . $tyreBrand->slug) }}" class="">{{ $tyreBrand->name }}</a>
                        @endforeach

                    </div>
                </div>
            @endif

            @if (!empty($tyreSizes))
                <!-- Tyre Sizes Section -->
                <div class="sitemap_wrap mb-30">
                    <h3>Tyre Sizes</h3>
                    <div class="sitemap_bank">
                        @foreach ($tyreSizes as $tyre)
                            <?php        //echo "<pre>"; print_r($tyre); ?>
                            @if($tyre['tyre_width'] != '' && $tyre['tyre_profile'] != '' && $tyre['tyre_diameter'] != '')
                                <a
                                    href="{{ url('/tyres-size/' . $tyre['tyre_width'] . '-' . $tyre['tyre_profile'] . '-' . $tyre['tyre_diameter']) }}">
                                    {{ $tyre['tyre_width'] }} / {{ $tyre['tyre_profile'] }} R{{ $tyre['tyre_diameter'] }}</a>
                            @endif

                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection