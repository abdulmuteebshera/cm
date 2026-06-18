# Google Play Store — CrownMaire Mobile

## Production URLs (configured)

| Setting | URL |
|---------|-----|
| Site | `https://crownmairecapital.com` |
| API | `https://crownmairecapital.com/api` |
| Privacy Policy | `https://crownmairecapital.com/policy/privacy-policy/394` |
| Terms and Conditions | `https://crownmairecapital.com/policy/terms-and-conditions/395` |

Set in `mobile/.env` before building:

```
EXPO_PUBLIC_SITE_URL=https://crownmairecapital.com
EXPO_PUBLIC_API_URL=https://crownmairecapital.com/api
```

> If your portal is in a subfolder (e.g. `/portal`), append that path to both URLs.

## Legal pages (server)

Privacy Policy and Terms were updated in the database via:

```bash
cd core
php artisan db:seed --class=CrownmaireLegalPagesSeeder
```

**Run the same command on your live cPanel server** after deployment.

Ensure production `core/.env` includes:

```
APP_URL=https://crownmairecapital.com
```

## Play Store checklist

### 1. Accounts and fees
- [ ] Google Play Developer account ($25 one-time)
- [ ] Expo account (`eas login`)

### 2. Production backend (cPanel)
- [ ] Portal live at HTTPS with valid SSL
- [ ] `APP_URL=https://crownmairecapital.com` in `core/.env`
- [ ] Legal seeder run on production DB
- [ ] Test `https://crownmairecapital.com/user/login` in phone browser
- [ ] Test `https://crownmairecapital.com/api/branding` returns JSON

### 3. App build
```bash
cd mobile
npm install
eas build -p android --profile production
eas submit -p android --profile production
```

### 4. Store listing (Play Console)
- [ ] App name: **CrownMaire**
- [ ] Short description (80 chars)
- [ ] Full description
- [ ] App icon 512×512
- [ ] Feature graphic 1024×500
- [ ] Phone screenshots (min 2)
- [ ] **Privacy policy URL**: `https://crownmairecapital.com/policy/privacy-policy/394`
- [ ] Contact email: `Info@crownmaire.com`
- [ ] Category: Finance

### 5. App content declarations
- [ ] Data safety form
- [ ] Target audience 18+
- [ ] Content rating questionnaire

### 6. Release
- [ ] Upload `.aab` from EAS production build
- [ ] Internal testing → production
- [ ] First review: typically **3–7 days**

## After launch

| Change type | App update needed? |
|-------------|-------------------|
| Web portal pages (WebView) | No |
| Privacy/terms on website | No |
| Store listing text/screenshots | No |
| Native app code | Yes — new EAS build |

Bump `version` in `app.config.js` for each Play Store upload.
