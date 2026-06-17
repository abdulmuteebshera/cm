@php
    $contactContent = getContent('contact.content', true);
@endphp
<div class="dashboard-header">
    <div class="dashboard-header__inner">
        <div class="dashboard-body__bar d-lg-none d-block">
            <span class="dashboard-body__bar-icon"><i class="las la-bars"></i></span>
        </div>
        <div class="dashboard-header__left">
            <ul class="contact-list">
                <li class="contact-list__item"><a href="{{ route('ticket.open')}}" class="contact-list__link"> <span
                            class="contact-list__link-icon"><i class="fas fa-headset"></i></span>
                            @lang('Support')</a></li>
                <li class="contact-list__item"><a href="mailto:{{ @$contactContent->data_values->support_email }}" class="contact-list__link"> <span
                            class="contact-list__link-icon"><i class="fas fa-envelope"></i></span>
                            {{ @$contactContent->data_values->support_email }}</a></li>
            </ul>
        </div>

        <div class="dashboard-header__right">
            <div class="user-profile">
                <div class="dropdown">
                    <div class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-profile__info">
                            <div class="user-profile__thumb">

                               <span class="user__icon"> <i class="las la-user-circle"></i></span>
                            </div>
                            <span class="user-profile__name">{{ __(auth()->user()->fullname) }}</span>
                        </div>
                    </div>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('user.profile.setting') }}">@lang('My Profile')</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.change.password') }}"> @lang('Change Password') </a></li>
                        <li><a class="dropdown-item" href="{{ route('user.logout') }}"> @lang('Logout')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
