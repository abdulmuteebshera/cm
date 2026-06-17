@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ route('plan') }}" class="btn btn--base">
                        @lang('Investment Plan')
                    </a>
                </div>
            </div>
            @include($activeTemplate . 'partials.invest_history', ['invests' => $invests])
            @if ($invests->hasPages())
                <div class="card-footer">
                    {{ $invests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
