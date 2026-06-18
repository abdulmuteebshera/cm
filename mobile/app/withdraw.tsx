import { Redirect } from 'expo-router';

export default function WithdrawRedirect() {
  return <Redirect href={{ pathname: '/browser', params: { path: '/user/withdraw', title: 'Withdraw' } }} />;
}
