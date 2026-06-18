import { Redirect } from 'expo-router';

export default function SupportRedirect() {
  return <Redirect href={{ pathname: '/browser', params: { path: '/ticket', title: 'Support Tickets' } }} />;
}
