import { Redirect, useLocalSearchParams } from 'expo-router';

export default function SupportTicketRedirect() {
  const { ticketNo } = useLocalSearchParams<{ ticketNo: string }>();
  return (
    <Redirect
      href={{
        pathname: '/browser',
        params: { path: `/ticket/view/${ticketNo}`, title: 'Support Ticket' },
      }}
    />
  );
}
