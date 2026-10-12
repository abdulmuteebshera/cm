@extends('admin.layouts.app')
@section('panel')
    <div class="card b-radius--10 mb-3">
        <div class="card-body">
            <p class="mb-2">@lang('Each day the queue prepares up to 20 welcome notes for founders, owners, chairmen, and family offices who can allocate at least $20,000. Larger clients are written first. Add each person with a public profile URL and a short note about the business.')</p>
            <p class="mb-2">@lang('Approve the welcome note, then send the connection request from your LinkedIn account and mark it sent. LinkedIn does not allow this portal to send the invite.')</p>
            <p class="mb-0">@lang('Posts are written from the Crownmaire site, with the firm’s keywords and hashtags. Create a post from a short context, review the picture, and approve it. It publishes through LinkedIn’s official posting API.')</p>
        </div>
    </div>

    @if($settings->last_error)
        <div class="alert alert-danger">{{ $settings->last_error }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-3 mb-3">
            <div class="card b-radius--10 h-100"><div class="card-body">
                <h3 class="mb-1">{{ $report->researched }}</h3>
                <span class="text-muted">@lang('Researched today')</span>
            </div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card b-radius--10 h-100"><div class="card-body">
                <h3 class="mb-1">{{ $report->approved }}</h3>
                <span class="text-muted">@lang('Approved today')</span>
            </div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card b-radius--10 h-100"><div class="card-body">
                <h3 class="mb-1">{{ $report->replies }}</h3>
                <span class="text-muted">@lang('Replies today')</span>
            </div></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card b-radius--10 h-100"><div class="card-body">
                <h3 class="mb-1">{{ $report->qualified }}</h3>
                <span class="text-muted">@lang('Qualified today')</span>
            </div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card b-radius--10">
                <div class="card-header"><h5 class="mb-0">@lang('Settings')</h5></div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.linkedin.settings') }}">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Daily automation')</label>
                            <select name="enabled" class="form-control">
                                <option value="1" @selected($settings->enabled)>@lang('On')</option>
                                <option value="0" @selected(!$settings->enabled)>@lang('Off')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Require approval before sending')</label>
                            <select name="require_approval" class="form-control">
                                <option value="1" @selected($settings->require_approval)>@lang('On — review each note and post')</option>
                                <option value="0" @selected(!$settings->require_approval)>@lang('Off — approve today’s queue automatically')</option>
                            </select>
                            <small class="text-muted">@lang('Leave this on at the start. Turning it off approves the day’s welcome notes, up to 20, and publishes a ready post. You still send each connection request from LinkedIn.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Profile posts')</label>
                            <select name="posting_enabled" class="form-control">
                                <option value="1" @selected($settings->posting_enabled)>@lang('On — publish when a post is approved')</option>
                                <option value="0" @selected(!$settings->posting_enabled)>@lang('Off')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Leads per day')</label>
                            <input type="number" name="daily_connection_limit" class="form-control" min="1" max="20" value="{{ $settings->daily_connection_limit }}" required>
                            <small class="text-muted">@lang('Maximum 20. Bigger clients are prepared first.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Extra roles to include')</label>
                            <textarea name="audience_titles" class="form-control" rows="2" placeholder="director, managing owner">{{ $settings->audience_titles }}</textarea>
                            <small class="text-muted">@lang('Founders, owners, chairmen, CEOs, and family offices are already included. The queue prefers people who can allocate from $20,000, and it puts larger groups and holdings first.')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('LinkedIn Client ID')</label>
                            <input type="text" name="client_id" class="form-control" value="{{ $settings->client_id }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('LinkedIn Client Secret')</label>
                            <input type="password" name="client_secret" class="form-control" autocomplete="new-password" placeholder="{{ $settings->client_secret ? 'Saved — leave blank to keep it' : '' }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('Redirect URL')</label>
                            <input type="text" class="form-control" value="{{ $redirectUrl }}" readonly>
                            <small class="text-muted">@lang('Add this exact URL in the LinkedIn app’s Auth tab.')</small>
                        </div>
                        <button type="submit" class="btn btn--primary">@lang('Save settings')</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="card b-radius--10 mb-3">
                <div class="card-header"><h5 class="mb-0">@lang('Connected account')</h5></div>
                <div class="card-body">
                    @if($settings->isConnected())
                        <p class="mb-1"><strong>{{ $settings->member_name }}</strong></p>
                        @if($settings->member_email)
                            <p class="mb-2 text-muted">{{ $settings->member_email }}</p>
                        @endif
                        <p class="mb-3">@lang('Connected') {{ showDateTime($settings->connected_at, 'd M Y') }}</p>
                        <form method="post" action="{{ route('admin.linkedin.disconnect') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline--danger btn-sm">@lang('Disconnect')</button>
                        </form>
                    @else
                        <p class="mb-3">@lang('Not connected yet. Save the Client ID and secret, then connect the personal account you use for Crownmaire Capital.')</p>
                        <a href="{{ route('admin.linkedin.connect') }}" class="btn btn--primary btn-sm">@lang('Connect LinkedIn account')</a>
                    @endif
                </div>
            </div>
            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <p class="mb-2">@lang('Waiting for approval'): <strong>{{ $pendingMessages }}</strong> @lang('messages'), <strong>{{ $pendingPosts }}</strong> @lang('posts')</p>
                    <p class="mb-3">@lang('Ready to send'): <strong>{{ $readyMessages }}</strong></p>
                    <form method="post" action="{{ route('admin.linkedin.prospects.search') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn--primary btn-sm">@lang('Search more')</button>
                    </form>
                    <form method="post" action="{{ route('admin.linkedin.run') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline--primary btn-sm">@lang('Run today’s preparation')</button>
                    </form>
                    @if($settings->last_daily_run_at)
                        <small class="text-muted d-block mt-2">@lang('Last run') {{ showDateTime($settings->last_daily_run_at) }}</small>
                    @endif
                </div>
            </div>
            <div class="card b-radius--10">
                <div class="card-header"><h5 class="mb-0">@lang('How to connect the account')</h5></div>
                <div class="card-body">
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">@lang('Open') <a href="https://www.linkedin.com/developers/apps" target="_blank" rel="noopener">@lang('LinkedIn Developers')</a> @lang('and create an app for Crownmaire Capital.')</li>
                        <li class="mb-2">@lang('On the Products tab, add “Sign In with LinkedIn using OpenID Connect” and “Share on LinkedIn”.')</li>
                        <li class="mb-2">@lang('On the Auth tab, add the redirect URL shown on this page.')</li>
                        <li class="mb-2">@lang('Copy the Client ID and Client Secret into the form and save.')</li>
                        <li>@lang('Click Connect LinkedIn account and sign in with the personal profile you use inside Crownmaire Capital. Accept the permissions.')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-header"><h5 class="mb-0">@lang('Messages waiting')</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Person')</th>
                            <th>@lang('Business')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Note')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queue as $message)
                            <tr>
                                <td>{{ $message->prospect->name }}</td>
                                <td><a href="{{ $message->prospect->profile_url }}" target="_blank" rel="noopener">{{ $message->prospect->company }}</a></td>
                                <td>{{ $message->kindLabel() }}</td>
                                <td>{{ $message->status === 'approved' ? __('Ready to send') : __('Needs approval') }}</td>
                                <td>{{ strLimit($message->body, 80) }}</td>
                                <td><a href="{{ route('admin.linkedin.prospects.show', $message->prospect_id) }}" class="btn btn-sm btn-outline--primary">@lang('Open')</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-center text-muted">@lang('No messages are waiting.')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @include('admin.linkedin.partials.nav')
@endpush
