@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light contact-lead-table">
                            <thead>
                            <tr>
                                <th>@lang('Submitted')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Company')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('Phone')</th>
                                <th>@lang('Reason')</th>
                                <th>@lang('Capital')</th>
                                <th>@lang('Message')</th>
                                <th>@lang('Status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $item)
                                @php $lead = $item->leadFields(); @endphp
                                <tr>
                                    <td>{{ showDateTime($item->created_at, 'd M Y, h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.contact.show', $item->id) }}" class="fw-bold">{{ $item->name }}</a>
                                    </td>
                                    <td>{{ $lead['company'] }}</td>
                                    <td><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></td>
                                    <td>
                                        @if ($lead['phone'] !== '—')
                                            <a href="tel:{{ preg_replace('/\s+/', '', $lead['phone']) }}">{{ $lead['phone'] }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $item->subject }}</td>
                                    <td>{{ $lead['capital'] }}</td>
                                    <td class="contact-lead-table__message">{{ $lead['message'] !== '' ? strLimit($lead['message'], 120) : '—' }}</td>
                                    <td>@php echo $item->leadStatusBadge(); @endphp</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($items->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($items) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <form method="get" class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg--white" placeholder="@lang('Name, email, phone')" style="width: 200px;">
        <select name="reason" class="form-control bg--white" style="width: 160px;">
            <option value="">@lang('All reasons')</option>
            @foreach (\App\Models\SupportTicket::inquiryReasons() as $reason)
                <option value="{{ $reason }}" @selected(request('reason') === $reason)>@lang($reason)</option>
            @endforeach
            <option value="Others" @selected(request('reason') === 'Others')>@lang('Others')</option>
        </select>
        <button class="btn btn--primary" type="submit"><i class="las la-search"></i></button>
        <a href="{{ route('admin.contact.pending') }}" class="btn btn-outline--warning">@lang('New only')</a>
    </form>
@endpush

@push('style')
    <style>
        .contact-lead-table th,
        .contact-lead-table td {
            vertical-align: top;
            white-space: normal;
        }
        .contact-lead-table__message {
            min-width: 220px;
            max-width: 320px;
        }
    </style>
@endpush
