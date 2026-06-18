import { TAB_DEFAULT_PATHS } from '../../src/config/portal';
import { PortalWebView } from '../../src/components/PortalWebView';
import { TabScreenShell } from '../../src/components/TabScreenShell';

export default function InvestTab() {
  return (
    <TabScreenShell title="Investments" subtitle="Strategy & Capital">
      <PortalWebView path={TAB_DEFAULT_PATHS.invest} />
    </TabScreenShell>
  );
}
