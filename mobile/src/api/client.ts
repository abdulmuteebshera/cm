import axios, { AxiosError } from 'axios';
import Constants from 'expo-constants';
import * as SecureStore from 'expo-secure-store';
import { ApiResponse } from './types';

const TOKEN_KEY = 'crownmaire_token';

export const getApiBaseUrl = () =>
  Constants.expoConfig?.extra?.apiUrl ?? 'http://10.0.2.2/portal/portal/api';

export const getSiteBaseUrl = () =>
  Constants.expoConfig?.extra?.siteUrl ?? 'http://10.0.2.2/portal/portal';

export const api = axios.create({
  baseURL: getApiBaseUrl(),
  timeout: 30000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(async (config) => {
  const token = await SecureStore.getItemAsync(TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export async function saveToken(token: string) {
  await SecureStore.setItemAsync(TOKEN_KEY, token);
}

export async function clearToken() {
  await SecureStore.deleteItemAsync(TOKEN_KEY);
}

export async function getToken() {
  return SecureStore.getItemAsync(TOKEN_KEY);
}

export function extractErrors(error: unknown): string[] {
  if (axios.isAxiosError(error)) {
    const data = error.response?.data as ApiResponse | undefined;
    if (data?.message) {
      const values = Object.values(data.message).flat();
      if (values.length) return values;
    }
    if (error.message) return [error.message];
  }
  return ['Something went wrong. Please try again.'];
}

export async function apiGet<T>(url: string, params?: Record<string, unknown>) {
  const { data } = await api.get<ApiResponse<T>>(url, { params });
  return data;
}

export async function apiPost<T>(url: string, body?: Record<string, unknown>) {
  const { data } = await api.post<ApiResponse<T>>(url, body);
  return data;
}

export type { AxiosError };
