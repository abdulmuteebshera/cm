@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Page')</th>
                            <th>@lang('Views')</th>
                            <th>@lang('Unique visitors')</th>
                            <th>@lang('Avg time')</th>
                            <th>@lang('Avg scroll')</th>
                            <th>@lang('Last visit')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $row)
                            <tr>
                                <td>{{ $row->path }}</td>
                                <td>{{ $row->views }}</td>
                                <td>{{ $row->visitors }}</td>
                                <td>{{ \App\Models\SeoVisitorSession::formatSeconds((int) $row->avg_time) }}</td>
                                <td>{{ round($row->avg_scroll) }}%</td>
                                <td>{{ $row->last_visit ? showDateTime($row->last_visit) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pages->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($pages) }}</div>
        @endif
    </div>
@endsection
