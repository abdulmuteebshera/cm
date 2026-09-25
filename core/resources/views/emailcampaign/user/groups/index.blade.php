@extends('emailcampaign.layouts.app_user')
@section('panel')
<section class="crm-card">
    <h2 class="crm-card__title">New email group</h2>
    <form method="post" action="{{ route('ec.user.groups.store') }}" class="crm-form crm-form--stack">
        @csrf
        <label><span>Group name</span><input type="text" name="name" required></label>
        <label><span>Description</span><input type="text" name="description"></label>
        <button type="submit" class="crm-btn crm-btn--accent">Create group</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Your groups</h2>
    @foreach($groups as $group)
        <div class="crm-list-row">
            <div>
                <strong>{{ $group->name }}</strong>
                @if($group->isSharedAdminGroup())<span class="ec-tag ec-tag--running">Shared by admin</span>@endif
                <span>{{ $group->members_count }} recipients</span>
            </div>
            <a href="{{ route('ec.user.groups.show', $group->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">Manage</a>
        </div>
    @endforeach
    {{ $groups->links() }}
</section>
@endsection
