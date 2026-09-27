@php $range = $range ?? request('range', '30'); @endphp
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('admin.seo.analytics.index', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.index') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Overview')</a>
    <a href="{{ route('admin.seo.analytics.visitors', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.visitors') || request()->routeIs('admin.seo.analytics.session') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Visitors')</a>
    <a href="{{ route('admin.seo.analytics.pages', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.pages') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Pages')</a>
    <a href="{{ route('admin.seo.analytics.locations', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.locations') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Locations')</a>
    <a href="{{ route('admin.seo.analytics.devices', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.devices') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Devices')</a>
    <a href="{{ route('admin.seo.analytics.sources', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.sources') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Sources')</a>
    <a href="{{ route('admin.seo.analytics.clicks', ['range' => $range]) }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.clicks') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('Clicks')</a>
    <a href="{{ route('admin.seo.analytics.results') }}" class="btn btn-sm {{ request()->routeIs('admin.seo.analytics.results') ? 'btn--primary' : 'btn-outline--primary' }}">@lang('SEO Results')</a>
    <a href="{{ route('admin.seo') }}" class="btn btn-sm btn-outline--dark">@lang('SEO Manager')</a>
</div>
@if(!request()->routeIs('admin.seo.analytics.results') && !request()->routeIs('admin.seo.analytics.session') && empty($setup))
<div class="d-flex flex-wrap gap-2 mb-4">
    @foreach(['7' => '7 days', '30' => '30 days', '90' => '90 days', '365' => '12 months'] as $key => $label)
        <a href="{{ request()->fullUrlWithQuery(['range' => $key]) }}" class="btn btn-sm {{ ($range ?? '30') == $key ? 'btn--dark' : 'btn-outline--dark' }}">@lang($label)</a>
    @endforeach
</div>
@endif
