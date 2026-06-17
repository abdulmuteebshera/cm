@php
  $affiliate = getContent('affiliate.content',true);
  $affiliateElement = getContent('affiliate.element',null,false,true);
@endphp

<section class="program-section pt-120">
  <div class="program-section__shape">
      <img src="{{ asset($activeTemplateTrue . 'images/shapes/program.png') }}" alt="">
  </div>
  <div class="container">
      <div class="row align-items-center gy-4">
          <div class="col-lg-5 col-md-6 ">
              <div class="section-heading">
                  <span class="section-heading__subtitle"> {{ __(@$affiliate->data_values->sub_heading) }} </span>
                  <h2 class="section-heading__title">{{ __(@$affiliate->data_values->heading) }} </h2>
             </div>
             @foreach ($affiliateElement as $item)
             <div class="program-item">
                <span class="program-item__icon">
                 @php echo @$item->data_values->icon @endphp
                </span>
                <div class="program-item__content">
                  <h5 class="program-item__title"> {{ __(@$item->data_values->title) }} </h5>
                  <p class="program-item__desc"> {{ __(@$item->data_values->content) }} </p>
                </div>
             </div>
             @endforeach
          </div>
          <div class="col-lg-7 col-md-6 ps-lg-5">
            <div class="program-thumb">
              <img src="{{ getImage('assets/images/frontend/affiliate/'.@$affiliate->data_values->image,'710x620') }}" alt="image">
            </div>
          </div> 
      </div>
  </div>
</section>
