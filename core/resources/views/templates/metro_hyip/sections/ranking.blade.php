@if ($general->user_ranking)
    @php
        $userRanking = getContent('ranking.content', true);
        $userRankings = App\Models\UserRanking::active()->get();
    @endphp

    <section class="referral-level-section border-top-1 pt-120 pb-60">
        <div class="container">
            <div class="row justify-content-center py-3">
                <div class="section-heading style-two">
                    <span class="section-heading__subtitle">{{ __(@$userRanking->data_values->sub_heading) }}
                    </span>
                    <h1 class="section-heading__title"> {{ __(@$userRanking->data_values->heading) }} </h1>
                    <p>{{ __($userRanking->data_values->content) }}</p>
                </div>
            </div>
            <div class="table--responsive">
                <div class="referral__level__area">
                    @php
                        $firstPercent = 20;
                        $lastPercent = 100;
                        $perItem = ($lastPercent - $firstPercent) / ($userRankings->count() - 1);
                    @endphp

                    @foreach ($userRankings as $rank)
                        <div class="referral__level__item">
                            <div class="referral__level__item__inner">
                                <div class="referral__left">
                                    <div class="referral__level__thumb">
                                        <img src="{{ getImage(getFilePath('userRanking') . '/' . $rank->icon, getFileSize('userRanking')) }}"
                                            alt="referral">
                                    </div>
                                    <div class="referral__level__name">
                                        {{ __($rank->name) }}
                                    </div>
                                </div>
                                <div class="referral__right">
                                    <div class="referral__level__content custom-width"
                                        data-custom_width="{{ $perItem * $loop->index + $firstPercent }}">
                                        <div class="referral__level__content__content">
                                            <span><i class="las la-coins"></i> @lang('Bonus'):
                                                {{ $general->cur_sym }}{{ showAmount($rank->bonus) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="referral__tooltip">
                                <ul>
                                    <li class="d-flex justify-content-between">
                                        @lang('Level')
                                        <span>{{ __($rank->level) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('Minimum Invest')
                                        <span>{{ $general->cur_sym }}{{ showAmount($rank->minimum_invest) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('Team Invest')
                                        <span>{{ $general->cur_sym }}{{ showAmount($rank->min_referral_invest) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        @lang('No. of Direct Referral')
                                        <span>{{ $rank->min_referral }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

@if ($general->user_ranking)
    @push('script')
        <script>
            (function($) {
                "use strict";
                $('.custom-width').each(function(index, value) {
                    $(value).css("max-width", `${$(value).data('custom_width')}%`);
                });
            })(jQuery);
        </script>
    @endpush
@endif

@push('style')
    <style>
        /* Ranking section */
        .table--responsive {
            max-width: 100%;
            overflow-y: hidden;
            overflow-x: auto;
        }

        .referral__level__item__inner {
            display: flex;
        }

        @media (max-width:991px) {
            .referral__level__item__inner {
                align-items: flex-end;
            }
        }

        .referral__left {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            width: 240px;
        }

        @media (max-width: 991px) {
            .referral__left {
                width: 180px;
            }
        }

        @media (max-width: 767px) {
            .referral__left {
                width: 150px;
            }
        }

        .referral__right {
            width: calc(100% - 240px);
            padding-left: 15px;
        }

        @media (max-width: 991px) {
            .referral__right {
                width: calc(100% - 180px);
            }
        }

        @media (max-width: 767px) {
            .referral__right {
                width: calc(100% - 150px);
            }
        }

        .referral__level__item__inner .referral__level__thumb {
            width: 40px;
            height: 40px;
            align-self: center;
        }

        .referral__level__item__inner .referral__level__thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .referral__level__item__inner .referral__level__name,
        .referral__level__item__inner .referral__level__profit,
        .referral__level__item__inner .referral__level__content {
            padding: 10px 20px;
            background: #343a40;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            color: #fff;
            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s;
            transition: all ease 0.3s;
            height: 54px;
            line-height: 38px;
        }

        @media (max-width: 991px) {

            .referral__level__item__inner .referral__level__name,
            .referral__level__item__inner .referral__level__profit,
            .referral__level__item__inner .referral__level__content {
                padding: 10px;
            }
        }

        .referral__level__item__inner .referral__level__name:not(:last-child),
        .referral__level__item__inner .referral__level__profit:not(:last-child),
        .referral__level__item__inner .referral__level__content:not(:last-child) {
            margin-right: 15px;
        }

        .referral__level__item__inner .referral__level__profit {
            width: 100px;
        }

        .referral__level__item__inner .referral__level__content.custom-width {
            transition: all ease 0.3s;
        }

        .referral__level__item__inner .referral__level__name {
            width: 200px;
            font-size: 20px;
        }

        @media (max-width: 991px) {
            .referral__level__item__inner .referral__level__name {
                width: 140px;
                font-size: 16px;
            }
        }

        .referral__level__item:hover .referral__level__item__inner .referral__level__content.custom-width {
            max-width: 1000% !important;
            transition: all ease 0.3s;
        }

        .referral__level__item__inner .referral__level__content__content {
            display: none;
        }

        .referral__level__item__inner .hover__none {
            display: flex;
        }

        .referral__level__item {
            position: relative;
        }

        .referral__level__item:not(:last-child) {
            margin-bottom: 15px;
        }

        .referral__level__item:hover .referral__level__name,
        .referral__level__item:hover .referral__level__profit,
        .referral__level__item:hover .referral__level__content {
            background: var(--base-gradient);
            color: #fff;
        }

        .referral__level__item:hover .referral__level__content {
            flex-grow: 1;
        }

        .referral__level__item:hover .hover__none {
            display: none;
        }

        .referral__level__item:hover .referral__level__content__content {
            display: flex;
        }

        .referral__level__item:hover .referral__tooltip {
            opacity: 1;
            visibility: visible;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        .referral__level__item:nth-last-of-type(3) .referral__tooltip {
            top: 100%;
            bottom: unset;
        }

        .referral__level__item:nth-last-of-type(2) .referral__tooltip,
        .referral__level__item:nth-last-of-type(1) .referral__tooltip {
            bottom: 100%;
            top: unset;
            margin-bottom: 5px;
        }

        @media (max-width:991px) {

            .referral__level__item:nth-last-of-type(2) .referral__tooltip,
            .referral__level__item:nth-last-of-type(1) .referral__tooltip {
                bottom: 65%;
            }
        }

        .referral__level__item:nth-last-of-type(3) .referral__tooltip::before {
            bottom: 100%;
            top: unset;
        }

        .referral__level__item:nth-last-of-type(2) .referral__tooltip::before,
        .referral__level__item:nth-last-of-type(1) .referral__tooltip::before {
            top: 100%;
            bottom: unset;
            clip-path: polygon(50% 100%, 0 0, 100% 0);
        }

        .referral__tooltip {
            position: absolute;
            top: 100%;
            left: 46%;
            -webkit-transform: translateX(-50%) translateY(15px);
            -ms-transform: translateX(-50%) translateY(15px);
            transform: translateX(-50%) translateY(15px);
            width: 100%;
            max-width: 350px;
            background: #fff;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            line-height: 1.6;
            box-shadow: 0 0 5px rgba(4, 38, 86, 0.2);
            visibility: hidden;
            opacity: 0;
            -webkit-transition: all ease 0.3s;
            -moz-transition: all ease 0.3s;
            transition: all ease 0.3s;
            z-index: 9;
            border: 1px solid #dddddd3d;
            background-color: #343a40;
            border-radius: 3px;
            margin-top: 5px;
        }

        .referral__tooltip::before {
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            background: inherit;
            width: 15px;
            height: 10px;
            content: "";
            position: absolute;
            bottom: 99%;
            left: 50%;
            border: 6px solid #dddddd3d;
            margin-left: -10px;
        }

        @media screen and (max-width: 424px) {
            .referral__tooltip {
                max-width: 280px;
                padding: 30px 15px;
            }
        }

        @media (max-width: 991px) {
            .referral__level__area {
                min-width: 920px;
                padding-bottom: 30px;
            }
        }
    </style>
@endpush
