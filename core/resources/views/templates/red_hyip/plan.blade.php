@php
    if (!auth()->check() && @auth()->user()->profile_complete != 1) {
        $layout = 'frontend';
    }else{
        $layout = 'master';
    }
@endphp

@extends($activeTemplate . 'layouts.' . $layout)

@section('content')
    <section class="{{ $layout == 'frontend' ? 'pt-120 pb-60' : '' }}">
        <div class="container">
            <div class="row mb-none-50 justify-content-center">
                @include($activeTemplate . 'partials.plan', ['plans' => $plans])
            </div>
        </div>
    </section>

    @guest
        @if ($sections->secs != null)
            @foreach (json_decode($sections->secs) as $sec)
                @include($activeTemplate . 'sections.' . $sec)
            @endforeach
        @endif
    @endguest

@endsection
