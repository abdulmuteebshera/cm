import { Redirect, useLocalSearchParams } from 'expo-router';

export default function WebViewRedirect() {
  const { path, title } = useLocalSearchParams<{ path?: string; title?: string }>();
  return (
    <Redirect
      href={{
        pathname: '/browser',
        params: { path: path ?? '/user/dashboard', title: title ?? 'Portal' },
      }}
    />
  );
}
