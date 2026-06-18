import * as WebBrowser from 'expo-web-browser';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export function LegalLinks({
  privacyUrl,
  termsUrl,
  prefix = 'By continuing, you agree to our',
}: {
  privacyUrl?: string | null;
  termsUrl?: string | null;
  prefix?: string;
}) {
  if (!privacyUrl && !termsUrl) return null;

  const open = (url: string) => {
    WebBrowser.openBrowserAsync(url).catch(() => undefined);
  };

  return (
    <View style={styles.wrap}>
      <Text style={styles.prefix}>{prefix}</Text>
      <View style={styles.row}>
        {privacyUrl ? (
          <Pressable onPress={() => open(privacyUrl)}>
            <Text style={styles.link}>Privacy Policy</Text>
          </Pressable>
        ) : null}
        {privacyUrl && termsUrl ? <Text style={styles.sep}> and </Text> : null}
        {termsUrl ? (
          <Pressable onPress={() => open(termsUrl)}>
            <Text style={styles.link}>Terms and Conditions</Text>
          </Pressable>
        ) : null}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginTop: spacing.md },
  prefix: { fontSize: 13, color: colors.body, lineHeight: 20, textAlign: 'center' },
  row: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'center', marginTop: 4 },
  link: { fontSize: 13, color: colors.primary, fontWeight: '700' },
  sep: { fontSize: 13, color: colors.body },
});
