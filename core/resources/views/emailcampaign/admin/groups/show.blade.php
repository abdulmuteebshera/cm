@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    <form method="post" action="{{ route('ec.admin.groups.update', $group->id) }}" class="crm-form crm-form--stack">
        @csrf
        <label><span>Group name</span><input type="text" name="name" value="{{ $group->name }}" required></label>
        <label><span>Description</span><input type="text" name="description" value="{{ $group->description }}"></label>
        <label><span>Visibility</span>
            <select name="is_private">
                <option value="0" @selected(!$group->is_private)>Shared — all campaign users can use this group</option>
                <option value="1" @selected($group->is_private)>Private — admin only</option>
            </select>
        </label>
        <button type="submit" class="crm-btn crm-btn--ghost crm-btn--sm">Save settings</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Add recipient manually</h2>
    <form method="post" action="{{ route('ec.admin.groups.members.add', $group->id) }}" class="crm-form crm-form__grid">
        @csrf
        <label><span>Email</span><input type="email" name="email" required></label>
        <label><span>Name</span><input type="text" name="name"></label>
        <button type="submit" class="crm-btn crm-btn--accent">Add</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Import CSV or Excel</h2>
    <p class="ec-hint">First row must include an <code>email</code> column; optional <code>name</code>.</p>
    <form method="post" action="{{ route('ec.admin.groups.members.import', $group->id) }}" enctype="multipart/form-data" class="crm-form crm-form--stack">
        @csrf
        <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required>
        <button type="submit" class="crm-btn crm-btn--accent">Upload list</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Members ({{ $group->members->count() }})</h2>
    @foreach($group->members as $member)
        <div class="crm-list-row">
            <div><strong>{{ $member->email }}</strong><span>{{ $member->name }}</span></div>
            <form method="post" action="{{ route('ec.admin.groups.members.destroy', [$group->id, $member->id]) }}">
                @csrf
                <button type="submit" class="crm-btn crm-btn--danger crm-btn--sm">Remove</button>
            </form>
        </div>
    @endforeach
</section>
@endsection
