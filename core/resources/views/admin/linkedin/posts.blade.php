@extends('admin.layouts.app')
@section('panel')
    <div class="card b-radius--10 mb-3">
        <div class="card-body">
            <p class="mb-2">@lang('One post every 3 days. The text is written from Crownmaire’s site: quantitative asset management, invitation-only programs for qualified investors from $20,000, New York and Dubai, plus the site keywords as hashtags. Add a picture and approve it before it publishes.')</p>
            <p class="mb-0">@lang('Next open day'): <strong>{{ $nextDate->format('d M Y') }}</strong>
                @if(!$settings->isConnected())
                    — @lang('connect the LinkedIn account before a post can publish.')
                @endif
            </p>
        </div>
    </div>

    <div class="card b-radius--10 mb-3">
        <div class="card-header"><h5 class="mb-0">@lang('New post')</h5></div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.linkedin.posts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>@lang('Text')</label>
                    <textarea name="commentary" class="form-control" rows="5" maxlength="3000" required>{{ old('commentary') }}</textarea>
                </div>
                <div class="form-group">
                    <label>@lang('Picture')</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required>
                    <small class="text-muted">@lang('JPG or PNG, up to 5 MB. A wide image around 1200 by 627 works well.')</small>
                </div>
                <button class="btn btn--primary" type="submit">@lang('Save post')</button>
            </form>
        </div>
    </div>

    @foreach($openPosts as $post)
        <div class="card b-radius--10 mb-3">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">{{ $post->source === 'scheduled' ? __('Scheduled draft') : __('Draft') }}</h5>
                <span class="badge badge--{{ $post->statusClass() }}">{{ $post->statusLabel() }}</span>
            </div>
            <div class="card-body">
                @if($post->error)
                    <div class="alert alert-danger">{{ $post->error }}</div>
                @endif
                @if($post->image)
                    <img src="{{ getImage($post->image) }}" alt="" class="mb-3" style="max-width:280px;height:auto;">
                @else
                    <div class="alert alert-warning">@lang('Add a picture before this post can be approved.')</div>
                @endif
                <form method="post" action="{{ route('admin.linkedin.posts.update', $post->id) }}">
                    @csrf
                    <div class="form-group">
                        <textarea name="commentary" class="form-control" rows="5" maxlength="3000" required>{{ $post->commentary }}</textarea>
                    </div>
                    <button class="btn btn-sm btn-outline--primary" type="submit">@lang('Save text')</button>
                </form>
                <form method="post" action="{{ route('admin.linkedin.posts.image', $post->id) }}" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <div class="form-group">
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png" required>
                    </div>
                    <button class="btn btn-sm btn-outline--primary" type="submit">@lang('Upload picture')</button>
                </form>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if(in_array($post->status, ['pending_approval', 'failed'], true))
                        <form method="post" action="{{ route('admin.linkedin.posts.approve', $post->id) }}">@csrf<button class="btn btn-sm btn--success" type="submit">@lang('Approve')</button></form>
                    @endif
                    @if($post->status === 'failed')
                        <form method="post" action="{{ route('admin.linkedin.posts.retry', $post->id) }}">@csrf<button class="btn btn-sm btn-outline--primary" type="submit">@lang('Retry publish')</button></form>
                    @endif
                    @if($post->status !== 'published')
                        <form method="post" action="{{ route('admin.linkedin.posts.reject', $post->id) }}">@csrf<button class="btn btn-sm btn-outline--danger" type="submit">@lang('Reject')</button></form>
                    @endif
                </div>
                @if($post->scheduled_for)
                    <small class="text-muted d-block mt-2">@lang('Scheduled') {{ $post->scheduled_for->format('d M Y') }}</small>
                @endif
            </div>
        </div>
    @endforeach

    <div class="card b-radius--10">
        <div class="card-header"><h5 class="mb-0">@lang('Published')</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Text')</th>
                            <th>@lang('Picture')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($published as $post)
                            <tr>
                                <td>{{ $post->published_at ? showDateTime($post->published_at, 'd M Y') : '—' }}</td>
                                <td>{{ strLimit($post->commentary, 120) }}</td>
                                <td>@if($post->image)<img src="{{ getImage($post->image) }}" alt="" style="height:48px;">@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-center text-muted">@lang('No posts published yet.')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($published->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($published) }}</div>
        @endif
    </div>
@endsection

@push('breadcrumb-plugins')
    @include('admin.linkedin.partials.nav')
@endpush
