@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')

    <div class="row gy-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">@lang('Primary domain')</small>
                    <div class="fw-bold">{{ $stats['canonical'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">@lang('Alias (301)')</small>
                    <div class="fw-bold">{{ $stats['alias'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">@lang('Tracked sessions')</small>
                    <div class="fw-bold">{{ $stats['sessions'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">@lang('Page views stored')</small>
                    <div class="fw-bold">{{ $stats['views'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">@lang('Implemented SEO foundation')</h5>
            <p class="text-muted">@lang('These are on-site controls you now own. Search rankings still depend on Google and Bing discovering the site, which requires the Search Console steps below.')</p>
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Item')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checklist as $row)
                            <tr>
                                <td>{{ $row['item'] }}</td>
                                <td class="white-space-wrap">{{ $row['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">@lang('Submit the site to search engines')</h5>
            <ol class="mb-3">
                <li>@lang('Open') <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a> @lang('and add') <code>https://crownmairecapital.com</code> @lang('as a URL-prefix property.')</li>
                <li>@lang('Verify ownership, then submit') <a href="{{ $stats['sitemap'] }}" target="_blank" rel="noopener">{{ $stats['sitemap'] }}</a>.</li>
                <li>@lang('Repeat in') <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a>.</li>
                <li>@lang('Request indexing for Home, About, Strategies, and Contact after the first crawl.')</li>
            </ol>
            <p class="mb-0"><a href="{{ $stats['robots'] }}" target="_blank" rel="noopener">{{ $stats['robots'] }}</a></p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="card-header"><h6 class="mb-0">@lang('Indexable page inventory')</h6></div>
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Route')</th>
                            <th>@lang('Title tag')</th>
                            <th>@lang('Meta description')</th>
                            <th>@lang('URL')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                            <tr>
                                <td>{{ $page['route'] }}</td>
                                <td class="white-space-wrap">{{ $page['title'] }}</td>
                                <td class="white-space-wrap">{{ $page['description'] }}</td>
                                <td><a href="{{ $page['url'] }}" target="_blank" rel="noopener">{{ $page['url'] }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
