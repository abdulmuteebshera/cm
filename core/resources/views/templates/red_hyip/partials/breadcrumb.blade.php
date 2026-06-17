@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

<section class="breadcumb bg-img bg-overlay-one" style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/breadcumb-bg.png') }});">
    <div class="breadcumb__shape"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="breadcumb__wrapper">
                    <h2 class="breadcumb__title">   {{ __($pageTitle) }}</h2>
                    <ul class="breadcumb__list">
                        <li class="breadcumb__item"><a href="{{ route('home')}}" class="breadcumb__link"> <i class="las la-home"></i> @lang('Home')</a> </li>
                        <li class="breadcumb__item">//</li>
                        <li class="breadcumb__item"> <span class="breadcumb__item-text">   {{ __($pageTitle) }} </span> </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="breadcumb__thumb">
                    <img src="{{ getImage('assets/images/frontend/breadcrumb/' . @$breadcrumbContent->data_values->image,'550x340') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
