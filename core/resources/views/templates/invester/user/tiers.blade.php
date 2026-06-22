@extends($activeTemplate.'layouts.master')
@section('content')
@php $cur = $general->cur_text; @endphp
<div class="dashboard-inner quant-dashboard">

    {{-- Header --}}
    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Membership Tiers')</h4>
            <p class="text-muted mb-0">@lang('Your tier is based on the amount you have invested across strategies. Invest more to unlock higher tiers and benefits.')</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('plan') }}" class="quant-panel__link">@lang('Invest More') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Current standing --}}
    <div class="row g-4 align-items-stretch mb-4">
        <div class="col-lg-5 d-flex">
            <div class="quant-panel tier-hero w-100" @if($standing->current) style="--tier-color: {{ $standing->current['color'] }}" @endif>
                <div class="quant-panel__body">
                    <span class="tier-hero__label">@lang('Your Current Tier')</span>
                    @if($standing->current)
                        <div class="tier-hero__badge">
                            <span class="tier-hero__emoji">{{ $standing->current['emoji'] }}</span>
                            <div>
                                <span class="tier-hero__name">{{ __($standing->current['name']) }}</span>
                                <span class="tier-hero__min">@lang('Qualifies at') {{ showAmount($standing->current['min'], 0) }} {{ $cur }}+</span>
                            </div>
                        </div>
                    @else
                        <div class="tier-hero__badge">
                            <span class="tier-hero__emoji">🔰</span>
                            <div>
                                <span class="tier-hero__name">@lang('Not Ranked Yet')</span>
                                <span class="tier-hero__min">@lang('Invest to unlock your first tier')</span>
                            </div>
                        </div>
                    @endif
                    <div class="tier-hero__invested">
                        <span>@lang('Total Invested')</span>
                        <strong>{{ showAmount($investedAmount, 0) }} {{ $cur }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-flex">
            <div class="quant-panel w-100">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Progress')</h5>
                        <p class="quant-panel__desc">@lang('Your journey to the next tier')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    @if($standing->is_top)
                        <div class="tier-progress__top">
                            <span class="tier-progress__top-emoji">{{ $standing->current['emoji'] }}</span>
                            <div>
                                <strong>@lang('You have reached the highest tier!')</strong>
                                <p class="mb-0 text-muted">@lang('You are enjoying every benefit Crownmaire Capital offers.')</p>
                            </div>
                        </div>
                    @elseif($standing->next)
                        <div class="tier-progress__heads">
                            <span>
                                @if($standing->current)
                                    {{ $standing->current['emoji'] }} {{ __($standing->current['name']) }}
                                @else
                                    🔰 @lang('Start')
                                @endif
                            </span>
                            <span class="text-end">{{ $standing->next['emoji'] }} {{ __($standing->next['name']) }}</span>
                        </div>
                        <div class="tier-progress__bar">
                            <span style="width: {{ $standing->progress }}%; @if($standing->current) background: {{ $standing->current['color'] }} @endif"></span>
                        </div>
                        <div class="tier-progress__foot">
                            <span class="tier-progress__pct">{{ showAmount($standing->progress, 0) }}% @lang('there')</span>
                            <span class="tier-progress__needed">
                                <strong>{{ showAmount($standing->needed, 0) }} {{ $cur }}</strong> @lang('more to reach') {{ $standing->next['emoji'] }} {{ __($standing->next['name']) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tier ladder --}}
    <div class="quant-panel mb-4">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('All Tiers')</h5>
                <p class="quant-panel__desc">@lang('Investment required to reach each level')</p>
            </div>
        </div>
        <div class="quant-panel__body">
            <div class="tier-ladder">
                @foreach($tiers as $i => $tier)
                    @php
                        $achieved = $standing->current_index >= $i;
                        $isCurrent = $standing->current_index === $i;
                    @endphp
                    <div class="tier-ladder__item {{ $achieved ? 'is-achieved' : '' }} {{ $isCurrent ? 'is-current' : '' }}" style="--tier-color: {{ $tier['color'] }}">
                        <span class="tier-ladder__emoji">{{ $tier['emoji'] }}</span>
                        <span class="tier-ladder__name">{{ __($tier['name']) }}</span>
                        <span class="tier-ladder__min">{{ showAmount($tier['min'], 0) }}+ {{ $cur }}</span>
                        @if($isCurrent)
                            <span class="tier-ladder__tag">@lang('You are here')</span>
                        @elseif($achieved)
                            <span class="tier-ladder__check"><i class="las la-check-circle"></i> @lang('Unlocked')</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Comparison chart --}}
    <div class="quant-panel">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Tier Benefits Comparison')</h5>
                <p class="quant-panel__desc">@lang('Facilities available at each membership tier')</p>
            </div>
        </div>
        <div class="quant-panel__body">
            <div class="table-responsive">
                <table class="table tier-compare__table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="tier-compare__facility">@lang('Facility')</th>
                            @foreach($tiers as $i => $tier)
                                <th class="text-center {{ $standing->current_index === $i ? 'is-current' : '' }}" style="--tier-color: {{ $tier['color'] }}">
                                    <span class="tier-compare__emoji">{{ $tier['emoji'] }}</span>
                                    <span class="tier-compare__name">{{ __($tier['name']) }}</span>
                                    <small class="tier-compare__min">{{ showAmount($tier['min'], 0) }}+ {{ $cur }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilities as $facility)
                            <tr>
                                <th class="tier-compare__facility">{{ __($facility['label']) }}</th>
                                @foreach($tiers as $i => $tier)
                                    @php $val = $facility['values'][$i] ?? false; @endphp
                                    <td class="text-center {{ $standing->current_index === $i ? 'is-current' : '' }}">
                                        @if(is_bool($val))
                                            @if($val)
                                                <i class="las la-check-circle tier-yes"></i>
                                            @else
                                                <span class="tier-no">&mdash;</span>
                                            @endif
                                        @else
                                            <span class="tier-val">{{ __($val) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3 mb-0"><i class="las la-info-circle"></i> @lang('Benefits shown are indicative and may be updated.')</p>
        </div>
    </div>

</div>
@endsection

@push('style')
<style>
    .tier-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-left: 4px solid var(--tier-color, var(--quant-primary, #1989BE));
    }
    .tier-hero__label {
        display: block;
        font-size: 0.6875rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 14px;
    }
    .tier-hero__badge {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }
    .tier-hero__emoji {
        font-size: 2.6rem;
        line-height: 1;
    }
    .tier-hero__name {
        display: block;
        font-family: "Maven Pro", sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--tier-color, var(--quant-text, #1a1a1a));
        line-height: 1.1;
    }
    .tier-hero__min {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    .tier-hero__invested {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: 1px solid var(--quant-border, #e5e5e5);
    }
    .tier-hero__invested span {
        font-size: 0.8rem;
        color: #64748b;
    }
    .tier-hero__invested strong {
        font-family: "Maven Pro", sans-serif;
        font-size: 1.1rem;
        color: var(--quant-text, #1a1a1a);
    }
    .tier-progress__heads {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--quant-text, #1a1a1a);
        margin-bottom: 10px;
    }
    .tier-progress__bar {
        height: 12px;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
    }
    .tier-progress__bar > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: var(--quant-primary, #1989BE);
        transition: width .5s ease;
    }
    .tier-progress__foot {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }
    .tier-progress__pct {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
    }
    .tier-progress__needed {
        font-size: 0.85rem;
        color: #64748b;
    }
    .tier-progress__needed strong {
        color: var(--quant-primary, #1989BE);
    }
    .tier-progress__top {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .tier-progress__top-emoji {
        font-size: 2.4rem;
    }

    .tier-ladder {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
    }
    .tier-ladder__item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 4px;
        padding: 16px 10px;
        border: 1px solid var(--quant-border, #e5e5e5);
        border-radius: 14px;
        background: #fff;
        opacity: 0.65;
        transition: all .2s ease;
    }
    .tier-ladder__item.is-achieved {
        opacity: 1;
        border-color: var(--tier-color, #cd7f32);
    }
    .tier-ladder__item.is-current {
        opacity: 1;
        border-width: 2px;
        border-color: var(--tier-color, #cd7f32);
        box-shadow: 0 8px 22px rgba(0,0,0,0.07);
        transform: translateY(-2px);
    }
    .tier-ladder__emoji { font-size: 1.7rem; line-height: 1; }
    .tier-ladder__name {
        font-family: "Maven Pro", sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--quant-text, #1a1a1a);
    }
    .tier-ladder__min { font-size: 0.7rem; color: #94a3b8; }
    .tier-ladder__tag {
        margin-top: 4px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #fff;
        background: var(--tier-color, #cd7f32);
        padding: 3px 8px;
        border-radius: 999px;
    }
    .tier-ladder__check {
        margin-top: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--quant-profit, #16a34a);
    }

    .tier-compare__table th,
    .tier-compare__table td {
        border-color: #eef2f7;
        vertical-align: middle;
    }
    .tier-compare__table thead th {
        background: #f8fafc;
        padding: 14px 10px;
        white-space: nowrap;
    }
    .tier-compare__facility {
        text-align: left;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
    }
    .tier-compare__emoji { display: block; font-size: 1.3rem; line-height: 1; }
    .tier-compare__name {
        display: block;
        font-family: "Maven Pro", sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--quant-text, #1a1a1a);
    }
    .tier-compare__min { display: block; font-size: 0.65rem; color: #94a3b8; font-weight: 500; }
    .tier-compare__table .is-current {
        background: color-mix(in srgb, var(--tier-color, #1989BE) 8%, #fff);
        border-left: 2px solid var(--tier-color, #1989BE);
        border-right: 2px solid var(--tier-color, #1989BE);
    }
    .tier-compare__table tbody tr:nth-child(odd) td:not(.is-current),
    .tier-compare__table tbody tr:nth-child(odd) .tier-compare__facility {
        background: #fcfdff;
    }
    .tier-yes { color: var(--quant-profit, #16a34a); font-size: 1.15rem; }
    .tier-no { color: #cbd5e1; }
    .tier-val { font-weight: 600; color: var(--quant-text, #1a1a1a); font-size: 0.85rem; }

    @media (max-width: 991px) {
        .tier-ladder { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 575px) {
        .tier-ladder { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush
