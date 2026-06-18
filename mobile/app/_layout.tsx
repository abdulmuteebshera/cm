import { Stack } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { StatusBar } from 'expo-status-bar';
import { useEffect } from 'react';
import { AppDrawer } from '../src/components/AppDrawer';
import { AuthProvider, useAuth } from '../src/context/AuthContext';
import { PortalProvider } from '../src/context/PortalContext';

SplashScreen.preventAutoHideAsync().catch(() => undefined);

function RootStack() {
  const { loading } = useAuth();

  useEffect(() => {
    if (!loading) SplashScreen.hideAsync().catch(() => undefined);
  }, [loading]);

  return (
    <>
      <StatusBar style="dark" />
      <Stack screenOptions={{ headerShown: false, animation: 'slide_from_right' }}>
        <Stack.Screen name="index" />
        <Stack.Screen name="(auth)" options={{ animation: 'fade' }} />
        <Stack.Screen name="(tabs)" options={{ animation: 'fade' }} />
        <Stack.Screen
          name="browser"
          options={{
            presentation: 'card',
            animation: 'slide_from_right',
          }}
        />
      </Stack>
      <AppDrawer />
    </>
  );
}

export default function RootLayout() {
  return (
    <AuthProvider>
      <PortalProvider>
        <RootStack />
      </PortalProvider>
    </AuthProvider>
  );
}
