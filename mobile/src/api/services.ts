import { apiGet, apiPost } from './client';
import { ApiResponse, DashboardData, GeneralSetting, User } from './types';

export const authService = {
  login: (username: string, password: string) =>
    apiPost<{ user: User; access_token: string; token_type: string }>('/login', {
      username,
      password,
    }),
  register: (payload: Record<string, unknown>) =>
    apiPost<{ user: User; access_token: string; token_type: string }>('/register', payload),
  logout: () => apiGet('/logout'),
  authorization: () => apiGet('/authorization'),
  verifyEmail: (code: string) => apiPost('/verify-email', { code }),
  verifyMobile: (code: string) => apiPost('/verify-mobile', { code }),
  verifyG2fa: (code: string) => apiPost('/verify-g2fa', { code }),
  resendVerify: (type: string) => apiGet(`/resend-verify/${type}`),
  completeProfile: (payload: Record<string, unknown>) => apiPost('/user-data-submit', payload),
};

export const userService = {
  dashboard: () => apiGet<DashboardData>('/dashboard'),
  userInfo: () => apiGet<{ user: User }>('/user-info'),
  transactions: (page?: number) => apiGet('/transactions', page ? { page } : undefined),
  depositHistory: () => apiGet('/deposit/history'),
  withdrawHistory: () => apiGet('/withdraw/history'),
  referrals: () => apiGet('/my-referrals'),
  changePassword: (payload: Record<string, unknown>) => apiPost('/change-password', payload),
  saveDeviceToken: (token: string) => apiPost('/save/device/token', { token }),
};

export const investService = {
  plans: () => apiGet('/invest/plans'),
  myInvests: (type?: 'active' | 'closed') => apiGet('/invest/', type ? { type } : undefined),
  store: (payload: Record<string, unknown>) => apiPost('/invest/store', payload),
};

export const paymentService = {
  depositMethods: () => apiGet('/deposit/methods'),
  depositInsert: (payload: Record<string, unknown>) => apiPost('/deposit/insert', payload),
  withdrawMethods: () => apiGet('/withdraw-method'),
  withdrawRequest: (payload: Record<string, unknown>) => apiPost('/withdraw-request', payload),
  withdrawConfirm: (payload: Record<string, unknown>) =>
    apiPost('/withdraw-request/confirm', payload),
};

export const strategyService = {
  analytics: () => apiGet('/strategy/analytics'),
  performance: (planId?: number) =>
    apiGet('/strategy/performance', planId ? { plan_id: planId } : undefined),
};

export const ticketService = {
  list: () => apiGet('/tickets'),
  show: (ticketNo: string) => apiGet(`/tickets/${ticketNo}`),
  create: (payload: Record<string, unknown>) => apiPost('/tickets', payload),
  reply: (id: number, message: string) => apiPost(`/tickets/${id}/reply`, { message }),
  close: (id: number) => apiPost(`/tickets/${id}/close`),
};

export const publicService = {
  generalSetting: () => apiGet<{ general_setting: GeneralSetting }>('/general-setting'),
  branding: () => apiGet('/branding'),
  countries: () => apiGet('/get-countries'),
  logoFavicon: () => apiGet('/logo-favicon'),
};

export const portalService = {
  webSession: (redirect: string) =>
    apiGet<{ url: string; path: string }>('/mobile/web-session', { redirect }),
};

export function getMessage(response: ApiResponse): string {
  const values = Object.values(response.message ?? {}).flat();
  return values[0] ?? '';
}
