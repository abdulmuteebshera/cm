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
                                    <th>@lang('Department')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Applications')</th>
                                    <th>@lang('Posted')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobPosts as $jobPost)
                                    <tr>
                                        <td>{{ __($jobPost->title) }}</td>
                                        <td>{{ __($jobPost->department ?: '—') }}</td>
                                        <td>{{ __($jobPost->location ?: '—') }}</td>
                                        <td>{{ __($jobPost->employment_type ?: '—') }}</td>
                                        <td>{!! $jobPost->statusBadge !!}</td>
                                        <td>
                                            @if ($jobPost->applications_count > 0)
                                                <a href="{{ route('admin.job.application.index', ['job_id' => $jobPost->id]) }}" class="badge badge--primary">
                                                    {{ $jobPost->applications_count }} @lang('applicant(s)')
                                                </a>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>{{ showDateTime($jobPost->created_at, 'd M Y') }}</td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-outline--primary btn-sm editBtn"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $jobPost->id }}"
                                                data-title="{{ $jobPost->title }}"
                                                data-department="{{ $jobPost->department }}"
                                                data-location="{{ $jobPost->location }}"
                                                data-employment_type="{{ $jobPost->employment_type }}"
                                                data-summary="{{ $jobPost->summary }}"
                                                data-description="{{ $jobPost->description }}"
                                                data-requirements="{{ $jobPost->requirements }}"
                                                data-status="{{ $jobPost->status }}"
                                                data-route="{{ route('admin.job.post.update', $jobPost->id) }}">
                                                <i class="las la-pen"></i>@lang('Edit')
                                            </button>
                                            @if ($jobPost->status)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this job posting?')" data-action="{{ route('admin.job.post.status', $jobPost->id) }}"><i class="las la-eye-slash"></i>@lang('Disable')</button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this job posting?')" data-action="{{ route('admin.job.post.status', $jobPost->id) }}"><i class="las la-eye"></i>@lang('Enable')</button>
                                            @endif
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this job posting?')" data-action="{{ route('admin.job.post.delete', $jobPost->id) }}"><i class="las la-trash"></i>@lang('Delete')</button>
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
                @if ($jobPosts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($jobPosts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('New Job Opportunity')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="{{ route('admin.job.post.store') }}">
                    @csrf
                    <div class="modal-body">
                        @include('admin.job_post.form_fields')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-send"></i> @lang('Publish')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Edit Job Opportunity')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal"><span><i class="las la-times"></i></span></button>
                </div>
                <form method="post" action="">
                    @csrf
                    <div class="modal-body">
                        @include('admin.job_post.form_fields')
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
                var $btn = $(this);
                modal.find('form').attr('action', $btn.data('route'));
                modal.find('input[name=title]').val($btn.data('title'));
                modal.find('input[name=department]').val($btn.data('department'));
                modal.find('input[name=location]').val($btn.data('location'));
                modal.find('input[name=employment_type]').val($btn.data('employment_type'));
                modal.find('textarea[name=summary]').val($btn.data('summary'));
                modal.find('textarea[name=description]').val($btn.data('description'));
                modal.find('textarea[name=requirements]').val($btn.data('requirements'));
                modal.find('select[name=status]').val($btn.data('status').toString());
            });
        })(jQuery);
    </script>
@endpush
