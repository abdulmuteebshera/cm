@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-8">
            <div class="card b-radius--10 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Applicant Details')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Position')</span>
                            <span>{{ __($application->jobPost->title ?? '—') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Name')</span>
                            <span>{{ __($application->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Email')</span>
                            <a href="mailto:{{ $application->email }}">{{ $application->email }}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Phone')</span>
                            <span>{{ $application->phone ?: '—' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('LinkedIn')</span>
                            @if ($application->linkedin)
                                <a href="{{ $application->linkedin }}" target="_blank" rel="noopener noreferrer">{{ $application->linkedin }}</a>
                            @else
                                <span>—</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">@lang('Applied')</span>
                            <span>{{ showDateTime($application->created_at, 'd M Y, h:i A') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">@lang('Résumé')</span>
                            <a href="{{ route('admin.job.application.resume', $application->id) }}" class="btn btn-sm btn-outline--info">
                                <i class="las la-download"></i> {{ $application->resume_original_name ?: __('Download') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Cover Letter / Message')</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-line;">{{ $application->message }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Review')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.job.application.update', $application->id) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="application_status" class="form-control" required>
                                @foreach (\App\Models\JobApplication::statusOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected($application->application_status == $value)>@lang($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Admin Notes')</label>
                            <textarea name="admin_notes" class="form-control" rows="6" placeholder="@lang('Internal notes about this applicant')">{{ $application->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="fa fa-save"></i> @lang('Save')</button>
                    </form>
                </div>
            </div>

            <div class="card b-radius--10 mt-4">
                <div class="card-body">
                    <a href="{{ route('admin.job.application.index', ['job_id' => $application->job_post_id]) }}" class="btn btn-outline--dark w-100 mb-2">
                        <i class="las la-list"></i> @lang('All applicants for this role')
                    </a>
                    <button class="btn btn-outline--danger w-100 confirmationBtn" data-question="@lang('Are you sure to delete this application?')" data-action="{{ route('admin.job.application.delete', $application->id) }}">
                        <i class="las la-trash"></i> @lang('Delete Application')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.job.application.index') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-arrow-left"></i> @lang('Back')
    </a>
@endpush
