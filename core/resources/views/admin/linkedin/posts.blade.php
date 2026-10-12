@extends('admin.layouts.app')
@section('panel')
    <div class="card b-radius--10 mb-3">
        <div class="card-body">
            <p class="mb-0">@lang('Write a short context. The text is written from that context and the Crownmaire site, with hashtags added automatically. The picture uses the Crownmaire logo, the site colors, and the contact details from the website, at LinkedIn’s portrait post size. Approve it to publish.')</p>
        </div>
    </div>

    <div class="card b-radius--10 mb-3">
        <div class="card-header"><h5 class="mb-0">@lang('Create post')</h5></div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.linkedin.posts.create') }}">
                @csrf
                <div class="form-group">
                    <label>@lang('Context')</label>
                    <textarea name="context" class="form-control" rows="4" maxlength="500" required placeholder="Example: Family offices in Dubai reviewing a private multi-asset program">{{ old('context') }}</textarea>
                </div>
                <button class="btn btn--primary" type="submit">@lang('Create post')</button>
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
                    <img src="{{ getImage($post->image) }}" alt="" class="mb-3" style="max-width:320px;height:auto;">
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
