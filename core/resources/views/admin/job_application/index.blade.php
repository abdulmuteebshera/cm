@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="" method="get" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label>@lang('Filter by Job')</label>
                            <select name="job_id" class="form-control" onchange="this.form.submit()">
                                <option value="">@lang('All positions')</option>
                                @foreach ($jobPosts as $post)
                                    <option value="{{ $post->id }}" @selected($jobId == $post->id)>{{ __($post->title) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($jobId)
                            <div class="col-md-6">
                                <a href="{{ route('admin.job.application.index') }}" class="btn btn-outline--dark">@lang('Clear filter')</a>
                            </div>
                        @endif
                    </form>
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
                                    <th>@lang('Applicant')</th>
                                    <th>@lang('Position')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Phone')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Applied')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr>
                                        <td>{{ __($application->name) }}</td>
                                        <td>{{ __($application->jobPost->title ?? '—') }}</td>
                                        <td><a href="mailto:{{ $application->email }}">{{ $application->email }}</a></td>
                                        <td>{{ $application->phone ?: '—' }}</td>
                                        <td>{!! $application->statusBadge() !!}</td>
                                        <td>{{ showDateTime($application->created_at, 'd M Y, h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.job.application.detail', $application->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-eye"></i> @lang('View')
                                            </a>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this application?')" data-action="{{ route('admin.job.application.delete', $application->id) }}">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
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
                @if ($applications->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($applications) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
