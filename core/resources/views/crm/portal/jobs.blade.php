@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--2">
    <div class="crm-card">
        <h2>Job posts</h2>
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead><tr><th>Title</th><th>Status</th><th>Applicants</th></tr></thead>
                <tbody>
                    @forelse($jobPosts as $j)
                        <tr>
                            <td>{{ $j->title }}</td>
                            <td>{{ $j->status ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $j->applications_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No job posts.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="crm-card">
        <h2>Applications</h2>
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead><tr><th>Name</th><th>Job</th><th>Email</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($applications as $a)
                        <tr>
                            <td>{{ $a->name }}</td>
                            <td>{{ optional($a->jobPost)->title }}</td>
                            <td>{{ $a->email }}</td>
                            <td>{{ showDateTime($a->created_at, 'd M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No applications.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(is_object($applications) && method_exists($applications,'hasPages') && $applications->hasPages())
            <div class="crm-pagination">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
