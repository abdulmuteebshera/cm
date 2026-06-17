
<section class="breadcumb ">
    <div class="breadcumb__inner bg-img" style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/bd-1.png') }});">
        <div class="breadcumb__bg"></div>
    <div class="container">
        <div class="row ">
            <div class="col-lg-8">
                <div class="breadcumb__wrapper">
                    <h2 class="breadcumb__title"> {{ __($pageTitle) }}</h2>
                    <ul class="breadcumb__list">
                        <li class="breadcumb__item"><a href="{{ route('home')}}" class="breadcumb__link"> <i class="las la-home"></i> @lang('Home')</a> </li>
                        <li class="breadcumb__item">//</li>
                        <li class="breadcumb__item"> <span class="breadcumb__item-text"> {{ __($pageTitle) }} </span> </li>
                    </ul>
                </div>
            </div>
        </div>
     </div>
   </div>
</section>
