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
                                    <th>@lang('Title')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $announcement)
                                    <tr>
                                        <td>{{ __($announcement->title) }}</td>
                                        <td>{{ strLimit(strip_tags($announcement->content), 60) }}</td>
                                        <td>{!! $announcement->statusBadge !!}</td>
                                        <td>{{ showDateTime($announcement->created_at, 'd M Y') }}</td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline--primary btn-sm editBtn"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $announcement->id }}"
                                                data-title="{{ $announcement->title }}"
                                                data-content="{{ $announcement->content }}"
                                                data-status="{{ $announcement->status }}"
                                                data-route="{{ route('admin.announcement.update', $announcement->id) }}">
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </button>
                                            @if ($announcement->status)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this announcement?')" data-action="{{ route('admin.announcement.status', $announcement->id) }}"><i class="las la-eye-slash"></i>@lang('Disable')</button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this announcement?')" data-action="{{ route('admin.announcement.status', $announcement->id) }}"><i class="las la-eye"></i>@lang('Enable')</button>
                                            @endif
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this announcement?')" data-action="{{ route('admin.announcement.delete', $announcement->id) }}"><i class="las la-trash"></i>@lang('Delete')</button>
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
                @if ($announcements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($announcements) }}
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
                    <h4 class="modal-title">@lang('New Announcement')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="{{ route('admin.announcement.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Title')</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Message')</label>
                            <textarea class="form-control" name="content" rows="5" required></textarea>
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
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> @lang('Publish')</button>
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
                    <h4 class="modal-title">@lang('Edit Announcement')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Title')</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Message')</label>
                            <textarea class="form-control" name="content" rows="5" required></textarea>
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
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> @lang('Update')</button>
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
                modal.find('form').attr('action', $(this).data('route'));
                modal.find('input[name=title]').val($(this).data('title'));
                modal.find('textarea[name=content]').val($(this).data('content'));
                modal.find('select[name=status]').val($(this).data('status').toString());
            });
        })(jQuery);
    </script>
@endpush
