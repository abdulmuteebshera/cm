import { getSiteBaseUrl } from '../api/client';

export function portalUrl(path: string) {
  const base = getSiteBaseUrl().replace(/\/$/, '');
  const normalized = path.startsWith('/') ? path : `/${path}`;
  return `${base}${normalized}`;
}

export type PortalLink = {
  label: string;
  path: string;
  icon: string;
  section?: string;
  action?: 'logout';
};

export const DRAWER_SECTIONS: { title: string; links: PortalLink[] }[] = [
    {
        title: 'Overview',
        links: [
            { label: 'Dashboard', path: '/user/dashboard', icon: 'grid-outline' },
            { label: 'Investments', path: '/user/invest/statistics', icon: 'trending-up-outline' },
        ],
    },
  {
    title: 'Wallet',
    links: [
      { label: 'Deposit', path: '/user/deposit', icon: 'wallet-outline' },
      { label: 'Withdraw', path: '/user/withdraw', icon: 'cash-outline' },
      { label: 'Transfer Balance', path: '/user/transfer-balance', icon: 'swap-horizontal-outline' },
      { label: 'Transactions', path: '/user/transactions', icon: 'list-outline' },
    ],
  },
  {
    title: 'Insights',
    links: [
      { label: 'Strategy Performance', path: '/user/strategy-performance', icon: 'analytics-outline' },
      { label: 'Portfolio Allocation', path: '/user/portfolio-allocation', icon: 'pie-chart-outline' },
      { label: 'Membership Tiers', path: '/user/tiers', icon: 'ribbon-outline' },
      { label: 'Leaderboard', path: '/user/leaderboard', icon: 'trophy-outline' },
      { label: 'Certificates', path: '/user/certificates', icon: 'medal-outline' },
    ],
  },
  {
    title: 'Rewards & Network',
    links: [
      { label: 'Referrals', path: '/user/referrals', icon: 'people-outline' },
    ],
  },
  {
    title: 'Updates',
    links: [
      { label: 'Announcements', path: '/user/announcements', icon: 'megaphone-outline' },
    ],
  },
  {
    title: 'Account',
    links: [
      { label: 'Support Ticket', path: '/ticket', icon: 'headset-outline' },
      { label: '2FA Security', path: '/user/twofactor', icon: 'shield-checkmark-outline' },
      { label: 'Profile', path: '/user/profile-setting', icon: 'person-outline' },
      { label: 'Change Password', path: '/user/change-password', icon: 'lock-closed-outline' },
      { label: 'Sign Out', path: '/user/logout', icon: 'log-out-outline', action: 'logout' },
    ],
  },
];

export const WALLET_LINKS: PortalLink[] = [
  { label: 'Deposit Funds', path: '/user/deposit', icon: 'add-circle-outline' },
  { label: 'Withdraw Funds', path: '/user/withdraw', icon: 'remove-circle-outline' },
  { label: 'Transfer Balance', path: '/user/transfer-balance', icon: 'swap-horizontal-outline' },
  { label: 'Transactions', path: '/user/transactions', icon: 'list-outline' },
  { label: 'Deposit History', path: '/user/deposit/history', icon: 'time-outline' },
  { label: 'Withdraw History', path: '/user/withdraw/history', icon: 'receipt-outline' },
];

export const ACCOUNT_LINKS: PortalLink[] = [
  { label: 'Membership Tiers', path: '/user/tiers', icon: 'ribbon-outline' },
  { label: 'Leaderboard', path: '/user/leaderboard', icon: 'trophy-outline' },
  { label: 'Certificates', path: '/user/certificates', icon: 'medal-outline' },
  { label: 'Portfolio Allocation', path: '/user/portfolio-allocation', icon: 'pie-chart-outline' },
  { label: 'Announcements', path: '/user/announcements', icon: 'megaphone-outline' },
  { label: 'Referrals', path: '/user/referrals', icon: 'people-outline' },
  { label: 'My Profile', path: '/user/profile-setting', icon: 'person-outline' },
  { label: 'Change Password', path: '/user/change-password', icon: 'lock-closed-outline' },
  { label: 'Two-Factor Auth', path: '/user/twofactor', icon: 'shield-checkmark-outline' },
  { label: 'Support Tickets', path: '/ticket', icon: 'headset-outline' },
  { label: 'Privacy Policy', path: '/policy/privacy-policy/394', icon: 'document-text-outline' },
  { label: 'Terms and Conditions', path: '/policy/terms-and-conditions/395', icon: 'reader-outline' },
  { label: 'Sign Out', path: '/user/logout', icon: 'log-out-outline', action: 'logout' },
];

export const TAB_DEFAULT_PATHS = {
  dashboard: '/user/dashboard',
  invest: '/user/invest/statistics',
  wallet: '/user/deposit',
  account: '/user/profile-setting',
} as const;

export function isAuthenticatedPortalUrl(url: string) {
  const lower = url.toLowerCase();
  if (
    lower.includes('/user/login') ||
    lower.includes('/user/register') ||
    lower.includes('/password/') ||
    lower.includes('/mobile-auth/')
  ) {
    return false;
  }
  return (
    lower.includes('/user/dashboard') ||
    lower.includes('/user/deposit') ||
    lower.includes('/user/withdraw') ||
    lower.includes('/user/invest') ||
    lower.includes('/user/profile') ||
    lower.includes('/user/transactions') ||
    lower.includes('/user/strategy') ||
    lower.includes('/user/referrals') ||
    lower.includes('/user/tiers') ||
    lower.includes('/user/leaderboard') ||
    lower.includes('/user/certificates') ||
    lower.includes('/user/portfolio') ||
    lower.includes('/user/announcements') ||
    lower.includes('/user/twofactor') ||
    lower.includes('/user/kyc') ||
    lower.includes('/user/authorization') ||
    lower.includes('/user/user-data') ||
    lower.includes('/ticket')
  );
}

export function isLogoutUrl(url: string) {
  return url.toLowerCase().includes('/user/logout');
}

/** Mobile portal pages inside the app shell — keep the web mobile layout, hide duplicate nav chrome. */
export function portalInjectedJavaScript() {
  return `
    (function () {
      try {
        var meta = document.querySelector('meta[name="viewport"]');
        if (!meta) {
          meta = document.createElement('meta');
          meta.setAttribute('name', 'viewport');
          document.head.appendChild(meta);
        }
        meta.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover');

        var style = document.getElementById('cm-app-shell-style');
        if (!style) {
          style = document.createElement('style');
          style.id = 'cm-app-shell-style';
          style.textContent = \`
            #dashboard-sidebar,
            .dashboard-sidebar,
            .dash-sidebar-toggler,
            .quant-topbar,
            .dash-sidebar-close,
            .dashboard__left,
            .preloader { display: none !important; }
            .dashboard-wrapper,
            .dashboard-container,
            .dashboard__right,
            .dashboard-body { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; }
            .dashboard-inner.quant-dashboard { padding: 16px 14px 28px !important; min-width: 0 !important; }
            body { background: #f7f7f7 !important; overflow-x: hidden !important; -webkit-text-size-adjust: 100%; }
          \`;
          document.head.appendChild(style);
        }
      } catch (e) {}
      true;
    })();
  `;
}
