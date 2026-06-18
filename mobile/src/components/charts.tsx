import { Dimensions, StyleSheet, Text, View } from 'react-native';
import { LineChart, PieChart } from 'react-native-chart-kit';
import { colors } from '../theme/colors';
import { radius, spacing } from '../theme/spacing';

const chartWidth = Dimensions.get('window').width - spacing.lg * 2 - spacing.md * 2;

const baseChartConfig = {
  backgroundGradientFrom: colors.surface,
  backgroundGradientTo: colors.surface,
  decimalPlaces: 2,
  color: (opacity = 1) => `rgba(25, 137, 190, ${opacity})`,
  labelColor: () => colors.muted,
  propsForBackgroundLines: {
    stroke: colors.chartGrid,
    strokeDasharray: '4',
  },
  propsForDots: {
    r: '3',
    strokeWidth: '2',
    stroke: colors.white,
  },
};

type Point = { label: string; return_percent?: number; cumulative_percent?: number };

export function ReturnAnalyticsChart({ data }: { data: Point[] }) {
  const labels = data.map((d) => d.label);
  const values = data.map((d) => Number(d.cumulative_percent ?? 0));

  if (!values.length) {
    return (
      <View style={styles.empty}>
        <Text style={styles.emptyText}>Approved period payouts will appear here after admin approval</Text>
      </View>
    );
  }

  return (
    <LineChart
      data={{
        labels: labels.length > 6 ? labels.filter((_, i) => i % Math.ceil(labels.length / 6) === 0) : labels,
        datasets: [{ data: values.length ? values : [0], strokeWidth: 2 }],
      }}
      width={chartWidth}
      height={220}
      chartConfig={{
        ...baseChartConfig,
        fillShadowGradientFrom: colors.primary,
        fillShadowGradientTo: colors.surface,
        fillShadowGradientOpacity: 0.15,
      }}
      bezier
      style={styles.chart}
      withInnerLines
      withOuterLines={false}
      fromZero
      formatYLabel={(v) => `${parseFloat(v).toFixed(1)}%`}
    />
  );
}

type StrategyPoint = { rate_percent?: number; date_label?: string };

export function StrategyChart({ points }: { points: StrategyPoint[] }) {
  const values = points.map((p) => Number(p.rate_percent ?? 0));

  if (!values.length) {
    return (
      <View style={styles.empty}>
        <Text style={styles.emptyText}>No approved performance yet</Text>
      </View>
    );
  }

  return (
    <LineChart
      data={{
        labels: values.map((_, i) => (i === 0 || i === values.length - 1 ? `${i + 1}` : '')),
        datasets: [{ data: values, strokeWidth: 2 }],
      }}
      width={chartWidth}
      height={200}
      chartConfig={{
        ...baseChartConfig,
        fillShadowGradientFrom: colors.primary,
        fillShadowGradientTo: colors.surface,
        fillShadowGradientOpacity: 0.12,
      }}
      bezier
      style={styles.chart}
      withHorizontalLabels
      withVerticalLabels={false}
      withInnerLines
      formatYLabel={(v) => `${parseFloat(v).toFixed(1)}%`}
    />
  );
}

export function AllocationChart({
  labels,
  series,
  chartColors,
}: {
  labels: string[];
  series: number[];
  chartColors: string[];
}) {
  const pieData = labels.map((name, index) => ({
    name,
    population: series[index] ?? 0,
    color: chartColors[index] ?? colors.primary,
    legendFontColor: colors.body,
    legendFontSize: 11,
  }));

  return (
    <PieChart
      data={pieData}
      width={chartWidth}
      height={200}
      chartConfig={baseChartConfig}
      accessor="population"
      backgroundColor="transparent"
      paddingLeft="12"
      absolute
      hasLegend
    />
  );
}

const styles = StyleSheet.create({
  chart: {
    borderRadius: radius.sm,
    marginLeft: -8,
  },
  empty: {
    paddingVertical: spacing.xl,
    paddingHorizontal: spacing.md,
    alignItems: 'center',
  },
  emptyText: {
    color: colors.muted,
    fontSize: 13,
    textAlign: 'center',
    lineHeight: 20,
  },
});
