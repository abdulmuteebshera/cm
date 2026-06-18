/** Live production portal — used for Play Store builds. */
export const PRODUCTION_SITE_URL = 'https://crownmairecapital.com';
export const PRODUCTION_API_URL = 'https://crownmairecapital.com/api';

export const LEGAL_PATHS = {
  privacyPolicy: '/policy/privacy-policy/394',
  termsAndConditions: '/policy/terms-and-conditions/395',
} as const;

export function legalPageUrl(path: string, siteBase = PRODUCTION_SITE_URL) {
  return `${siteBase.replace(/\/$/, '')}${path}`;
}
