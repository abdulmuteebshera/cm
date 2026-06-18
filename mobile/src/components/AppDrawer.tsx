import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import {
  Alert,
  Dimensions,
  Image,
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { DRAWER_SECTIONS, PortalLink } from '../config/portal';
import { useAuth } from '../context/AuthContext';
import { usePortal } from '../context/PortalContext';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

const DRAWER_WIDTH = Math.min(Dimensions.get('window').width * 0.88, 340);

const TAB_ROUTES: Record<string, string> = {
  '/user/dashboard': '/(tabs)',
  '/user/invest/statistics': '/(tabs)/invest',
};

export function AppDrawer() {
  const insets = useSafeAreaInsets();
  const { drawerOpen, setDrawerOpen, signOut } = usePortal();
  const { branding } = useAuth();

  const openPath = (link: PortalLink) => {
    setDrawerOpen(false);
    if (link.action === 'logout') {
      Alert.alert('Sign out', 'Are you sure you want to sign out?', [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Sign Out', style: 'destructive', onPress: () => signOut() },
      ]);
      return;
    }
    const tabRoute = TAB_ROUTES[link.path];
    if (tabRoute) {
      router.navigate(tabRoute as never);
      return;
    }
    router.push({ pathname: '/browser', params: { path: link.path, title: link.label } });
  };

  return (
    <Modal visible={drawerOpen} transparent animationType="fade" onRequestClose={() => setDrawerOpen(false)}>
      <View style={styles.overlay}>
        <Pressable style={styles.backdrop} onPress={() => setDrawerOpen(false)} />
        <View style={[styles.drawer, { paddingTop: insets.top + spacing.sm, width: DRAWER_WIDTH }]}>
          <View style={styles.brandRow}>
            {branding?.auth_logo ? (
              <Image source={{ uri: branding.auth_logo }} style={styles.logo} resizeMode="contain" />
            ) : (
              <Text style={styles.brandText}>{branding?.site_name ?? 'CrownMaire Capital'}</Text>
            )}
          </View>

          <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: insets.bottom + 24 }}>
            {DRAWER_SECTIONS.map((section) => (
              <View key={section.title} style={styles.section}>
                <Text style={styles.sectionTitle}>{section.title}</Text>
                {section.links.map((link) => (
                  <Pressable key={link.path + link.label} style={styles.linkRow} onPress={() => openPath(link)}>
                    <View style={styles.linkIcon}>
                      <Ionicons name={link.icon as keyof typeof Ionicons.glyphMap} size={18} color={colors.primary} />
                    </View>
                    <Text style={styles.linkLabel}>{link.label}</Text>
                    <Ionicons name="chevron-forward" size={16} color={colors.muted} />
                  </Pressable>
                ))}
              </View>
            ))}
          </ScrollView>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: { flex: 1, flexDirection: 'row' },
  backdrop: { flex: 1, backgroundColor: 'rgba(15, 23, 42, 0.45)' },
  drawer: {
    backgroundColor: colors.surface,
    borderRightWidth: 1,
    borderRightColor: colors.border,
    shadowColor: '#000',
    shadowOpacity: 0.15,
    shadowRadius: 16,
    elevation: 8,
  },
  brandRow: {
    paddingHorizontal: spacing.lg,
    paddingBottom: spacing.md,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
    marginBottom: spacing.sm,
  },
  logo: { height: 44, width: 180 },
  brandText: { fontSize: 18, fontWeight: '800', color: colors.primary },
  section: { paddingHorizontal: spacing.md, marginTop: spacing.sm },
  sectionTitle: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1,
    textTransform: 'uppercase',
    color: colors.muted,
    marginBottom: spacing.sm,
    paddingHorizontal: 4,
  },
  linkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderRadius: 10,
  },
  linkIcon: {
    width: 34,
    height: 34,
    borderRadius: 10,
    backgroundColor: colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  linkLabel: { flex: 1, fontSize: 15, fontWeight: '600', color: colors.ink },
});
