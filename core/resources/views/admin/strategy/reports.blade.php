@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">@lang('Upload Strategy Report')</h5>
                    <p class="text-muted">@lang('Upload a PDF report for a specific strategy and year. Users will see it under that year\'s performance chart.')</p>
                    <form action="{{ route('admin.strategy.reports.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Strategy')</label>
                            <select name="plan_id" class="form-control" required>
                                <option value="">@lang('Select strategy')</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ __($plan->name) }} ({{ $plan->payoutFrequencyLabel() }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Year')</label>
                            <select name="year" class="form-control" required>
                                @foreach($entryYears as $entryYear)
                                    <option value="{{ $entryYear }}" @selected(old('year', date('Y')) == $entryYear)>{{ $entryYear }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('PDF Report')</label>
                            <input type="file" name="report" class="form-control" accept="application/pdf,.pdf" required>
                            <small class="text-muted">@lang('PDF only, max 10MB. Uploading again replaces the existing report for that strategy and year.')</small>
                        </div>
                        <button type="submit" class="btn btn--primary w-100">@lang('Upload Report')</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Strategy')</th>
                                    <th>@lang('Year')</th>
                                    <th>@lang('File')</th>
                                    <th>@lang('Uploaded')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>{{ __($report->plan->name ?? 'Strategy') }}</td>
                                        <td>{{ $report->year }}</td>
                                        <td>{{ $report->displayName() }}</td>
                                        <td>{{ showDateTime($report->updated_at) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Delete this strategy report?')" data-action="{{ route('admin.strategy.reports.delete', $report->id) }}">
                                                <i class="las la-trash"></i> @lang('Delete')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-muted text-center">@lang('No strategy reports uploaded yet.')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
