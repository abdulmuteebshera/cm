import { router } from 'expo-router';
import React, { createContext, useCallback, useContext, useMemo, useState } from 'react';
import { portalService } from '../api/services';
import { isAuthenticatedPortalUrl, isLogoutUrl, portalUrl } from '../config/portal';
import { useAuth } from './AuthContext';

interface PortalContextValue {
  drawerOpen: boolean;
  webSessionReady: boolean;
  setDrawerOpen: (open: boolean) => void;
  fetchBridgeUrl: (path: string) => Promise<string>;
  markWebSessionReady: () => void;
  onWebNavigation: (url: string) => Promise<void>;
  resetWebSession: () => void;
  signOut: () => Promise<void>;
}

const PortalContext = createContext<PortalContextValue | null>(null);

export function PortalProvider({ children }: { children: React.ReactNode }) {
  const { logout } = useAuth();
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [webSessionReady, setWebSessionReady] = useState(false);

  const resetWebSession = useCallback(() => {
    setWebSessionReady(false);
  }, []);

  const markWebSessionReady = useCallback(() => {
    setWebSessionReady(true);
  }, []);

  const fetchBridgeUrl = useCallback(async (path: string) => {
    const res = await portalService.webSession(path);
    if (res.status === 'success' && res.data?.url) {
      return String(res.data.url);
    }
    throw new Error('Could not start portal session');
  }, []);

  const signOut = useCallback(async () => {
    resetWebSession();
    await logout();
    router.replace('/(auth)/login');
  }, [logout, resetWebSession]);

  const onWebNavigation = useCallback(
    async (url: string) => {
      if (isLogoutUrl(url)) {
        await signOut();
        return;
      }
      if (isAuthenticatedPortalUrl(url)) {
        setWebSessionReady(true);
      }
    },
    [signOut]
  );

  const value = useMemo(
    () => ({
      drawerOpen,
      webSessionReady,
      setDrawerOpen,
      fetchBridgeUrl,
      markWebSessionReady,
      onWebNavigation,
      resetWebSession,
      signOut,
    }),
    [
      drawerOpen,
      webSessionReady,
      fetchBridgeUrl,
      markWebSessionReady,
      onWebNavigation,
      resetWebSession,
      signOut,
    ]
  );

  return <PortalContext.Provider value={value}>{children}</PortalContext.Provider>;
}

export function usePortal() {
  const ctx = useContext(PortalContext);
  if (!ctx) throw new Error('usePortal must be used within PortalProvider');
  return ctx;
}

export { portalUrl };
