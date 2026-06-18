import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { Image } from 'react-native';
import { clearToken, extractErrors, getToken, saveToken } from '../api/client';
import { authService, getMessage, publicService, userService } from '../api/services';
import { ApiResponse, GeneralSetting, User } from '../api/types';

type AuthState = 'loading' | 'guest' | 'verify' | 'profile' | 'authenticated';

export interface Branding {
  site_name: string;
  cur_text: string;
  cur_sym: string;
  auth_logo?: string;
  logo?: string;
  login_title?: string;
  login_subtitle?: string;
  privacy_policy_url?: string;
  terms_url?: string;
}

interface AuthContextValue {
  state: AuthState;
  user: User | null;
  settings: GeneralSetting | null;
  branding: Branding | null;
  loading: boolean;
  login: (username: string, password: string) => Promise<void>;
  register: (payload: Record<string, unknown>) => Promise<void>;
  logout: () => Promise<void>;
  refreshUser: () => Promise<void>;
  completeProfile: (payload: Record<string, unknown>) => Promise<void>;
  verifyCode: (type: 'email' | 'sms' | '2fa', code: string) => Promise<void>;
  resendCode: (type: string) => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

function resolveState(user: User | null): AuthState {
  if (!user) return 'guest';
  if (!user.status || !user.ev || !user.sv || !user.tv) return 'verify';
  if (user.profile_complete !== 1) return 'profile';
  return 'authenticated';
}

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [settings, setSettings] = useState<GeneralSetting | null>(null);
  const [branding, setBranding] = useState<Branding | null>(null);
  const [loading, setLoading] = useState(true);

  const refreshUser = useCallback(async () => {
    const token = await getToken();
    if (!token) {
      setUser(null);
      return;
    }
    const response = await userService.userInfo();
    if (response.status === 'success' && response.data?.user) {
      setUser(response.data.user);
    }
  }, []);

  const bootstrap = useCallback(async () => {
    try {
      const [settingRes, brandingRes] = await Promise.all([
        publicService.generalSetting(),
        publicService.branding(),
        refreshUser(),
      ]);
      if (settingRes.status === 'success' && settingRes.data?.general_setting) {
        setSettings(settingRes.data.general_setting);
      }
      if (brandingRes.status === 'success' && brandingRes.data) {
        setBranding(brandingRes.data as Branding);
      }
    } finally {
      setLoading(false);
    }
  }, [refreshUser]);

  useEffect(() => {
    bootstrap();
  }, [bootstrap]);

  const login = useCallback(async (username: string, password: string) => {
    const response = await authService.login(username, password);
    if (response.status !== 'success' || !response.data) {
      throw new Error(getMessage(response) || 'Login failed');
    }
    await saveToken(response.data.access_token);
    setUser(response.data.user);
  }, []);

  const register = useCallback(async (payload: Record<string, unknown>) => {
    const response = await authService.register(payload);
    if (response.status !== 'success' || !response.data) {
      throw new Error(getMessage(response) || 'Registration failed');
    }
    await saveToken(response.data.access_token);
    setUser(response.data.user);
  }, []);

  const logout = useCallback(async () => {
    try {
      await authService.logout();
    } catch {
      // ignore
    }
    await clearToken();
    setUser(null);
  }, []);

  const completeProfile = useCallback(
    async (payload: Record<string, unknown>) => {
      const response = await authService.completeProfile(payload);
      if (response.status !== 'success') {
        throw new Error(getMessage(response) || 'Could not save profile');
      }
      await refreshUser();
    },
    [refreshUser]
  );

  const verifyCode = useCallback(
    async (type: 'email' | 'sms' | '2fa', code: string) => {
      let response: ApiResponse;
      if (type === 'email') response = await authService.verifyEmail(code);
      else if (type === 'sms') response = await authService.verifyMobile(code);
      else response = await authService.verifyG2fa(code);

      if (response.status !== 'success') {
        throw new Error(getMessage(response) || 'Verification failed');
      }
      await refreshUser();
    },
    [refreshUser]
  );

  const resendCode = useCallback(async (type: string) => {
    const response = await authService.resendVerify(type);
    if (response.status !== 'success') {
      throw new Error(getMessage(response) || 'Could not resend code');
    }
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      state: loading ? 'loading' : resolveState(user),
      user,
      settings,
      branding,
      loading,
      login,
      register,
      logout,
      refreshUser,
      completeProfile,
      verifyCode,
      resendCode,
    }),
    [user, settings, branding, loading, login, register, logout, refreshUser, completeProfile, verifyCode, resendCode]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function AuthLogo({ height = 48 }: { height?: number }) {
  const { branding } = useAuth();
  if (!branding?.auth_logo) return null;
  return (
    <Image
      source={{ uri: branding.auth_logo }}
      style={{ height, width: 180, alignSelf: 'center' }}
      resizeMode="contain"
    />
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}

export function useFormatMoney() {
  const { settings, branding } = useAuth();
  return useCallback(
    (amount: number | string | undefined, withSymbol = true) => {
      const value = Number(amount ?? 0);
      const sym = settings?.cur_sym ?? branding?.cur_sym ?? '$';
      const text = value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      return withSymbol ? `${sym}${text}` : text;
    },
    [settings, branding]
  );
}

export function useApiError() {
  return useCallback((error: unknown) => extractErrors(error).join('\n'), []);
}
