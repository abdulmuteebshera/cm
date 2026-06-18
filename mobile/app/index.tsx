import { Redirect } from 'expo-router';
import { LoadingScreen } from '../src/components/ui';
import { useAuth } from '../src/context/AuthContext';

export default function Index() {
  const { state, loading } = useAuth();

  if (loading) {
    return <LoadingScreen />;
  }

  if (state === 'authenticated') return <Redirect href="/(tabs)" />;
  if (state === 'verify') return <Redirect href="/(auth)/verify" />;
  if (state === 'profile') return <Redirect href="/(auth)/complete-profile" />;
  return <Redirect href="/(auth)/login" />;
}
