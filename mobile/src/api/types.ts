export type ApiStatus = 'success' | 'error' | 'ok';

export interface ApiResponse<T = unknown> {
  remark: string;
  status: ApiStatus;
  message: Record<string, string[]>;
  data?: T;
}

export interface User {
  id: number;
  username: string;
  email: string;
  firstname?: string;
  lastname?: string;
  mobile?: string;
  deposit_wallet: number;
  interest_wallet: number;
  balance?: number;
  status: number;
  ev: number;
  sv: number;
  tv: number;
  ts: number;
  kv: number;
  profile_complete: number;
  address?: {
    country?: string;
    address?: string;
    state?: string;
    city?: string;
    zip?: string;
  };
}

export interface GeneralSetting {
  site_name: string;
  cur_text: string;
  cur_sym: string;
  base_color?: string;
  secondary_color?: string;
}

export interface DashboardData {
  user: User;
  total_invest: number;
  total_deposit: number;
  total_withdrawal: number;
  referral_earnings: number;
  pending_deposit: number;
  pending_withdraw: number;
}
