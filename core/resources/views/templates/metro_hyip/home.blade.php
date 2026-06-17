@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $banner = getContent('banner.content',true);
@endphp
<!--========================== Banner Section Start ==========================-->
<section class="banner-section">
  <div class="planet-bg">
      <img src="{{ asset($activeTemplateTrue . 'images/shapes/planet-bg.png') }}" alt="">
  </div>
  <div class="planet-small">
      <img src=" {{ asset($activeTemplateTrue . 'images/shapes/planet-small.png') }}" alt="">
  </div>
  <span class="banner-section__icon animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-one animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-two animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-three animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-four animated">
    <i class="las la-star"></i>
</span> 
<span class="banner-section__icon-five animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-six animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-seven animated">
    <i class="las la-star"></i>
</span>
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-xl-5 col-md-7">
            <div class="banner-content">
                <h2 class="banner-content__title">  {{ __(@$banner->data_values->heading) }}</h2>
                <div class="banner-content__buttons">
                    <a href="{{ @$banner->data_values->button_link }}" class="btn btn--base">
                        {{ __(@$banner->data_values->button_name) }}</a>
                    <a href="{{@$banner->data_values->button_two_link }}" class="btn btn--base">
                        {{ __(@$banner->data_values->button_two_name) }}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-7 col-md-5">
          <div class="city-scene">
              <div class="bd-1">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-1-4.png') }}">
              </div>
               <div class="bd-2">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-2.png') }}">
              </div>
            <div class="bd-3">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-3-10.png') }}">
              </div>
              <div class="bd-4">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-1-4.png') }}">
              </div>
              <div class="bd-5">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-5.png') }}">
              </div>
            <div class="bd-6">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-6.png') }}">
              </div>
              <div class="bd-7">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-7.png') }}">
              </div>
              <div class="bd-8">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-3-10.png') }}">
              </div>
              <div class="bd-9">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-9-11.p') }}ng">
              </div>
              <div class="bd-10">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-8.png') }}">
              </div>
              <div class="bd-11">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-9-11.png') }}">
              </div>
              <div class="bd-12">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-12.png') }}">
              </div>
              <div class="bd-13">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-13.png') }}">
              </div>
              <div class="bd-14">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-14.png') }}">
              </div>
              <div class="bd-15">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/london-bridge-14.png') }}" >
              </div>
              <div class="bd-16">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-16.png') }}">
              </div>
              <div class="bd-17">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-17.png') }}">
              </div>
             <div class="bd-18">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-18.png') }}">
              </div>
              <div class="bd-19">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-19.png') }}">
              </div>
              <div class="bd-20">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-20.png') }}">
              </div>
          </div>
        </div>
    </div>
</div>
</section>
<!--========================== Banner Section End ==========================-->
<!-- =========================start train section =============================-->

<div class="train-section">
  <div class="train-wrapper">
      <div class="train" bg="{{ asset($activeTemplateTrue . 'images/shapes/train.png') }}">
          <img src="{{ asset($activeTemplateTrue . 'images/shapes/train.png') }}" alt="">
      </div>
  </div>
  <div class="railway">
  </div>
</div>

<!-- ========================end train section =========================-->

    @if($sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
