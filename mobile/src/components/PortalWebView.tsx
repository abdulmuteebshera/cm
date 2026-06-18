import { forwardRef, useEffect, useImperativeHandle, useRef, useState } from 'react';
import { ActivityIndicator, StyleSheet, View } from 'react-native';
import { WebView, WebViewNavigation } from 'react-native-webview';
import { isAuthenticatedPortalUrl, portalInjectedJavaScript } from '../config/portal';
import { portalUrl, usePortal } from '../context/PortalContext';
import { colors } from '../theme/colors';

export type PortalWebViewRef = {
  reload: () => void;
  goBack: () => void;
};

type Props = {
  path: string;
  onUrlChange?: (url: string) => void;
};

export const PortalWebView = forwardRef<PortalWebViewRef, Props>(function PortalWebView(
  { path, onUrlChange },
  ref
) {
  const webRef = useRef<WebView>(null);
  const [loading, setLoading] = useState(true);
  const [sourceUri, setSourceUri] = useState<string | null>(null);
  const { webSessionReady, fetchBridgeUrl, onWebNavigation } = usePortal();

  useImperativeHandle(ref, () => ({
    reload: () => webRef.current?.reload(),
    goBack: () => webRef.current?.goBack(),
  }));

  useEffect(() => {
    let active = true;

    async function load() {
      setLoading(true);
      try {
        if (webSessionReady) {
          if (active) setSourceUri(portalUrl(path));
          return;
        }
        const bridge = await fetchBridgeUrl(path);
        if (active) setSourceUri(bridge);
      } catch {
        if (active) setSourceUri(portalUrl(path));
      }
    }

    load();
    return () => {
      active = false;
    };
  }, [path, webSessionReady, fetchBridgeUrl]);

  const handleNav = (nav: WebViewNavigation) => {
    onUrlChange?.(nav.url);
    onWebNavigation(nav.url);

    if (!webSessionReady && isAuthenticatedPortalUrl(nav.url) && nav.url !== sourceUri) {
      setSourceUri(portalUrl(path));
    }
  };

  if (!sourceUri) {
    return (
      <View style={styles.loaderOnly}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <View style={styles.wrap}>
      <WebView
        ref={webRef}
        source={{ uri: sourceUri }}
        injectedJavaScriptBeforeContentLoaded={portalInjectedJavaScript()}
        injectedJavaScript={portalInjectedJavaScript()}
        onLoadStart={() => setLoading(true)}
        onLoadEnd={() => setLoading(false)}
        onNavigationStateChange={handleNav}
        sharedCookiesEnabled
        thirdPartyCookiesEnabled
        domStorageEnabled
        javaScriptEnabled
        setSupportMultipleWindows={false}
        allowsBackForwardNavigationGestures
        mixedContentMode="always"
        originWhitelist={['*']}
        style={styles.web}
      />
      {loading && (
        <View style={styles.loader}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      )}
    </View>
  );
});

const styles = StyleSheet.create({
  wrap: { flex: 1, backgroundColor: colors.pageBg },
  web: { flex: 1, backgroundColor: colors.pageBg },
  loaderOnly: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.pageBg,
  },
  loader: {
    ...StyleSheet.absoluteFill,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'rgba(247,247,247,0.85)',
  },
});
