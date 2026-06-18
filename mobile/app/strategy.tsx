import { Redirect } from 'expo-router';

export default function StrategyRedirect() {
  return (
    <Redirect
      href={{ pathname: '/browser', params: { path: '/user/strategy-performance', title: 'Strategy Performance' } }}
    />
  );
}
