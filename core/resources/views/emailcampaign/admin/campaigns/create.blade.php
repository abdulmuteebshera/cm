@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    <p class="ec-hint">Pick a template, customize content, optionally link an admin email group, then add recipients and start.</p>
    <form method="post" action="{{ route('ec.admin.campaigns.store') }}" class="crm-form crm-form--stack" id="ec-campaign-create">
        @csrf
        <label><span>Campaign name</span><input type="text" name="name" value="{{ old('name') }}" required></label>
        <label><span>Base template</span>
            <select name="ec_template_id" id="ec-template-select" required>
                @foreach($templates as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </label>
        <label><span>Link email group (optional)</span>
            <select name="ec_group_id">
                <option value="">— None —</option>
                @foreach($groups as $g)
                    <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->members_count }})</option>
                @endforeach
            </select>
        </label>
        <label><span>Subject</span><input type="text" name="subject" id="ec-subject" required></label>
        <label><span>HTML body</span><textarea name="body_html" id="ec-body-html" rows="8" required></textarea></label>
        <label><span>Plain text</span><textarea name="body_text" id="ec-body-text" rows="5"></textarea></label>
        <button type="submit" class="crm-btn crm-btn--accent">Create campaign</button>
    </form>
</section>
@push('script')
<script>
(function () {
    const templates = @json($templatesJson);
    const byId = Object.fromEntries(templates.map(t => [String(t.id), t]));
    const sel = document.getElementById('ec-template-select');
    const apply = () => {
        const t = byId[sel.value];
        if (!t) return;
        document.getElementById('ec-subject').value = t.subject || '';
        document.getElementById('ec-body-html').value = t.body_html || '';
        document.getElementById('ec-body-text').value = t.body_text || '';
    };
    sel.addEventListener('change', apply);
    apply();
})();
</script>
@endpush
@endsection
