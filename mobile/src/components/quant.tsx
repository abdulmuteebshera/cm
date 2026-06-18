import { Ionicons } from '@expo/vector-icons';
import { ReactNode } from 'react';
import { StyleSheet, Text, View, ViewStyle } from 'react-native';
import { colors } from '../theme/colors';
import { radius, spacing } from '../theme/spacing';

export function QuantPanel({
  title,
  desc,
  children,
  style,
  headerRight,
}: {
  title: string;
  desc?: string;
  children: ReactNode;
  style?: ViewStyle;
  headerRight?: ReactNode;
}) {
  return (
    <View style={[styles.panel, style]}>
      <View style={styles.panelHead}>
        <View style={{ flex: 1 }}>
          <Text style={styles.panelTitle}>{title}</Text>
          {desc ? <Text style={styles.panelDesc}>{desc}</Text> : null}
        </View>
        {headerRight}
      </View>
      <View style={styles.panelBody}>{children}</View>
    </View>
  );
}

export function QuantKpi({
  label,
  value,
  sub,
  icon,
  accent,
}: {
  label: string;
  value: string;
  sub?: string;
  icon: keyof typeof Ionicons.glyphMap;
  accent?: 'primary' | 'blue';
}) {
  return (
    <View style={styles.kpi}>
      <View style={[styles.kpiIcon, accent === 'primary' && styles.kpiIconPrimary]}>
        <Ionicons name={icon} size={22} color={accent === 'primary' ? colors.white : colors.primary} />
      </View>
      <View style={styles.kpiBody}>
        <Text style={styles.kpiLabel}>{label}</Text>
        <Text style={styles.kpiValue}>{value}</Text>
        {sub ? <Text style={styles.kpiSub}>{sub}</Text> : null}
      </View>
    </View>
  );
}

export function WelcomeBanner({ name }: { name: string }) {
  return (
    <View style={styles.welcome}>
      <View style={styles.welcomeAccent} />
      <View style={styles.welcomeContent}>
        <Text style={styles.welcomeText}>
          Hi <Text style={styles.welcomeName}>{name}</Text>, welcome to{' '}
          <Text style={styles.welcomeBrand}>Crownmaire Capital</Text> — one of the world&apos;s leading AI-powered quant
          funds, where technology, intelligence, and opportunity come together to help shape your financial future.
        </Text>
      </View>
    </View>
  );
}

export function YtdPanel({
  percent,
  payoutCount,
  year,
}: {
  percent: number | null;
  payoutCount?: number;
  year: number;
}) {
  const isPositive = percent == null || percent >= 0;
  return (
    <View style={styles.ytdPanel}>
      <View>
        <Text style={styles.ytdTitle}>RETURN TILL DATE</Text>
        <Text style={styles.ytdSub}>Cumulative approved return % · {year}</Text>
      </View>
      <View style={styles.ytdRight}>
        <Text style={[styles.ytdValue, !isPositive && styles.ytdLoss]}>
          {percent != null ? `${percent.toFixed(2)}%` : '—'}
        </Text>
        <Text style={styles.ytdStatus}>
          {payoutCount ? `${payoutCount} approved periods` : 'No approved payouts yet'}
        </Text>
      </View>
    </View>
  );
}

export function ActivityRow({
  title,
  meta,
  amount,
  positive,
}: {
  title: string;
  meta: string;
  amount: string;
  positive: boolean;
}) {
  return (
    <View style={styles.activityRow}>
      <View style={[styles.activityIcon, positive ? styles.activityIn : styles.activityOut]}>
        <Ionicons name={positive ? 'arrow-down' : 'arrow-up'} size={16} color={positive ? colors.profit : colors.loss} />
      </View>
      <View style={{ flex: 1 }}>
        <Text style={styles.activityTitle}>{title}</Text>
        <Text style={styles.activityMeta}>{meta}</Text>
      </View>
      <Text style={[styles.activityAmount, positive ? styles.amountIn : styles.amountOut]}>{amount}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  panel: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    marginBottom: spacing.md,
    overflow: 'hidden',
  },
  panelHead: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingHorizontal: spacing.md,
    paddingTop: spacing.md,
    paddingBottom: spacing.sm,
    gap: spacing.sm,
  },
  panelTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: colors.ink,
  },
  panelDesc: {
    fontSize: 12,
    color: colors.muted,
    marginTop: 4,
    lineHeight: 18,
  },
  panelBody: {
    paddingHorizontal: spacing.md,
    paddingBottom: spacing.md,
  },
  kpi: {
    flex: 1,
    minWidth: '46%',
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    padding: spacing.md,
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    marginBottom: spacing.sm,
  },
  kpiIcon: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  kpiIconPrimary: {
    backgroundColor: colors.primary,
  },
  kpiBody: { flex: 1, minWidth: 0 },
  kpiLabel: {
    fontSize: 10,
    fontWeight: '600',
    letterSpacing: 0.6,
    textTransform: 'uppercase',
    color: '#94a3b8',
  },
  kpiValue: {
    fontSize: 18,
    fontWeight: '700',
    color: colors.ink,
    marginTop: 2,
  },
  kpiSub: {
    fontSize: 11,
    color: colors.muted,
    marginTop: 2,
  },
  welcome: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: colors.border,
    flexDirection: 'row',
    overflow: 'hidden',
    marginBottom: spacing.md,
  },
  welcomeAccent: {
    width: 4,
    backgroundColor: colors.primary,
  },
  welcomeContent: {
    flex: 1,
    padding: spacing.md,
  },
  welcomeText: {
    fontSize: 14,
    lineHeight: 22,
    color: colors.ink,
  },
  welcomeName: {
    fontWeight: '700',
    color: colors.primary,
  },
  welcomeBrand: {
    fontWeight: '700',
    color: colors.ink,
  },
  ytdPanel: {
    backgroundColor: colors.surface,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: 'rgba(25, 137, 190, 0.2)',
    padding: spacing.md,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.md,
  },
  ytdTitle: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.5,
    color: colors.ink,
  },
  ytdSub: {
    fontSize: 11,
    color: colors.muted,
    marginTop: 2,
  },
  ytdRight: { alignItems: 'flex-end' },
  ytdValue: {
    fontSize: 24,
    fontWeight: '800',
    color: colors.profit,
  },
  ytdLoss: { color: colors.loss },
  ytdStatus: {
    fontSize: 10,
    color: colors.muted,
    marginTop: 2,
  },
  activityRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: colors.border,
  },
  activityIcon: {
    width: 36,
    height: 36,
    borderRadius: 10,
    alignItems: 'center',
    justifyContent: 'center',
  },
  activityIn: { backgroundColor: colors.profitLight },
  activityOut: { backgroundColor: colors.lossLight },
  activityTitle: { fontSize: 14, fontWeight: '600', color: colors.ink },
  activityMeta: { fontSize: 11, color: colors.muted, marginTop: 2 },
  activityAmount: { fontSize: 14, fontWeight: '700' },
  amountIn: { color: colors.profit },
  amountOut: { color: colors.loss },
});
