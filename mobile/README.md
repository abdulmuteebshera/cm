# CrownMaire Mobile App

Native Android app connected to the same Laravel portal API as the web dashboard. Any change you make in the admin panel or user portal is reflected instantly in the app because both clients read from the same backend.

## Architecture

```
Mobile App (Expo/React Native)
        │
        │  HTTPS + Bearer Token (Laravel Sanctum)
        ▼
Portal API  →  /api/*
        │
        ▼
Same MySQL database as web portal
```

## Features

- Login / Register / Email-SMS-2FA verification
- Dashboard with wallets, stats, and YTD strategy return
- Investment plans and active investments
- Deposits (WebView for payment gateways)
- Withdrawals
- Transactions, referrals, support tickets
- Strategy performance (synced with web portal)

## Setup

### 1. Configure API URL

Copy `.env.example` to `.env` and set your portal URL:

**Android Emulator (XAMPP on same PC):**
```
EXPO_PUBLIC_API_URL=http://10.0.2.2/portal/portal/api
EXPO_PUBLIC_SITE_URL=http://10.0.2.2/portal/portal
```

**Physical Android phone (same Wi‑Fi as your PC):**
```
EXPO_PUBLIC_API_URL=http://YOUR_PC_IP/portal/portal/api
EXPO_PUBLIC_SITE_URL=http://YOUR_PC_IP/portal/portal
```

Replace `YOUR_PC_IP` with your LAN IP (run `ipconfig` on Windows).

**Production (live server):**
```
EXPO_PUBLIC_API_URL=https://yourdomain.com/api
EXPO_PUBLIC_SITE_URL=https://yourdomain.com
```

### 2. Install & run

```bash
cd mobile
npm install
npm start
```

Scan the QR code with **Expo Go** on your phone, or press `a` for Android emulator.

### 3. Build APK for testing

Install EAS CLI and log in to your Expo account:

```bash
npm install -g eas-cli
eas login
eas build -p android --profile preview
```

This produces a downloadable **APK** you can sideload on your phone.

### 4. Publish to Google Play Store

When ready after APK testing:

```bash
eas build -p android --profile production
eas submit -p android
```

Use your existing Play Console developer account. Update `android.package` in `app.config.ts` if needed (`com.crownmaire.portal`).

## Backend API additions

These endpoints were added for mobile parity with the web portal:

| Endpoint | Purpose |
|----------|---------|
| `GET /api/strategy/analytics` | Dashboard charts & YTD return |
| `GET /api/strategy/performance?plan_id=` | Strategy performance by plan |
| `GET /api/strategy/report/{planId}/{year}` | PDF strategy report |
| `GET/POST /api/tickets/*` | Support tickets |

## Play Store checklist

- [ ] Point API URL to production HTTPS domain
- [ ] Replace app icon/splash with final CrownMaire branding assets
- [ ] Add Privacy Policy URL in Play Console (use portal policy page)
- [ ] Test deposits/withdrawals on production
- [ ] Configure Firebase push notifications in admin panel (optional)
- [ ] Upload AAB via `eas submit` or Play Console manually

## Notes

- The app uses **Laravel Sanctum** tokens stored in the device secure store.
- Payment gateways open in an in-app **WebView** (same flow as existing mobile API).
- Keep XAMPP Apache running and allow firewall access when testing on a physical device.
