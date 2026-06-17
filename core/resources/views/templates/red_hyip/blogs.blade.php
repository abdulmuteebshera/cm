@extends($activeTemplate . 'layouts.frontend')
@section('content')

@php

    $blogContent = getContent('blog.content', true);
@endphp

<section class="blog py-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h2 class="section-heading__title"> {{ __(@$blogContent->data_values->heading) }}</h2>
                    <p class="section-heading__desc"> {{ __(@$blogContent->data_values->sub_heading) }} </p>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            @forelse($blogs as $blog)
            <div class="col-lg-4 col-md-6">
                <div class="blog-item bg-img" style="background-image: url(assets/images/thumbs/blog-bg.png);">
                    <div class="blog-item__thumb">
                        <a href="{{ route('blog.details', [slug($blog->data_values->title), $blog->id]) }}""
                            class="blog-item__thumb-link">
                            <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->image, '400x320') }}"
                                alt="">
                        </a>

                        <div class="blog-item__date">{{ showDateTime($blog->created_at, 'd') }} <span
                                class="text">{{ showDateTime($blog->created_at, 'M') }}</span></div>
                    </div>
                    <div class="blog-item__content">
                        <h4 class="blog-item__title"><a
                                href="{{ route('blog.details', [slug($blog->data_values->title), $blog->id]) }}"
                                class="blog-item__title-link">{{ __($blog->data_values->title) }}</a></h4>

                        <p class="blog-item__desc">
                            @lang(strLimit(strip_tags(@$blog->data_values->description), 100))
                        </p>
                        <ul class="text-list inline">
                            <li class="text-list__item"> <span class="icon"><i class="fas fa-user"></i></span>
                                @lang(' By Admin')</li>
                            <li class="text-list__item"> <span class="icon"><i
                                        class="fas fa-calendar-check"></i></span>
                                {{ showDateTime($blog->created_at, 'Y M d') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            @empty

            @endforelse

        </div>

       {{ $blogs->links() }}


    </div>
</section>

    @if ($sections != null)
        @foreach (json_decode($sections) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
