@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-3">
        <div class="col-lg-12">
            @php $totalActive = (float) $totalActive; @endphp
            <div class="alert {{ abs($totalActive - 100) < 0.01 ? 'alert--success' : 'alert--warning' }} d-flex align-items-center" role="alert">
                <i class="las la-chart-pie fa-2x me-3"></i>
                <div>
                    <strong>@lang('Active allocation total'): {{ showAmount($totalActive) }}%</strong>
                    @if(abs($totalActive - 100) >= 0.01)
                        <div class="small">@lang('Tip: active allocations should add up to 100% for a balanced portfolio chart.')</div>
                    @else
                        <div class="small">@lang('Your active allocation is perfectly balanced at 100%.')</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Asset Class')</th>
                                    <th>@lang('Allocation')</th>
                                    <th>@lang('Color')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allocations as $allocation)
                                    <tr>
                                        <td>{{ $allocation->sort_order }}</td>
                                        <td>
                                            <span class="fw-bold">{{ __($allocation->name) }}</span>
                                            @if($allocation->description)
                                                <br><small class="text-muted">{{ strLimit($allocation->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($allocation->percentage) }}%</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-2">
                                                <span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:{{ $allocation->color }};border:1px solid #ddd;"></span>
                                                <code>{{ $allocation->color }}</code>
                                            </span>
                                        </td>
                                        <td>{!! $allocation->statusBadge !!}</td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline--primary btn-sm editBtn"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $allocation->id }}"
                                                data-name="{{ $allocation->name }}"
                                                data-percentage="{{ getAmount($allocation->percentage) }}"
                                                data-color="{{ $allocation->color }}"
                                                data-description="{{ $allocation->description }}"
                                                data-sort_order="{{ $allocation->sort_order }}"
                                                data-status="{{ $allocation->status }}"
                                                data-route="{{ route('admin.portfolio.allocation.update', $allocation->id) }}">
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </button>
                                            @if ($allocation->status)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this allocation?')" data-action="{{ route('admin.portfolio.allocation.status', $allocation->id) }}"><i class="las la-eye-slash"></i>@lang('Disable')</button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this allocation?')" data-action="{{ route('admin.portfolio.allocation.status', $allocation->id) }}"><i class="las la-eye"></i>@lang('Enable')</button>
                                            @endif
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this allocation?')" data-action="{{ route('admin.portfolio.allocation.delete', $allocation->id) }}"><i class="las la-trash"></i>@lang('Delete')</button>
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
                @if ($allocations->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($allocations) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create modal --}}
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Add Allocation')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="{{ route('admin.portfolio.allocation.store') }}">
                    @csrf
                    <div class="modal-body">
                        @include('admin.portfolio_allocation.form_fields')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="las la-plus"></i> @lang('Add')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit modal --}}
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Edit Allocation')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="">
                    @csrf
                    <div class="modal-body">
                        @include('admin.portfolio_allocation.form_fields')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="las la-pen"></i> @lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" data-bs-target="#addModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--primary"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.editBtn').on('click', function() {
                var modal = $('#editModal');
                var form  = modal.find('form');
                form.attr('action', $(this).data('route'));
                form.find('input[name=name]').val($(this).data('name'));
                form.find('input[name=percentage]').val($(this).data('percentage'));
                form.find('input[name=color]').val($(this).data('color'));
                form.find('input[name=description]').val($(this).data('description'));
                form.find('input[name=sort_order]').val($(this).data('sort_order'));
                form.find('select[name=status]').val($(this).data('status').toString());
            });
        })(jQuery);
    </script>
@endpush
