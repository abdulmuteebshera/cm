@extends('emailcampaign.layouts.master')
@section('body-class', 'crm-login-page')
@section('content')
<div class="crm-login">
    <div class="crm-login__panel">
        <div class="crm-login__brand">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo.png')) }}" alt="Crownmaire">
            <h1>Crownmaire Capital</h1>
            <p>{{ $pageTitle }}</p>
        </div>
        <form method="post" action="{{ route('ec.admin.login') }}" class="crm-login__form">
            @csrf
            <label>
                <span>Admin username</span>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <label class="crm-login__remember">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button type="submit" class="crm-btn crm-btn--accent w-100">Sign in to admin</button>
        </form>
        <p class="crm-login__note"><a href="{{ route('ec.user.login') }}">Campaign user login</a></p>
    </div>
</div>
@endsection
