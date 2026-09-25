# cPanel deploy (keep live database)

Use this when pulling from GitHub to production. **Do not replace or import the database** — clients are actively using deposits, withdrawals, tickets, and accounts.

## 1. Pull code only

```bash
cd ~/public_html/portal   # your app root (adjust path)
git pull origin main
```

Or use cPanel **Git Version Control** → Pull.

## 2. Install PHP dependencies (if `composer.json` changed)

```bash
cd core
composer install --no-dev --optimize-autoloader
```

## 3. Safe database step — new tables only

**Do not run** `php artisan migrate` on production. That can try to alter core tables.

Run the safe command instead (creates only missing feature tables; skips if they already exist):

```bash
cd core
php artisan migrate:new-tables-only
```

This adds tables such as `job_posts`, `job_applications`, and CRM tables (`crm_staff`, `crm_roles`, etc.) when missing. It does **not** modify `deposits`, `withdrawals`, `support_tickets`, `users`, or other live data tables.

## 4. Writable upload folders

```bash
chmod -R 775 assets/files/job-resumes
chmod -R 775 assets/files/crm-pitch-decks
```

## 5. Seed CRM super admin (first CRM deploy only)

Creates CRM roles/permissions and super admin `info@crownmaire.com` (does not touch portal users/admins):

```bash
cd core
php artisan db:seed --class=CrmSystemSeeder --force
```

## 6. Clear caches

```bash
cd core
php artisan optimize:clear
```

## 7. Hard-refresh the site

Browser: Ctrl+F5. Bump CSS `?v=` in views if styles look stale.

### CRM portal URLs

| Portal | URL |
|--------|-----|
| Super Admin CRM | `/internalportal/crm` |
| Manager | `/internalportal/manager` |
| Investment Officer | `/internalportal/agent` |
| Trader | `/internalportal/trader` |
| Finance | `/internalportal/finance` |
| Live investor admin (unchanged) | `/admin` |

---

## Never do on production

- `php artisan migrate` (full — may run alter migrations)
- `php artisan migrate:fresh` / `migrate:refresh` / `db:wipe`
- Importing a local `.sql` dump over the live database
- `php artisan db:seed` unless you know exactly what it changes

## Optional: policy page content

If Privacy Policy / Terms links are empty after deploy:

```bash
cd core
php artisan db:seed --class=CrownmaireLegalPagesSeeder
```

This only updates `frontends` policy page content, not client financial data.
