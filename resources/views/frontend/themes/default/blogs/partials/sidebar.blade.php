<div class="sidebar-style">
    <div class="sidebar-widget">
        <h4 class="pro-sidebar-title">Search </h4>
        <div class="pro-sidebar-search mb-55 mt-25">
            <form class="pro-sidebar-search-form" action="#">
                <input type="text" placeholder="Search here..." fdprocessedid="u68xc9o">
                <button fdprocessedid="dmm8o">
                    <i class="pe-7s-search"></i>
                </button>
            </form>
            <!-- <form action="{{ route('blogs.search') }}" method="GET">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit">Search</button>
                </form> -->



        </div>
    </div>
    <div class="sidebar-widget">
        <h4 class="pro-sidebar-title">Recent Blogs</h4>
        <div class="sidebar-project-wrap mt-30">
        @foreach($recentBlogs as $recent)
        <div class="single-sidebar-blog">
            @php
                $domain = str_replace(['http://', 'https://'], '', request()->getHost());
                $bannerPath = 'frontend/' . str_replace('.', '-', $domain) . '/img/blogs/images/';
                $fallbackPath = 'frontend/themes/default/img/blogs/images/';

                $bannerimagePath = $recent->image ?? $recent->slug.'.jpg';
                $bannerImageUrl = asset($bannerPath . $bannerimagePath);
                $fallbackImageUrl = asset($fallbackPath . $bannerimagePath);
            @endphp
            <div class="sidebar-blog-img">
                <a href="{{ route('blogs.show', $recent->slug) }}">
                    <img src="{{ $bannerImageUrl }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';" alt="{{ $recent->title }}" class="img-bank">
                </a>
            </div>
            <div class="sidebar-blog-content">
                <h4><a href="{{ route('blogs.show', $recent->slug) }}">{{ $recent->title }}</a></h4>
                <span>{{ $recent->date_added }}</span>
            </div>
        </div>
        @endforeach
        </div>
    </div>
    <div class="sidebar-widget mt-35">
        <h4 class="pro-sidebar-title">Categories</h4>
        <div class="sidebar-widget-list mt-20">
            <ul>
            @foreach($categories as $category)
                <li>
                    <div class="sidebar-widget-list-left">
                        <a href="{{ route('blogs.category', $category->slug) }}">{{ $category->title }} <span>({{ $category->blogs_count }})</span></a>
                    </div>
                    
                </li>
            @endforeach
            </ul>
        </div>
    </div>
</div>