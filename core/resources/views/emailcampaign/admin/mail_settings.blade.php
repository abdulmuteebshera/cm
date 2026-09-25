@extends('emailcampaign.layouts.app_admin')
@section('panel')
<p class="ec-hint">All campaigns send through this SMTP account. Messages are paced at 10 seconds apart per campaign.</p>
<section class="crm-card" style="margin-bottom:16px">
    <h2 class="crm-card__title">Connect Gmail (Google SMTP)</h2>
    <ol class="ec-hint" style="padding-left:1.2rem;margin:0">
        <li>Use a Google account with <strong>2-Step Verification</strong> turned on.</li>
        <li>Open Google Account → <strong>Security</strong> → <strong>2-Step Verification</strong> → <strong>App passwords</strong>.</li>
        <li>Create an app password (Mail / Other → “Crownmaire Campaign”). Copy the 16-character password.</li>
        <li>Enter settings below:</li>
    </ol>
    <table class="ec-hint" style="width:100%;margin-top:12px;font-size:.85rem;border-collapse:collapse">
        <tr><td style="padding:6px 8px;color:#64748b">SMTP host</td><td><code>smtp.gmail.com</code></td></tr>
        <tr><td style="padding:6px 8px;color:#64748b">Port</td><td><code>587</code></td></tr>
        <tr><td style="padding:6px 8px;color:#64748b">Encryption</td><td><code>TLS</code></td></tr>
        <tr><td style="padding:6px 8px;color:#64748b">Username</td><td>Your full Gmail address (e.g. you@gmail.com)</td></tr>
        <tr><td style="padding:6px 8px;color:#64748b">Password</td><td>The <strong>App password</strong> (not your normal Gmail password)</td></tr>
        <tr><td style="padding:6px 8px;color:#64748b">From email</td><td>Same Gmail address (or a Google Workspace alias you’re allowed to send as)</td></tr>
    </table>
    <p class="ec-hint" style="margin-top:12px">Bulk marketing from Gmail has low daily limits (~500/day for personal Gmail). For large campaigns use Google Workspace or a transactional provider (SendGrid, Mailgun, Amazon SES).</p>
</section>
<section class="crm-card">
    <form method="post" action="{{ route('ec.admin.mail.update') }}" class="crm-form crm-form--stack">
        @csrf
        <div class="crm-form__grid">
            <label><span>From email</span><input type="email" name="from_email" value="{{ old('from_email', $settings->from_email) }}" required></label>
            <label><span>From name</span><input type="text" name="from_name" value="{{ old('from_name', $settings->from_name) }}" required></label>
            <label><span>Reply-To</span><input type="email" name="reply_to" value="{{ old('reply_to', $settings->reply_to) }}"></label>
            <label><span>SMTP host</span><input type="text" name="smtp_host" value="{{ old('smtp_host', $settings->smtp_host) }}" required></label>
            <label><span>SMTP port</span><input type="number" name="smtp_port" value="{{ old('smtp_port', $settings->smtp_port) }}" required></label>
            <label><span>Encryption</span>
                <select name="smtp_encryption">
                    @foreach(['tls','ssl','none'] as $enc)
                        <option value="{{ $enc }}" @selected(old('smtp_encryption', $settings->smtp_encryption) === $enc)>{{ strtoupper($enc) }}</option>
                    @endforeach
                </select>
            </label>
            <label><span>SMTP username</span><input type="text" name="smtp_username" value="{{ old('smtp_username', $settings->smtp_username) }}" required autocomplete="off"></label>
            <label><span>SMTP password</span><input type="password" name="smtp_password" placeholder="Leave blank to keep current" autocomplete="new-password"></label>
        </div>
        <button type="submit" class="crm-btn crm-btn--accent">Save mail settings</button>
    </form>
</section>
@endsection
