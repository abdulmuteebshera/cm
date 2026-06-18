import { ActivityIndicator, Pressable, PressableProps, StyleSheet, Text, TextInput, TextInputProps, View, ViewStyle } from 'react-native';
import { AuthLogo } from '../context/AuthContext';
import { colors } from '../theme/colors';
import { radius, spacing } from '../theme/spacing';

export function Screen({ children, style }: { children: React.ReactNode; style?: ViewStyle }) {
  return <View style={[styles.screen, style]}>{children}</View>;
}

export function Card({ children, style }: { children: React.ReactNode; style?: ViewStyle }) {
  return <View style={[styles.card, style]}>{children}</View>;
}

export function Title({ children }: { children: React.ReactNode }) {
  return <Text style={styles.title}>{children}</Text>;
}

export function Subtitle({ children }: { children: React.ReactNode }) {
  return <Text style={styles.subtitle}>{children}</Text>;
}

export function AppInput(props: TextInputProps) {
  return <TextInput placeholderTextColor={colors.muted} style={styles.input} {...props} />;
}

export function PrimaryButton({
  title,
  loading,
  ...props
}: PressableProps & { title: string; loading?: boolean }) {
  return (
    <Pressable style={[styles.button, props.disabled && styles.buttonDisabled]} {...props}>
      {loading ? (
        <ActivityIndicator color={colors.white} />
      ) : (
        <Text style={styles.buttonText}>{title}</Text>
      )}
    </Pressable>
  );
}

export function GhostButton({ title, ...props }: PressableProps & { title: string }) {
  return (
    <Pressable style={styles.ghostButton} {...props}>
      <Text style={styles.ghostButtonText}>{title}</Text>
    </Pressable>
  );
}

export function StatTile({
  label,
  value,
  accent,
}: {
  label: string;
  value: string;
  accent?: string;
}) {
  return (
    <View style={[styles.statTile, accent ? { borderLeftColor: accent, borderLeftWidth: 3 } : null]}>
      <Text style={styles.statLabel}>{label}</Text>
      <Text style={styles.statValue}>{value}</Text>
    </View>
  );
}

export function EmptyState({ message }: { message: string }) {
  return (
    <View style={styles.empty}>
      <Text style={styles.emptyText}>{message}</Text>
    </View>
  );
}

export function LoadingScreen() {
  return (
    <View style={styles.loading}>
      <AuthLogo height={56} />
      <ActivityIndicator size="large" color={colors.primary} style={{ marginTop: spacing.lg }} />
    </View>
  );
}

const styles = StyleSheet.create({
  screen: {
    flex: 1,
    backgroundColor: colors.pageBg,
  },
  card: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    padding: spacing.md,
    marginBottom: spacing.md,
    shadowColor: '#0f172a',
    shadowOpacity: 0.06,
    shadowRadius: 12,
    shadowOffset: { width: 0, height: 4 },
    elevation: 3,
  },
  title: {
    fontSize: 28,
    fontWeight: '700',
    color: colors.ink,
    marginBottom: spacing.sm,
  },
  subtitle: {
    fontSize: 15,
    color: colors.body,
    marginBottom: spacing.lg,
    lineHeight: 22,
  },
  input: {
    backgroundColor: colors.surface2,
    borderRadius: radius.sm,
    paddingHorizontal: spacing.md,
    paddingVertical: 14,
    fontSize: 16,
    color: colors.ink,
    marginBottom: spacing.md,
    borderWidth: 1,
    borderColor: colors.line,
  },
  button: {
    backgroundColor: colors.primary,
    borderRadius: radius.sm,
    paddingVertical: 16,
    alignItems: 'center',
    marginTop: spacing.sm,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonText: {
    color: colors.white,
    fontSize: 16,
    fontWeight: '700',
  },
  ghostButton: {
    paddingVertical: spacing.md,
    alignItems: 'center',
  },
  ghostButtonText: {
    color: colors.primary,
    fontSize: 15,
    fontWeight: '600',
  },
  statTile: {
    flex: 1,
    minWidth: '46%',
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    padding: spacing.md,
    marginBottom: spacing.sm,
  },
  statLabel: {
    color: colors.muted,
    fontSize: 13,
    marginBottom: 6,
  },
  statValue: {
    color: colors.ink,
    fontSize: 18,
    fontWeight: '700',
  },
  empty: {
    padding: spacing.xl,
    alignItems: 'center',
  },
  emptyText: {
    color: colors.muted,
    fontSize: 15,
    textAlign: 'center',
  },
  loading: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.authBg,
  },
});
