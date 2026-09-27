@php
    $operator = '—';
    if ($campaign->relationLoaded('admin') && $campaign->admin) {
        $operator = 'Admin: ' . $campaign->admin->name . ' (' . $campaign->admin->username . ')';
    } elseif ($campaign->relationLoaded('user') && $campaign->user) {
        $operator = 'User: ' . $campaign->user->name . ' (' . $campaign->user->username . ')';
    } elseif ($campaign->ec_admin_id) {
        $operator = 'Admin (ID ' . $campaign->ec_admin_id . ')';
    } elseif ($campaign->ec_user_id) {
        $operator = 'User (ID ' . $campaign->ec_user_id . ')';
    }
@endphp
{{ $operator }}
