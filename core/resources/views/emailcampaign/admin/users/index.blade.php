@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    <h2 class="crm-card__title">Add campaign user</h2>
    <form method="post" action="{{ route('ec.admin.users.store') }}" class="crm-form crm-form--stack">
        @csrf
        <div class="crm-form__grid">
            <label><span>Full name</span><input type="text" name="name" required></label>
            <label><span>Username</span><input type="text" name="username" required></label>
            <label><span>Email (optional)</span><input type="email" name="email"></label>
            <label><span>Password</span><input type="password" name="password" required minlength="8"></label>
            <label><span>Confirm password</span><input type="password" name="password_confirmation" required></label>
        </div>
        <button type="submit" class="crm-btn crm-btn--accent">Create user</button>
    </form>
</section>

<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Users</h2>
    @foreach($users as $user)
        <details class="crm-details" style="margin-bottom:10px">
            <summary>{{ $user->name }} ({{ $user->username }}) — {{ $user->campaigns_count }} campaigns</summary>
            <form method="post" action="{{ route('ec.admin.users.update', $user->id) }}" class="crm-form crm-form--stack" style="margin-top:10px">
                @csrf
                <div class="crm-form__grid">
                    <label><span>Name</span><input type="text" name="name" value="{{ $user->name }}" required></label>
                    <label><span>Username</span><input type="text" name="username" value="{{ $user->username }}" required></label>
                    <label><span>Email</span><input type="email" name="email" value="{{ $user->email }}"></label>
                    <label><span>Status</span>
                        <select name="status">
                            <option value="1" @selected($user->status == 1)>Active</option>
                            <option value="0" @selected($user->status == 0)>Disabled</option>
                        </select>
                    </label>
                    <label><span>New password</span><input type="password" name="password" placeholder="Optional"></label>
                    <label><span>Confirm</span><input type="password" name="password_confirmation"></label>
                </div>
                <button type="submit" class="crm-btn crm-btn--accent crm-btn--sm">Update</button>
            </form>
        </details>
    @endforeach
    {{ $users->links() }}
</section>
@endsection
