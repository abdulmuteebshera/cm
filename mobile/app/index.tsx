import { Redirect } from 'expo-router';
import { LoadingScreen } from '../src/components/ui';
import { useAuth } from '../src/context/AuthContext';
import { session } from '../src/state/session';

export default function Index() {
  const { state, loading } = useAuth();

  if (loading) {
    return <LoadingScreen />;
  }

  if (state === 'authenticated') {
    if (!session.introShown) return <Redirect href="/intro" />;
    return <Redirect href="/(tabs)" />;
  }
  if (state === 'verify') return <Redirect href="/(auth)/verify" />;
  if (state === 'profile') return <Redirect href="/(auth)/complete-profile" />;
  return <Redirect href="/(auth)/login" />;
}
