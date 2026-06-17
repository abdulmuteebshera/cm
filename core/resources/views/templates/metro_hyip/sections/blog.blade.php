@php
$blogElement = getContent('blog.element',false,3);
$blogContent = getContent('blog.content',true);
@endphp

<section class="blog pt-120">
  <div class="container">
      <div class="row">
          <div class="col-lg-12">
              <div class="section-heading style-two">
                  <span class="section-heading__subtitle"> {{ __(@$blogContent->data_values->sub_heading) }} </span>
                  <h2 class="section-heading__title"> {{ __(@$blogContent->data_values->heading) }} </h2>
              </div>
          </div>
      </div>
      <div class="row gy-4 justify-content-center">
        @foreach ($blogElement as $blog)
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="blog-item">
                <div class="blog-item__thumb">
                    <a href="{{route('blog.details',[slug($blog->data_values->title),$blog->id])}}" class="blog-item__thumb-link">
                        <img src="{{getImage('assets/images/frontend/blog/thumb_'.@$blog->data_values->image,'415x305')}}" alt="image">
                    </a>
                </div>
                <div class="blog-item__content">
                    <ul class="content-list">
                        <li class="content-list__item">  {{ showDateTime($blog->created_at, 'F j, Y') }} </li>
                        <li class="content-list__item "> {{ diffForHumans($blog->created_at) }} </li>
                    </ul>
                    <h3 class="blog-item__title"><a href="{{route('blog.details',[slug($blog->data_values->title),$blog->id])}}" class="blog-item__title-link"> {{__(@$blog->data_values->title)}} </a></h3>
                    <a href="{{route('blog.details',[slug($blog->data_values->title),$blog->id])}}" class="btn--simple"> @lang('Continue Reading') <span class="btn--simple__icon"><i class="fas fa-angle-right"></i></span></a>
                </div>
            </div>
        </div>
        @endforeach 
      </div>
  </div>
</section>
