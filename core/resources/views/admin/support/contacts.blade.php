@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                            <tr>
                                <th>@lang('Subject')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Submitted')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.ticket.view', $item->id) }}" class="fw-bold"> {{ strLimit($item->subject, 40) }} </a>
                                    </td>
                                    <td>
                                        <p class="fw-bold mb-0">{{ $item->name }}</p>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                                    </td>
                                    <td>
                                        @php echo $item->statusBadge; @endphp
                                    </td>
                                    <td>
                                        {{ showDateTime($item->created_at, 'd M Y, h:i A') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ticket.view', $item->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                            <i class="las la-desktop"></i> @lang('Details')
                                        </a>
                                    </td>
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
