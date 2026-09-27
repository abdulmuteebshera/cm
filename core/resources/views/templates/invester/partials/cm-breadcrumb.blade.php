@php
    $crumbs = $seoPage->breadcrumbs ?? \App\Support\Seo\SeoCatalog::resolve(get_defined_vars())->breadcrumbs ?? [];
@endphp
@if(count($crumbs) > 1)
<nav class="cm-breadcrumb" aria-label="@lang('Breadcrumb')">
    <div class="cm-container">
        <ol>
            @foreach($crumbs as $i => $crumb)
                <li>
                    @if($i < count($crumbs) - 1)
                        <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                    @else
                        <span aria-current="page">{{ $crumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
<style>
.cm-breadcrumb { padding: 14px 0 0; }
.cm-breadcrumb ol { display:flex; flex-wrap:wrap; gap:8px; list-style:none; margin:0; padding:0; font-size:13px; color: var(--cm-muted, #6b7c86); }
.cm-breadcrumb li { display:flex; align-items:center; gap:8px; }
.cm-breadcrumb li:not(:last-child)::after { content:"/"; opacity:.5; }
.cm-breadcrumb a { color: inherit; text-decoration:none; }
.cm-breadcrumb a:hover { color: var(--cm-accent, #1bb0ce); }
</style>
@endif
