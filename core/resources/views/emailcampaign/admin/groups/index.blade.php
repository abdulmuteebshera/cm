@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    <h2 class="crm-card__title">New email group</h2>
    <form method="post" action="{{ route('ec.admin.groups.store') }}" class="crm-form crm-form--stack">
        @csrf
        <label><span>Group name</span><input type="text" name="name" required></label>
        <label><span>Description</span><input type="text" name="description"></label>
        <label class="crm-login__remember">
            <input type="checkbox" name="is_private" value="1"> Private (only admin sees this group; hidden from all campaign users)
        </label>
        <p class="ec-hint">Leave unchecked to share this group with every campaign user.</p>
        <button type="submit" class="crm-btn crm-btn--accent">Create group</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Admin groups</h2>
    @foreach($groups as $group)
        <div class="crm-list-row">
            <div>
                <strong>{{ $group->name }}</strong>
                @if($group->is_private)<span class="ec-tag ec-tag--draft">Private</span>@else<span class="ec-tag ec-tag--running">Shared with users</span>@endif
                <span>{{ $group->members_count }} recipients</span>
            </div>
            <a href="{{ route('ec.admin.groups.show', $group->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">Manage</a>
        </div>
    @endforeach
    {{ $groups->links() }}
</section>
@endsection
