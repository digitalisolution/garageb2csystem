@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')
@section('meta_title', $metaTitle)
@section('meta_description', $metaDescription)
@section('content')
<div class="breadcrumb-area brand_breadcrumb">
    <img src="https://www.digitalideasltd.in/frontend/themes/default/img/banner/content-pages/common_image.jpg" alt="Default Banner" class="img-bank">
    <div class="brand_name">
        <h1>Our Blogs</h1>
    </div>
</div>

<div class="pt-70 pb-70 content_pages">
    <div class="container">
        <div class="page-content">
            <div class="Blog-area">
                <div class="container">
                    <div class="row flex-row-reverse">
                        <div class="col-lg-9">
                            <div class="ml-20">
                                <div class="row">
                                    @foreach($blogs as $blog)
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="blog-wrap-2 mb-30">
                                        <div class="blog-img-2">
                                            <a href="{{ route('blogs.show', $blog->slug) }}"><img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}"></a>
                                        </div>
                                        <div class="blog-content-2">
                                                <div class="blog-meta-2">
                                                    <ul>
                                                        <li>{{ $blog->date_added }}</li>
                                                        <li>{{ $blog->view }} <i class="fa fa-comments-o"></i></li>
                                                    </ul>
                                                </div>
                                                <h4><a href="{{ route('blogs.show', $blog->slug) }}">{{ $blog->title }}</a></h4>
                                                <p class="description line-clamp">{{ Str::limit(strip_tags($blog->description), 150) }}</p>

                                                <div class="blog-share-comment">
                                                    <div class="blog-btn-2">
                                                        <a href="{{ route('blogs.show', $blog->slug) }}" class="read-more">read more</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                </div>
                                <div class="text-center mt-20">
                                    {{ $blogs->links() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            @include('blogs.partials.sidebar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">.pagination{justify-content: center;}</style>
@endsection
@push('styles')
<style>
.line-clamp{display: -webkit-box;-webkit-line-clamp: 4;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;max-height: 6.2em;line-height: 1.55em;}
.read-more{display: inline-block;margin-top: 8px;color: #007bff;text-decoration: underline;}
</style>
@endpush
