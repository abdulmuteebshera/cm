@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    <h2 class="crm-card__title">Create template</h2>
    <p class="ec-hint">Users can pick templates and edit content on their campaigns; only admins create master templates here. Placeholders: <code>@{{name}}</code>, <code>@{{email}}</code>, plus any CSV column names.</p>
    <form method="post" action="{{ route('ec.admin.templates.store') }}" class="crm-form crm-form--stack">
        @csrf
        <label><span>Name</span><input type="text" name="name" required></label>
        <label><span>Subject</span><input type="text" name="subject" required></label>
        <label><span>HTML body</span><textarea name="body_html" rows="6" required></textarea></label>
        <label><span>Plain text (optional, helps deliverability)</span><textarea name="body_text" rows="4"></textarea></label>
        <button type="submit" class="crm-btn crm-btn--accent">Add template</button>
    </form>
</section>

<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Templates</h2>
    @foreach($templates as $template)
        <details class="crm-details" style="margin-bottom:12px">
            <summary>
                <strong>{{ $template->name }}</strong>
                @include('emailcampaign.partials.status_tag', ['status' => $template->status ? 'running' : 'draft'])
                <a href="{{ route('ec.admin.templates.preview', $template->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm" style="margin-left:8px" target="_blank" onclick="event.stopPropagation()">Preview</a>
            </summary>
            <form method="post" action="{{ route('ec.admin.templates.update', $template->id) }}" class="crm-form crm-form--stack" style="margin-top:12px">
                @csrf
                <label><span>Name</span><input type="text" name="name" value="{{ $template->name }}" required></label>
                <label><span>Subject</span><input type="text" name="subject" value="{{ $template->subject }}" required></label>
                <label><span>HTML body</span><textarea name="body_html" rows="6" required>{{ $template->body_html }}</textarea></label>
                <label><span>Plain text</span><textarea name="body_text" rows="4">{{ $template->body_text }}</textarea></label>
                <label><span>Status</span>
                    <select name="status">
                        <option value="1" @selected($template->status == 1)>Active</option>
                        <option value="0" @selected($template->status == 0)>Disabled</option>
                    </select>
                </label>
                <div class="crm-dialog__actions">
                    <button type="submit" class="crm-btn crm-btn--accent">Save</button>
                </div>
            </form>
            @if(!$template->campaigns()->exists())
                <form method="post" action="{{ route('ec.admin.templates.destroy', $template->id) }}" onsubmit="return confirm('Delete this template?')">
                    @csrf
                    <button type="submit" class="crm-btn crm-btn--danger crm-btn--sm">Delete</button>
                </form>
            @endif
        </details>
    @endforeach
    {{ $templates->links() }}
</section>
@endsection
