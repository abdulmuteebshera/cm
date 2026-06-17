 @php
     $workContent = getContent('how_work.content', true);
     $workElement = getContent('how_work.element',false, orderById: true);
 @endphp

 <section class="py-120 profit-section">
    <div class="profit-section__inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2 class="section-heading__title"> {{ __(@$workContent->data_values->heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$workContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-7">
                    <div class="profit-card-wrapper">
                        <span class="profit-card-wrapper__border"></span>
                        @foreach ($workElement as $work)
                        <div class="profit-card">
                            <div class="profit-card__icon">
                                @php echo $work->data_values->icon; @endphp
                            </div>
                            <div class="profit-card__content">
                                <h4 class="profit-card__title">{{ $loop->iteration }}. {{ __(@$work->data_values->title) }}</h4>
                                <p class="profit-card__desc">{{ __(@$work->data_values->sub_title) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="profit-thumb">
                        <img src="{{ getImage('assets/images/frontend/how_work/' . @$workContent->data_values->image,'800x700') }}" alt="@lang('How it works')" class="profit-thumb__img">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
