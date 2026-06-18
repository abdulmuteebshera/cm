import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { usePortal } from '../context/PortalContext';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export function AppHeader({ title, subtitle }: { title: string; subtitle?: string }) {
  const insets = useSafeAreaInsets();
  const { setDrawerOpen } = usePortal();

  return (
    <View style={[styles.header, { paddingTop: insets.top + spacing.sm }]}>
      <View style={styles.titles}>
        <Text style={styles.subtitle}>{subtitle ?? 'Client Portal'}</Text>
        <Text style={styles.title} numberOfLines={1}>
          {title}
        </Text>
      </View>
      <Pressable style={styles.menuBtn} onPress={() => setDrawerOpen(true)} hitSlop={12}>
        <Ionicons name="menu" size={24} color={colors.ink} />
      </Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
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
  menuBtn: {
    width: 42,
    height: 42,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.pageBg,
  },
  titles: { flex: 1 },
  subtitle: {
    fontSize: 11,
    fontWeight: '600',
    color: colors.primary,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  title: {
    fontSize: 17,
    fontWeight: '700',
    color: colors.ink,
    marginTop: 2,
  },
});
