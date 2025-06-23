@extends('layouts.app')
@section('meta_title', $metaTitle)
@section('meta_keywords', $metaKeywords)
@section('meta_description', $metaDescription)
@section('content')
<div class="breadcrumb-area brand_breadcrumb">
    <img src="https://www.digitalideasltd.in/frontend/themes/default/img/banner/content-pages/common_image.jpg" alt="Default Banner" class="img-bank">
    <div class="brand_name">
        <h1>Blogs Details</h1>
    </div>
</div>
<div class="pt-70 pb-70 content_pages">
    <div class="container">
        <div class="page-content">
            <div class="Blog-area">
                <div class="container">
                    <div class="row flex-row-reverse">
                        <div class="col-lg-9">
                            <div class="blog-details-wrapper ml-20">
                                <div class="blog-details-top">
                                    <div class="blog-details-img">
                                        <img alt="{{ $blog->title }}" src="{{ asset($blog->image) }}">
                                    </div>
                                    <div class="blog-details-content">
                                        <div class="blog-meta-2">
                                            <ul>
                                                <li>{{ $blog->date_added }}</li>
                                                <li>{{ $blog->view }}<i class="fa fa-comments-o"></i></li>
                                            </ul>
                                        </div>
                                        <h1>{{ $blog->title }}</h1>
                                        <p>{!! $blog->description !!}</p>
                                    </div>
                                </div>
                                <div class="next-previous-post">
                                    <a href="#"> <i class="fa fa-angle-left"></i> prev post</a>
                                    <a href="#">next post <i class="fa fa-angle-right"></i></a>
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














@endsection
