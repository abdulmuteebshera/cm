import { Redirect } from 'expo-router';

export default function ReferralsRedirect() {
  return <Redirect href={{ pathname: '/browser', params: { path: '/user/referrals', title: 'Referrals' } }} />;
}
