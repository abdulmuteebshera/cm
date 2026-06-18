import { Redirect, useLocalSearchParams } from 'expo-router';

export default function DepositRedirect() {
  const { path } = useLocalSearchParams<{ path?: string }>();
  return (
    <Redirect
      href={{
        pathname: '/browser',
        params: { path: path ?? '/user/deposit', title: 'Deposit' },
      }}
    />
  );
}
