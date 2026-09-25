@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Pitch decks</h2>
        @if($staff->hasPermission('crm.pitch_decks.manage'))
            <button class="crm-btn crm-btn--accent" onclick="document.getElementById('addDeck').showModal()">Upload</button>
        @endif
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Title</th><th>Audience</th><th>File</th><th>Uploaded</th><th></th></tr></thead>
            <tbody>
                @forelse($decks as $deck)
                    <tr>
                        <td>
                            <strong>{{ $deck->title }}</strong>
                            @if($deck->description)<br><small>{{ $deck->description }}</small>@endif
                        </td>
                        <td>{{ $deck->audience }}</td>
                        <td>{{ $deck->original_name }}</td>
                        <td>{{ showDateTime($deck->created_at, 'd M Y') }}</td>
                        <td class="crm-row-actions">
                            <a class="crm-btn crm-btn--sm" href="{{ route('crm.pitch.download', $deck->id) }}">Download</a>
                            @if($staff->hasPermission('crm.pitch_decks.manage'))
                                <form method="post" action="{{ route('crm.pitch.delete', $deck->id) }}" onsubmit="return confirm('Delete this deck?')">@csrf
                                    <button class="crm-btn crm-btn--sm">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No pitch decks uploaded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($decks->hasPages())<div class="crm-pagination">{{ $decks->links() }}</div>@endif
</div>
<dialog id="addDeck" class="crm-dialog">
    <form method="post" action="{{ route('crm.pitch.store') }}" enctype="multipart/form-data">@csrf
        <h3>Upload pitch deck</h3>
        <label>Title<input name="title" required></label>
        <label>Description<textarea name="description" rows="3"></textarea></label>
        <label>Audience
            <select name="audience">
                <option value="all">All portals</option>
                <option value="agent">Agents</option>
                <option value="manager">Managers</option>
                <option value="trader">Traders</option>
                <option value="finance">Finance</option>
            </select>
        </label>
        <label>File (PDF / PPT / DOC, max 20MB)<input type="file" name="file" required></label>
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Upload</button>
        </div>
    </form>
</dialog>
@endsection
