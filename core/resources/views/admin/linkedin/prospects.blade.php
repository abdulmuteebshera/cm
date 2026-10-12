@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card b-radius--10">
                <div class="card-header"><h5 class="mb-0">@lang('Add a prospect')</h5></div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.linkedin.prospects.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Business')</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company') }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Role')</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Founder">
                        </div>
                        <div class="form-group">
                            <label>@lang('LinkedIn profile URL')</label>
                            <input type="url" name="profile_url" class="form-control" value="{{ old('profile_url') }}" placeholder="https://www.linkedin.com/in/name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Location')</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('Public note')</label>
                            <textarea name="public_note" class="form-control" rows="3" placeholder="What you already know from their public profile">{{ old('public_note') }}</textarea>
                            <small class="text-muted">@lang('A short public fact, such as the company they own or a recent expansion. Welcome notes are for people who can open an account from $20,000. Larger clients are queued first.')</small>
                        </div>
                        <button type="submit" class="btn btn--primary">@lang('Add prospect')</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card b-radius--10">
                <div class="card-header"><h5 class="mb-0">@lang('Import CSV')</h5></div>
                <div class="card-body">
                    <p>@lang('Columns: name, company, title, profile_url, location, public_note. Up to 100 rows at a time. Only profile URLs that look like linkedin.com/in/name are accepted.')</p>
                    <form method="post" action="{{ route('admin.linkedin.prospects.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="file" class="form-control" accept=".csv,text/csv" required>
                        </div>
                        <button type="submit" class="btn btn-outline--primary">@lang('Import')</button>
                    </form>
                    <form method="post" action="{{ route('admin.linkedin.prospects.search') }}" class="mt-4">
                        @csrf
                        <p>@lang('Search more public profiles of chief executives, founders, and chairmen. People already on the list are skipped.')</p>
                        <button type="submit" class="btn btn--primary">@lang('Search more')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Business')</th>
                            <th>@lang('Role')</th>
                            <th>@lang('Outreach date')</th>
                            <th>@lang('Response')</th>
                            <th>@lang('Follow-up')</th>
                            <th>@lang('Status')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prospects as $prospect)
                            <tr>
                                <td>{{ $prospect->name }}</td>
                                <td>
                                    <a href="{{ $prospect->profile_url }}" target="_blank" rel="noopener">{{ $prospect->company }}</a>
                                    @if($prospect->location)
                                        <br><small class="text-muted">{{ $prospect->location }}</small>
                                    @endif
                                </td>
                                <td>{{ $prospect->title ?: '—' }}</td>
                                <td>{{ $prospect->outreach_date ? $prospect->outreach_date->format('d M Y') : '—' }}</td>
                                <td>{{ $prospect->response === 'none' ? '—' : ucfirst(str_replace('_', ' ', $prospect->response)) }}</td>
                                <td>{{ $prospect->follow_up_status === 'none' ? '—' : ucfirst(str_replace('_', ' ', $prospect->follow_up_status)) }}</td>
                                <td><span class="badge badge--{{ $prospect->statusClass() }}">{{ $prospect->statusLabel() }}</span></td>
                                <td><a href="{{ route('admin.linkedin.prospects.show', $prospect->id) }}" class="btn btn-sm btn-outline--primary">@lang('Details')</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-center text-muted">@lang('No prospects yet.')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($prospects->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($prospects) }}</div>
        @endif
    </div>
@endsection

@push('breadcrumb-plugins')
    @include('admin.linkedin.partials.nav')
    <form method="get" class="d-flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="@lang('Name or business')">
        <select name="status" class="form-control form-control-sm">
            <option value="">@lang('All statuses')</option>
            @foreach(['pending_approval' => 'Needs approval', 'approved' => 'Ready to send', 'sent' => 'Invite sent', 'accepted' => 'Connected', 'replied' => 'Replied', 'qualified' => 'Qualified', 'declined' => 'Declined', 'opted_out' => 'Opted out', 'not_a_fit' => 'Not a fit', 'too_small' => 'Below $20k fit', 'new' => 'Queued'] as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ __($label) }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn--primary" type="submit">@lang('Filter')</button>
    </form>
@endpush
