import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import { Alert, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { PortalLink } from '../config/portal';
import { usePortal } from '../context/PortalContext';
import { colors } from '../theme/colors';
import { radius, spacing } from '../theme/spacing';

export function PortalHub({
  title,
  subtitle,
  links,
}: {
  title: string;
  subtitle: string;
  links: PortalLink[];
}) {
  const { signOut } = usePortal();

  const openLink = (link: PortalLink) => {
    if (link.action === 'logout') {
      Alert.alert('Sign out', 'Are you sure you want to sign out?', [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Sign Out', style: 'destructive', onPress: () => signOut() },
      ]);
      return;
    }
    router.push({
      pathname: '/browser',
      params: { path: link.path, title: link.label },
    });
  };

  return (
    <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>
      <View style={styles.hero}>
        <Text style={styles.heroTitle}>{title}</Text>
        <Text style={styles.heroSub}>{subtitle}</Text>
      </View>
      <View style={styles.grid}>
        {links.map((link) => (
          <Pressable key={link.path + link.label} style={styles.card} onPress={() => openLink(link)}>
            <View style={styles.iconWrap}>
              <Ionicons name={link.icon as keyof typeof Ionicons.glyphMap} size={24} color={colors.primary} />
            </View>
            <Text style={styles.cardLabel}>{link.label}</Text>
            <Ionicons name="chevron-forward" size={16} color={colors.muted} style={styles.chevron} />
          </Pressable>
        ))}
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  scroll: {
    padding: spacing.lg,
    paddingBottom: spacing.xxl,
  },
  hero: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    padding: spacing.lg,
    marginBottom: spacing.lg,
    borderLeftWidth: 4,
    borderLeftColor: colors.primary,
  },
  heroTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: colors.ink,
  },
  heroSub: {
    marginTop: 6,
    fontSize: 14,
    lineHeight: 21,
    color: colors.body,
  },
  grid: { gap: spacing.sm },
  card: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    padding: spacing.md,
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.md,
  },
  iconWrap: {
    width: 48,
    height: 48,
    borderRadius: 14,
    backgroundColor: colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  cardLabel: {
    flex: 1,
    fontSize: 16,
    fontWeight: '700',
    color: colors.ink,
  },
  chevron: { marginRight: 4 },
});
