<div class="dashboard-header">

    <div class="dashboard-header__inner">
        <div class="dashboard-header__left">
            <h4 class="dashboard-header__left-title"> {{ __($pageTitle) }} </h4>
        </div>
        <div class="dashboard-header__right">
            <div class="user-info">
                <div class="user-info__button">
                    <div class="user-info__info">
                        @if (auth()->user()->userRanking && $general->user_ranking)
                            <span class="rang-user" data-bs-toggle="tooltip" data-bs-title="@lang('Your current rank is ' . auth()->user()->userRanking->name)">
                                <img src="{{ getImage(getFilePath('userRanking') . '/' . auth()->user()->userRanking->icon, getFileSize('userRanking')) }}"
                                    alt="">
                                <img class="rang-user-icon"
                                    src="http://localhost:81/u-hyiplab3.5/assets/images/user_rankings/6450c99123b231683016081.png"
                                    alt="">
                            </span>
                        @endif

                        <span class="user-info__thumb">
                            {{ getInitials(auth()->user()->fullname) }}
                        </span>

                        <ul class="user-info-dropdown">
                            <li class="user-info-dropdown__item"><a class="user-info-dropdown__link"
                                    href="{{ route('user.profile.setting') }}">@lang('Profile Setting')</a></li>
                            <li class="user-info-dropdown__item"><a class="user-info-dropdown__link"
                                    href="{{ route('user.change.password') }}">@lang('Change Password')</a></li>
                            @if ($general->user_ranking)
                                <li class="user-info-dropdown__item"><a class="user-info-dropdown__link"
                                        href="{{ route('user.invest.ranking') }}">@lang('Ranking')</a></li>
                            @endif
                            <li class="user-info-dropdown__item"><a class="user-info-dropdown__link"
                                    href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                        </ul>
                        <div class="user-info__content">
                            <div class="d-flex justify-content-around">
                                <span class="user-info__name">{{ __(auth()->user()->fullname) }} </span>
                                <span><i class="las la-angle-down"></i></span>
                            </div>
                            <span class="user-info__link"> {{ auth()->user()->email }} </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-body__bar d-xl-none d-block mt-2 text-end">
    <span class="dashboard-body__bar-icon"><i class="las la-bars"></i></span>
</div>
