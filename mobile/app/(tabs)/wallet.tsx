import { WALLET_LINKS } from '../../src/config/portal';
import { PortalHub } from '../../src/components/PortalHub';
import { TabScreenShell } from '../../src/components/TabScreenShell';

export default function WalletTab() {
  return (
    <TabScreenShell title="Wallet" subtitle="Funds & Transfers">
      <PortalHub
        title="Wallet Services"
        subtitle="Access deposits, withdrawals, transfers, and full transaction history — identical to the web client portal."
        links={WALLET_LINKS}
      />
    </TabScreenShell>
  );
}
