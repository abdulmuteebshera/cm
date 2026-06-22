@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard">

    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('My Certificates')</h4>
            <p class="text-muted mb-0">@lang('Your official Crownmaire Capital membership certificates')</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="cert-grid">
        @foreach($certificates as $certificate)
            @php $isWelcome = $certificate->isWelcome(); @endphp
            <div class="quant-panel cert-card {{ $isWelcome ? 'cert-card--welcome' : '' }}">
                <div class="quant-panel__body">
                    <div class="cert-card__ribbon">
                        <i class="las {{ $isWelcome ? 'la-medal' : 'la-award' }}"></i>
                    </div>
                    <span class="cert-card__type">{{ $isWelcome ? __('Welcome') : __('Strategy Membership') }}</span>
                    <h5 class="cert-card__title">{{ __($certificate->title()) }}</h5>
                    @if(!$isWelcome && $certificate->strategy_name)
                        <p class="cert-card__strategy"><i class="las la-layer-group"></i> {{ __($certificate->strategy_name) }}</p>
                    @else
                        <p class="cert-card__strategy"><i class="las la-star"></i> @lang('Founding Membership')</p>
                    @endif
                    <div class="cert-card__meta">
                        <span><i class="las la-hashtag"></i> {{ $certificate->certificate_number }}</span>
                        <span><i class="las la-calendar"></i> {{ showDateTime($certificate->issued_at, 'd M Y') }}</span>
                    </div>
                    <div class="cert-card__actions">
                        <a href="{{ route('certificate.show', $certificate->uid) }}" target="_blank" class="cert-btn cert-btn--primary">
                            <i class="las la-eye"></i> @lang('View & Download')
                        </a>
                        <button type="button" class="cert-btn cert-btn--ghost cert-share" data-url="{{ route('certificate.show', $certificate->uid) }}">
                            <i class="las la-share-alt"></i> <span class="cert-share__text">@lang('Share')</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

@push('style')
<style>
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 20px;
    }
    .cert-card { position: relative; overflow: hidden; }
    .cert-card .quant-panel__body { position: relative; }
    .cert-card__ribbon {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 74px;
        height: 74px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #fff;
        background: linear-gradient(135deg, #1989BE, #0f3d57);
        opacity: 0.16;
    }
    .cert-card--welcome .cert-card__ribbon {
        background: linear-gradient(135deg, #d4a017, #b8860b);
        opacity: 0.2;
    }
    .cert-card__type {
        display: inline-block;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        color: #1989BE;
        background: rgba(25, 137, 190, 0.1);
        margin-bottom: 12px;
    }
    .cert-card--welcome .cert-card__type {
        color: #b8860b;
        background: rgba(212, 160, 23, 0.12);
    }
    .cert-card__title {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--quant-text, #0f172a);
        margin-bottom: 6px;
    }
    .cert-card__strategy {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        color: var(--quant-primary, #1989BE);
        font-weight: 600;
        margin-bottom: 14px;
    }
    .cert-card--welcome .cert-card__strategy { color: #b8860b; }
    .cert-card__meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 0.78rem;
        color: var(--quant-text-muted, #64748b);
        margin-bottom: 16px;
    }
    .cert-card__meta i { width: 16px; }
    .cert-card__actions { display: flex; gap: 10px; }
    .cert-btn {
        flex: 1 1 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .cert-btn--primary { background: var(--quant-primary, #1989BE); color: #fff; }
    .cert-btn--primary:hover { background: #146a96; color: #fff; }
    .cert-btn--ghost {
        background: transparent;
        color: var(--quant-text, #334155);
        border-color: rgba(148, 163, 184, 0.4);
    }
    .cert-btn--ghost:hover { background: rgba(148, 163, 184, 0.12); }
</style>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        $('.cert-share').on('click', function(){
            var url = $(this).data('url');
            var btn = $(this);
            if (navigator.share) {
                navigator.share({ title: 'Crownmaire Capital Certificate', url: url });
                return;
            }
            navigator.clipboard.writeText(url).then(function(){
                var label = btn.find('.cert-share__text');
                var old = label.text();
                label.text('Link Copied');
                setTimeout(function(){ label.text(old); }, 2000);
            });
        });
    })(jQuery);
</script>
@endpush
