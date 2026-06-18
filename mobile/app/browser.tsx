import { Ionicons } from '@expo/vector-icons';
import { router, useLocalSearchParams } from 'expo-router';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { PortalWebView } from '../src/components/PortalWebView';
import { usePortal } from '../src/context/PortalContext';
import { colors } from '../src/theme/colors';
import { spacing } from '../src/theme/spacing';

export default function BrowserScreen() {
  const { path, title } = useLocalSearchParams<{ path: string; title?: string }>();
  const { setDrawerOpen } = usePortal();
  const safePath = path?.startsWith('/') ? path : '/user/dashboard';

  return (
    <SafeAreaView style={styles.safe} edges={['top', 'bottom']}>
      <View style={styles.header}>
        <Pressable style={styles.iconBtn} onPress={() => router.back()} hitSlop={12}>
          <Ionicons name="arrow-back" size={22} color={colors.ink} />
        </Pressable>
        <Text style={styles.title} numberOfLines={1}>
          {title ?? 'Portal'}
        </Text>
        <Pressable style={styles.iconBtn} onPress={() => setDrawerOpen(true)} hitSlop={12}>
          <Ionicons name="menu" size={24} color={colors.ink} />
        </Pressable>
      </View>
      <PortalWebView path={safePath} />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.pageBg },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    paddingHorizontal: spacing.md,
    paddingBottom: spacing.sm,
    backgroundColor: colors.surface,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  iconBtn: {
    width: 40,
    height: 40,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.pageBg,
  },
  title: {
    flex: 1,
    fontSize: 16,
    fontWeight: '700',
    color: colors.ink,
    textAlign: 'center',
  },
});
