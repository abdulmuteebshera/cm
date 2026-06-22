@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Rank')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Masked (Public)')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Invested Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>#{{ ($entries->firstItem() ?? 0) + $loop->index }}</td>
                                        <td>{{ __($entry->name) }}</td>
                                        <td><span class="fw-bold">{{ $entry->masked_name }}</span></td>
                                        <td>
                                            @if ($entry->user_id)
                                                <span class="badge badge--primary">@lang('User')</span>
                                                @if ($entry->user)
                                                    <br><small class="text-muted">{{ '@' . $entry->user->username }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge--dark">@lang('Dummy')</span>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($entry->amount) }} {{ gs('cur_text') }}</td>
                                        <td>{!! $entry->statusBadge !!}</td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline--primary btn-sm editBtn"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $entry->id }}"
                                                data-user_id="{{ $entry->user_id }}"
                                                data-username="{{ $entry->user->username ?? '' }}"
                                                data-name="{{ $entry->name }}"
                                                data-amount="{{ getAmount($entry->amount) }}"
                                                data-status="{{ $entry->status }}"
                                                data-route="{{ route('admin.leaderboard.update', $entry->id) }}">
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </button>
                                            @if ($entry->status)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this entry?')" data-action="{{ route('admin.leaderboard.status', $entry->id) }}"><i class="las la-eye-slash"></i>@lang('Disable')</button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this entry?')" data-action="{{ route('admin.leaderboard.status', $entry->id) }}"><i class="las la-eye"></i>@lang('Enable')</button>
                                            @endif
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this entry?')" data-action="{{ route('admin.leaderboard.delete', $entry->id) }}"><i class="las la-trash"></i>@lang('Delete')</button>
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
                @if ($entries->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($entries) }}
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
                    <h4 class="modal-title">@lang('Add Leaderboard Entry')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="{{ route('admin.leaderboard.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Entry Type')</label>
                            <select name="type" class="form-control entry-type">
                                <option value="user">@lang('Existing User')</option>
                                <option value="dummy">@lang('Dummy Entry')</option>
                            </select>
                        </div>
                        <div class="form-group type-user">
                            <label>@lang('Username')</label>
                            <input type="text" class="form-control" name="username" placeholder="@lang('Enter the username of an existing user')">
                            <small class="text-muted">@lang('Their full name will be pulled automatically and masked on the public leaderboard.')</small>
                        </div>
                        <div class="form-group type-dummy d-none">
                            <label>@lang('Display Name')</label>
                            <input type="text" class="form-control" name="name" placeholder="@lang('e.g. Michael')">
                        </div>
                        <div class="form-group">
                            <label>@lang('Invested Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" class="form-control" name="amount" required>
                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control">
                                <option value="1">@lang('Active')</option>
                                <option value="0">@lang('Inactive')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="las la-plus"></i> @lang('Add Entry')</button>
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
                    <h4 class="modal-title">@lang('Edit Leaderboard Entry')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Entry Type')</label>
                            <select name="type" class="form-control entry-type">
                                <option value="user">@lang('Existing User')</option>
                                <option value="dummy">@lang('Dummy Entry')</option>
                            </select>
                        </div>
                        <div class="form-group type-user">
                            <label>@lang('Username')</label>
                            <input type="text" class="form-control" name="username" placeholder="@lang('Enter the username of an existing user')">
                        </div>
                        <div class="form-group type-dummy d-none">
                            <label>@lang('Display Name')</label>
                            <input type="text" class="form-control" name="name" placeholder="@lang('e.g. Michael')">
                        </div>
                        <div class="form-group">
                            <label>@lang('Invested Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" min="0" class="form-control" name="amount" required>
                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control">
                                <option value="1">@lang('Active')</option>
                                <option value="0">@lang('Inactive')</option>
                            </select>
                        </div>
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

            function toggleType(form) {
                var type = form.find('select[name=type]').val();
                if (type === 'dummy') {
                    form.find('.type-dummy').removeClass('d-none');
                    form.find('.type-user').addClass('d-none');
                } else {
                    form.find('.type-user').removeClass('d-none');
                    form.find('.type-dummy').addClass('d-none');
                }
            }

            $('.entry-type').on('change', function() {
                toggleType($(this).closest('form'));
            });

            $('.editBtn').on('click', function() {
                var modal = $('#editModal');
                var form  = modal.find('form');
                var type  = $(this).data('user_id') ? 'user' : 'dummy';

                form.attr('action', $(this).data('route'));
                form.find('select[name=type]').val(type);
                form.find('input[name=username]').val($(this).data('username'));
                form.find('input[name=name]').val($(this).data('name'));
                form.find('input[name=amount]').val($(this).data('amount'));
                form.find('select[name=status]').val($(this).data('status').toString());
                toggleType(form);
            });

            // Initialise default state for the add modal.
            toggleType($('#addModal form'));
        })(jQuery);
    </script>
@endpush
