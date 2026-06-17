@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="dashboard-fluid position-relative">
        <div class="dashboard__inner">
            @include($activeTemplate . 'partials.sidebar')
            <div class="dashboard__right">
                @include($activeTemplate . 'partials.user_header')
                <div class="dashboard-body">
                @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
