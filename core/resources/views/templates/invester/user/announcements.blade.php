@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard">

    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Announcements')</h4>
            <p class="text-muted mb-0">@lang('Latest updates and notices from Crownmaire Capital')</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="announcement-list">
        @forelse($announcements as $announcement)
            <div class="quant-panel announcement-item">
                <div class="quant-panel__body">
                    <div class="announcement-item__head">
                        <span class="announcement-item__icon"><i class="las la-bullhorn"></i></span>
                        <div class="announcement-item__title-wrap">
                            <h5 class="announcement-item__title">{{ __($announcement->title) }}</h5>
                            <span class="announcement-item__date"><i class="las la-clock"></i> {{ showDateTime($announcement->created_at, 'd M Y, h:i A') }}</span>
                        </div>
                    </div>
                    <div class="announcement-item__body">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="quant-panel">
                <div class="quant-panel__body">
                    <div class="announcement-empty">
                        <i class="las la-bullhorn"></i>
                        <p class="mb-0">@lang('No announcements yet. Please check back later.')</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($announcements->hasPages())
        <div class="mt-4">
            {{ paginateLinks($announcements) }}
        </div>
    @endif

</div>
@endsection

@push('style')
<style>
    .announcement-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .announcement-item__head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }
    .announcement-item__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        background: var(--quant-primary-light, #e8f4fa);
        color: var(--quant-primary, #1989BE);
    }
    .announcement-item__title-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }
    .announcement-item__title {
        margin: 0;
        font-family: "Maven Pro", sans-serif;
        font-weight: 700;
        color: var(--quant-text, #1a1a1a);
        font-size: 1.05rem;
        line-height: 1.25;
    }
    .announcement-item__date {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--quant-text-muted, #94a3b8);
    }
    .announcement-item__body {
        color: #4b5563;
        font-size: 0.9rem;
        line-height: 1.7;
        padding-left: 58px;
    }
    .announcement-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--quant-text-muted, #64748b);
    }
    .announcement-empty i {
        font-size: 1.75rem;
        color: var(--quant-primary, #1989BE);
    }
    @media (max-width: 575px) {
        .announcement-item__body {
            padding-left: 0;
        }
    }
</style>
@endpush
