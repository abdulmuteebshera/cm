@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $top    = $entries->take(3);
    $rest   = $entries->slice(3);
    // key = rank index (0 = highest amount). `order` only controls the visual
    // placement so #1 sits raised in the centre, #2 left, #3 right.
    $podium = [
        0 => ['order' => 2, 'medal' => '🥇', 'cls' => 'is-first'],
        1 => ['order' => 1, 'medal' => '🥈', 'cls' => 'is-second'],
        2 => ['order' => 3, 'medal' => '🥉', 'cls' => 'is-third'],
    ];
@endphp
<div class="dashboard-inner quant-dashboard">

    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Investor Leaderboard')</h4>
            <p class="text-muted mb-0">@lang('Our top 10 investors ranked by total capital invested')</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    @if($entries->count())
        {{-- Podium (top 3) --}}
        @if($top->count())
            <div class="quant-panel lb-podium-panel mb-4">
                <div class="quant-panel__body">
                    <div class="lb-podium">
                        @foreach($top as $i => $entry)
                            @php $p = $podium[$i] ?? ['order' => $i + 1, 'medal' => '', 'cls' => '']; @endphp
                            <div class="lb-podium__item {{ $p['cls'] }}" style="order: {{ $p['order'] }};">
                                <div class="lb-podium__medal">{{ $p['medal'] }}</div>
                                <div class="lb-podium__avatar">{{ mb_strtoupper(mb_substr($entry->name, 0, 1)) }}</div>
                                <div class="lb-podium__name">{{ $entry->masked_name }}</div>
                                <div class="lb-podium__amount">{{ showAmount($entry->amount) }} <small>{{ $general->cur_text }}</small></div>
                                <div class="lb-podium__rank">#{{ $loop->iteration }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Rewards & recognition --}}
        <div class="quant-panel lb-rewards mb-4">
            <div class="quant-panel__head quant-panel__head--aligned">
                <div>
                    <h5 class="quant-panel__title">@lang('Rewards & Recognition')</h5>
                    <p class="quant-panel__desc">@lang('Exclusive honours for our leading investors')</p>
                </div>
            </div>
            <div class="quant-panel__body">
                <div class="lb-rewards__grid">
                    <div class="lb-reward lb-reward--crown">
                        <span class="lb-reward__icon"><i class="las la-award"></i></span>
                        <div>
                            <h6 class="lb-reward__title">@lang('Investor of the Year')</h6>
                            <p class="lb-reward__text">@lang('The #1 investor is honoured with the Crownmaire Capital Investor of the Year award.')</p>
                        </div>
                    </div>
                    <div class="lb-reward">
                        <span class="lb-reward__icon"><i class="las la-shield-alt"></i></span>
                        <div>
                            <h6 class="lb-reward__title">@lang('Shield & Bonus')</h6>
                            <p class="lb-reward__text">@lang('The top 3 investors are awarded an exclusive shield and a bonus from Crownmaire Capital.')</p>
                        </div>
                    </div>
                    <div class="lb-reward">
                        <span class="lb-reward__icon"><i class="las la-star"></i></span>
                        <div>
                            <h6 class="lb-reward__title">@lang('Hall of Fame')</h6>
                            <p class="lb-reward__text">@lang('The top 3 investors are featured in the Crownmaire Capital Hall of Fame.')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full ranking --}}
        <div class="quant-panel">
            <div class="quant-panel__head quant-panel__head--aligned">
                <div>
                    <h5 class="quant-panel__title">@lang('Top 10 Ranking')</h5>
                    <p class="quant-panel__desc">@lang('Names are partially hidden to protect investor privacy')</p>
                </div>
            </div>
            <div class="quant-panel__body p-0">
                <div class="table-responsive">
                    <table class="lb-table">
                        <thead>
                            <tr>
                                <th class="lb-table__rank">@lang('Rank')</th>
                                <th>@lang('Investor')</th>
                                <th class="text-end">@lang('Total Invested')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                                <tr class="{{ $loop->iteration <= 3 ? 'is-top' : '' }}">
                                    <td class="lb-table__rank">
                                        @if($loop->iteration == 1)
                                            <span class="lb-rank-badge lb-rank-badge--gold">1</span>
                                        @elseif($loop->iteration == 2)
                                            <span class="lb-rank-badge lb-rank-badge--silver">2</span>
                                        @elseif($loop->iteration == 3)
                                            <span class="lb-rank-badge lb-rank-badge--bronze">3</span>
                                        @else
                                            <span class="lb-rank-badge">{{ $loop->iteration }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="lb-investor">
                                            <span class="lb-investor__avatar">{{ mb_strtoupper(mb_substr($entry->name, 0, 1)) }}</span>
                                            <span class="lb-investor__name">{{ $entry->masked_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end lb-table__amount">{{ showAmount($entry->amount) }} <small>{{ $general->cur_text }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="quant-panel">
            <div class="quant-panel__body">
                <div class="lb-empty">
                    <i class="las la-trophy"></i>
                    <p class="mb-0">@lang('The leaderboard is being prepared. Please check back soon.')</p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('style')
<style>
    .lb-podium {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 18px;
        flex-wrap: wrap;
        padding: 10px 0;
    }
    .lb-podium__item {
        flex: 1 1 0;
        max-width: 220px;
        min-width: 140px;
        text-align: center;
        border-radius: 16px;
        padding: 18px 14px;
        background: var(--quant-primary-light, #e8f4fa);
        border: 1px solid rgba(25, 137, 190, 0.18);
        position: relative;
    }
    .lb-podium__item.is-first {
        background: linear-gradient(160deg, #fff7e0, #fde6b8);
        border-color: rgba(212, 160, 23, 0.45);
        transform: translateY(-14px);
        box-shadow: 0 16px 36px rgba(212, 160, 23, 0.22);
    }
    .lb-podium__item.is-second {
        background: linear-gradient(160deg, #f4f6f8, #e4e8ee);
        border-color: rgba(148, 163, 184, 0.5);
    }
    .lb-podium__item.is-third {
        background: linear-gradient(160deg, #fdeee2, #f6dcc4);
        border-color: rgba(205, 127, 50, 0.45);
    }
    .lb-podium__medal {
        font-size: 1.9rem;
        line-height: 1;
        margin-bottom: 8px;
    }
    .lb-podium__avatar {
        width: 54px;
        height: 54px;
        margin: 0 auto 10px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        background: var(--quant-primary, #1989BE);
    }
    .lb-podium__item.is-first .lb-podium__avatar { background: #d4a017; }
    .lb-podium__item.is-second .lb-podium__avatar { background: #94a3b8; }
    .lb-podium__item.is-third .lb-podium__avatar { background: #cd7f32; }
    .lb-podium__name {
        font-weight: 700;
        color: var(--quant-text, #0f172a);
        letter-spacing: 0.5px;
    }
    .lb-podium__amount {
        margin-top: 4px;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--quant-primary, #1989BE);
    }
    .lb-podium__amount small { font-weight: 500; font-size: 0.7rem; }
    .lb-podium__rank {
        margin-top: 6px;
        font-size: 0.75rem;
        color: var(--quant-text-muted, #64748b);
        font-weight: 600;
    }
    .lb-table {
        width: 100%;
        border-collapse: collapse;
    }
    .lb-table thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--quant-text-muted, #94a3b8);
        font-weight: 600;
        padding: 14px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }
    .lb-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        vertical-align: middle;
    }
    .lb-table tbody tr:last-child td { border-bottom: none; }
    .lb-table tbody tr.is-top { background: rgba(25, 137, 190, 0.04); }
    .lb-table__rank { width: 80px; }
    .lb-rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        background: rgba(148, 163, 184, 0.16);
        color: var(--quant-text, #334155);
    }
    .lb-rank-badge--gold { background: rgba(212, 160, 23, 0.18); color: #b8860b; }
    .lb-rank-badge--silver { background: rgba(148, 163, 184, 0.22); color: #64748b; }
    .lb-rank-badge--bronze { background: rgba(205, 127, 50, 0.18); color: #cd7f32; }
    .lb-investor {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .lb-investor__avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: #fff;
        background: var(--quant-primary, #1989BE);
        flex-shrink: 0;
    }
    .lb-investor__name {
        font-weight: 600;
        color: var(--quant-text, #0f172a);
        letter-spacing: 0.5px;
    }
    .lb-table__amount {
        font-weight: 700;
        color: var(--quant-text, #0f172a);
        white-space: nowrap;
    }
    .lb-table__amount small { font-weight: 500; color: var(--quant-text-muted, #94a3b8); }
    .lb-rewards__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .lb-reward {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        border-radius: 14px;
        background: rgba(25, 137, 190, 0.05);
        border: 1px solid rgba(25, 137, 190, 0.14);
    }
    .lb-reward--crown {
        background: linear-gradient(160deg, #fff7e0, #fdeccb);
        border-color: rgba(212, 160, 23, 0.4);
    }
    .lb-reward__icon {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #fff;
        background: var(--quant-primary, #1989BE);
    }
    .lb-reward--crown .lb-reward__icon {
        background: #d4a017;
    }
    .lb-reward__title {
        margin: 0 0 4px;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--quant-text, #0f172a);
    }
    .lb-reward__text {
        margin: 0;
        font-size: 0.82rem;
        line-height: 1.55;
        color: var(--quant-text-muted, #64748b);
    }
    @media (max-width: 767px) {
        .lb-rewards__grid {
            grid-template-columns: 1fr;
        }
    }
    .lb-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--quant-text-muted, #64748b);
    }
    .lb-empty i {
        font-size: 1.75rem;
        color: var(--quant-primary, #1989BE);
    }
    @media (max-width: 575px) {
        .lb-podium__item.is-first { transform: none; }
        .lb-podium__item { min-width: 100px; padding: 14px 8px; }
    }
</style>
@endpush
