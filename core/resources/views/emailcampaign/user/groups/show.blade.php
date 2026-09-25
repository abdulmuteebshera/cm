@extends('emailcampaign.layouts.app_user')
@section('panel')
@if(!empty($readOnly))
    <p class="ec-hint">This is an admin shared group. You can use it in campaigns; only the administrator can edit members.</p>
@endif
@if(empty($readOnly))
<section class="crm-card">
    <h2 class="crm-card__title">Add recipient manually</h2>
    <form method="post" action="{{ route('ec.user.groups.members.add', $group->id) }}" class="crm-form crm-form__grid">
        @csrf
        <label><span>Email</span><input type="email" name="email" required></label>
        <label><span>Name</span><input type="text" name="name"></label>
        <button type="submit" class="crm-btn crm-btn--accent">Add</button>
    </form>
</section>
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Import CSV or Excel</h2>
    <p class="ec-hint">First row must include an <code>email</code> column; optional <code>name</code>. Extra columns become merge tags like <code>@{{company}}</code>.</p>
    <form method="post" action="{{ route('ec.user.groups.members.import', $group->id) }}" enctype="multipart/form-data" class="crm-form crm-form--stack">
        @csrf
        <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required>
        <button type="submit" class="crm-btn crm-btn--accent">Upload list</button>
    </form>
</section>
@endif
<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Members ({{ $group->members->count() }})</h2>
    @foreach($group->members as $member)
        <div class="crm-list-row">
            <div><strong>{{ $member->email }}</strong><span>{{ $member->name }}</span></div>
            @if(empty($readOnly))
            <form method="post" action="{{ route('ec.user.groups.members.destroy', [$group->id, $member->id]) }}">
                @csrf
                <button type="submit" class="crm-btn crm-btn--danger crm-btn--sm">Remove</button>
            </form>
            @endif
        </div>
    @endforeach
</section>
@endsection
