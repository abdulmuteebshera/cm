import { TAB_DEFAULT_PATHS } from '../../src/config/portal';
import { PortalWebView } from '../../src/components/PortalWebView';
import { TabScreenShell } from '../../src/components/TabScreenShell';

export default function DashboardTab() {
  return (
    <TabScreenShell title="Portfolio Dashboard" subtitle="Client Portal">
      <PortalWebView path={TAB_DEFAULT_PATHS.dashboard} />
    </TabScreenShell>
  );
}
