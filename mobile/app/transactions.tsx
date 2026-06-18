import { Redirect } from 'expo-router';

export default function TransactionsRedirect() {
  return (
    <Redirect href={{ pathname: '/browser', params: { path: '/user/transactions', title: 'Transactions' } }} />
  );
}
