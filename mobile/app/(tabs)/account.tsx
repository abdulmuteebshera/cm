import { ACCOUNT_LINKS } from '../../src/config/portal';
import { PortalHub } from '../../src/components/PortalHub';
import { TabScreenShell } from '../../src/components/TabScreenShell';

export default function AccountTab() {
  return (
    <TabScreenShell title="Account" subtitle="Profile & Security">
      <PortalHub
        title="Account Center"
        subtitle="Manage profile, security, KYC, referrals, and support — every option from the web portal."
        links={ACCOUNT_LINKS}
      />
    </TabScreenShell>
  );
}
