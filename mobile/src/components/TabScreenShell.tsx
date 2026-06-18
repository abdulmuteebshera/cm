import { View, StyleSheet } from 'react-native';
import { AppHeader } from '../components/AppHeader';
import { colors } from '../theme/colors';

export function TabScreenShell({
  title,
  subtitle,
  children,
}: {
  title: string;
  subtitle?: string;
  children: React.ReactNode;
}) {
  return (
    <View style={styles.shell}>
      <AppHeader title={title} subtitle={subtitle} />
      <View style={styles.body}>{children}</View>
    </View>
  );
}

const styles = StyleSheet.create({
  shell: { flex: 1, backgroundColor: colors.pageBg },
  body: { flex: 1 },
});
