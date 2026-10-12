@extends('admin.layouts.app')
@section('panel')
    @if($prospect->do_not_contact)
        <div class="alert alert-danger">
            @if($prospect->stop_reason === 'qualified')
                @lang('This is a qualified opportunity. Automatic outreach has stopped.')
            @elseif($prospect->stop_reason === 'opted_out')
                @lang('This person opted out. They will not be contacted again.')
            @else
                @lang('This person declined. They will not be contacted again.')
            @endif
            <form method="post" action="{{ route('admin.linkedin.prospects.undo', $prospect->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline--dark ms-2">@lang('Undo if this was recorded by mistake')</button>
            </form>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-5 mb-3">
            <div class="card b-radius--10">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">{{ $prospect->name }}</h5>
                    <span class="badge badge--{{ $prospect->statusClass() }}">{{ $prospect->statusLabel() }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>@lang('Business'):</strong> <a href="{{ $prospect->profile_url }}" target="_blank" rel="noopener">{{ $prospect->company }}</a></p>
                    <p class="mb-2"><strong>@lang('Profile'):</strong> <a href="{{ $prospect->profile_url }}" target="_blank" rel="noopener">{{ $prospect->profile_url }}</a></p>
                    <p class="mb-2"><strong>@lang('Role'):</strong> {{ $prospect->title ?: '—' }}</p>
                    <p class="mb-2"><strong>@lang('Location'):</strong> {{ $prospect->location ?: '—' }}</p>
                    <p class="mb-2"><strong>@lang('Outreach date'):</strong> {{ $prospect->outreach_date ? $prospect->outreach_date->format('d M Y') : '—' }}</p>
                    <p class="mb-2"><strong>@lang('Response'):</strong> {{ $prospect->response === 'none' ? '—' : ucfirst(str_replace('_', ' ', $prospect->response)) }}</p>
                    <p class="mb-0"><strong>@lang('Follow-up'):</strong> {{ $prospect->follow_up_status === 'none' ? '—' : ucfirst(str_replace('_', ' ', $prospect->follow_up_status)) }}</p>
                    @if($prospect->public_note)
                        <hr>
                        <p class="mb-0"><strong>@lang('Public note'):</strong> {{ $prospect->public_note }}</p>
                    @endif
                </div>
            </div>

            @if($prospect->canEditRecord())
                <div class="card b-radius--10 mt-3">
                    <div class="card-header"><h5 class="mb-0">@lang('Edit details')</h5></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.linkedin.prospects.update', $prospect->id) }}">
                            @csrf
                            <div class="form-group"><label>@lang('Name')</label><input class="form-control" name="name" value="{{ $prospect->name }}" required></div>
                            <div class="form-group"><label>@lang('Business')</label><input class="form-control" name="company" value="{{ $prospect->company }}" required></div>
                            <div class="form-group"><label>@lang('Role')</label><input class="form-control" name="title" value="{{ $prospect->title }}"></div>
                            <div class="form-group"><label>@lang('Profile URL')</label><input class="form-control" name="profile_url" value="{{ $prospect->profile_url }}" required></div>
                            <div class="form-group"><label>@lang('Location')</label><input class="form-control" name="location" value="{{ $prospect->location }}"></div>
                            <div class="form-group"><label>@lang('Public note')</label><textarea class="form-control" name="public_note" rows="3">{{ $prospect->public_note }}</textarea></div>
                            <button class="btn btn--primary btn-sm" type="submit">@lang('Save')</button>
                        </form>
                        @if($prospect->canDelete())
                            <form method="post" action="{{ route('admin.linkedin.prospects.delete', $prospect->id) }}" class="mt-3">
                                @csrf
                                <button class="btn btn-sm btn-outline--danger" type="submit">@lang('Remove prospect')</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-7 mb-3">
            @if(in_array($prospect->outreach_status, ['not_a_fit', 'too_small'], true))
                <div class="alert alert-warning">
                    @if($prospect->outreach_status === 'too_small')
                        @lang('This profile looks too small for a $20,000 allocation. Include them only if you know they can fund the account.')
                    @else
                        @lang('The role does not match the founder, owner, and family-office audience.')
                    @endif
                    <form method="post" action="{{ route('admin.linkedin.prospects.include', $prospect->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline--primary ms-2" type="submit">@lang('Include anyway')</button>
                    </form>
                </div>
            @endif

            @foreach($prospect->messages as $message)
                <div class="card b-radius--10 mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="mb-0">{{ $message->kindLabel() }}</h5>
                        <span class="badge badge--dark">{{ ucfirst(str_replace('_', ' ', $message->status)) }}</span>
                    </div>
                    <div class="card-body">
                        @if($message->basis)
                            <p class="text-muted">{{ $message->basis }}</p>
                        @endif
                        @if($message->isOpen())
                            <form method="post" action="{{ route('admin.linkedin.messages.update', $message->id) }}">
                                @csrf
                                <div class="form-group">
                                    <textarea id="message-{{ $message->id }}" name="body" class="form-control" rows="4" maxlength="300" required>{{ $message->body }}</textarea>
                                    <small class="text-muted">@lang('LinkedIn allows 300 characters on Premium and 200 on a free account.')</small>
                                </div>
                                <button class="btn btn-sm btn-outline--primary" type="submit">@lang('Save text')</button>
                                <button class="btn btn-sm btn-outline--dark js-copy" type="button" data-target="message-{{ $message->id }}">@lang('Copy')</button>
                            </form>
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline--primary" href="{{ $prospect->profile_url }}" target="_blank" rel="noopener">@lang('Open profile')</a>
                                @if($message->status === 'pending_approval')
                                    <form method="post" action="{{ route('admin.linkedin.messages.approve', $message->id) }}">@csrf<button class="btn btn-sm btn--success" type="submit">@lang('Approve and mark sent')</button></form>
                                @endif
                                @if($message->status === 'approved')
                                    <form method="post" action="{{ route('admin.linkedin.messages.sent', $message->id) }}">@csrf<button class="btn btn-sm btn--primary" type="submit">@lang('Mark as sent')</button></form>
                                @endif
                                <form method="post" action="{{ route('admin.linkedin.messages.reject', $message->id) }}">@csrf<button class="btn btn-sm btn-outline--danger" type="submit">@lang('Reject')</button></form>
                            </div>
                        @else
                            <p class="mb-2">{{ $message->body }}</p>
                            <a class="btn btn-sm btn-outline--primary" href="{{ $prospect->profile_url }}" target="_blank" rel="noopener">@lang('Open profile')</a>
                            @if($message->sent_at)
                                <small class="text-muted d-block mt-2">@lang('Sent') {{ showDateTime($message->sent_at) }}</small>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach

            @if(!$prospect->do_not_contact && in_array($prospect->outreach_status, ['sent', 'accepted', 'replied'], true))
                <div class="card b-radius--10 mb-3">
                    <div class="card-header"><h5 class="mb-0">@lang('Record a response')</h5></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.linkedin.prospects.response', $prospect->id) }}">
                            @csrf
                            <div class="form-group">
                                <select name="response" class="form-control" required>
                                    <option value="accepted">@lang('Accepted the connection')</option>
                                    <option value="replied">@lang('Replied')</option>
                                    <option value="qualified">@lang('Qualified opportunity')</option>
                                    <option value="declined">@lang('Declined')</option>
                                    <option value="opted_out">@lang('Opted out')</option>
                                </select>
                            </div>
                            <button class="btn btn--primary btn-sm" type="submit">@lang('Save response')</button>
                        </form>
                        <small class="text-muted d-block mt-2">@lang('Declined and opted out stops every future note. A follow-up is prepared only after they accept.')</small>
                    </div>
                </div>
            @endif

            @if(!$prospect->do_not_contact && in_array($prospect->outreach_status, ['accepted', 'replied'], true))
                <form method="post" action="{{ route('admin.linkedin.prospects.followup', $prospect->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline--primary" type="submit">@lang('Prepare another follow-up')</button>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @include('admin.linkedin.partials.nav')
@endpush

@push('script')
<script>
    document.querySelectorAll('.js-copy').forEach(function (button) {
        button.addEventListener('click', function () {
            var field = document.getElementById(button.getAttribute('data-target'));
            if (!field) return;
            navigator.clipboard.writeText(field.value).then(function () {
                button.textContent = 'Copied';
            });
        });
    });
</script>
@endpush
