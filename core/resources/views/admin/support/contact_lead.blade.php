@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8">
            <div class="card b-radius--10 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Inquiry')</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Name')</span>
                                <strong>{{ $ticket->name }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Company')</span>
                                <strong>{{ $lead['company'] }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Email')</span>
                                <a href="mailto:{{ $ticket->email }}">{{ $ticket->email }}</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Phone')</span>
                                @if ($lead['phone'] !== '—')
                                    <a href="tel:{{ preg_replace('/\s+/', '', $lead['phone']) }}">{{ $lead['phone'] }}</a>
                                @else
                                    <strong>—</strong>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Reason')</span>
                                <strong>{{ $ticket->subject }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Approximate capital')</span>
                                <strong>{{ $lead['capital'] }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Submitted')</span>
                                <strong>{{ showDateTime($ticket->created_at, 'd M Y, h:i A') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <span class="text-muted d-block">@lang('Status')</span>
                                @php echo $ticket->leadStatusBadge(); @endphp
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded">
                                <span class="text-muted d-block mb-2">@lang('Message')</span>
                                <p class="mb-0" style="white-space: pre-line;">{{ $lead['message'] !== '' ? $lead['message'] : '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Follow up')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.status', $ticket->id) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control" required>
                                <option value="0" @selected((int) $ticket->status === 0)>@lang('New')</option>
                                <option value="1" @selected((int) $ticket->status === 1)>@lang('Contacted')</option>
                                <option value="3" @selected((int) $ticket->status === 3)>@lang('Closed')</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save status')</button>
                    </form>
                    <a href="mailto:{{ $ticket->email }}" class="btn btn-outline--primary w-100 mt-3">
                        <i class="las la-envelope"></i> @lang('Email this lead')
                    </a>
                    @if ($lead['phone'] !== '—')
                        <a href="tel:{{ preg_replace('/\s+/', '', $lead['phone']) }}" class="btn btn-outline--dark w-100 mt-2">
                            <i class="las la-phone"></i> @lang('Call this lead')
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.contact.index') }}" />
@endpush
