<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Campaign — setup required</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 40px 20px; }
        .box { max-width: 640px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 28px; border: 1px solid #334155; }
        h1 { font-size: 1.25rem; margin: 0 0 12px; }
        p { line-height: 1.6; color: #94a3b8; }
        code, pre { background: #0f172a; padding: 12px; border-radius: 8px; display: block; overflow-x: auto; font-size: 0.9rem; color: #f8fafc; margin: 16px 0; }
        strong { color: #f8fafc; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Email campaign database not installed</h1>
        <p>This module uses <strong>new tables only</strong> (<code>ec_*</code>). Your live investors, deposits, and withdrawals are not changed.</p>
        <p>On cPanel, open <strong>Terminal</strong> (or SSH) and run:</p>
        <pre>cd core
php artisan ec:setup-live --seed
php artisan optimize:clear</pre>
        <p>Or run the full safe portal command (includes CRM and other feature tables if missing):</p>
        <pre>cd core
php artisan migrate:new-tables-only
php artisan db:seed --class=EcSystemSeeder --force
php artisan optimize:clear</pre>
        <p>Then reload <strong>/emailcampaign/admin</strong>.</p>
    </div>
</body>
</html>
