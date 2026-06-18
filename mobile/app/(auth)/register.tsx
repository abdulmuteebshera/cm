import { router } from 'expo-router';
import { useEffect, useState } from 'react';
import { Alert, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { publicService } from '../../src/api/services';
import { AppInput, PrimaryButton, Subtitle, Title } from '../../src/components/ui';
import { LegalLinks } from '../../src/components/LegalLinks';
import { AuthLogo, useApiError, useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { spacing } from '../../src/theme/spacing';

export default function RegisterScreen() {
  const { register, branding } = useAuth();
  const getError = useApiError();
  const [countries, setCountries] = useState<
    Array<{ country: string; dial_code: string; country_code: string }>
  >([]);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    username: '',
    email: '',
    mobile: '',
    password: '',
    password_confirmation: '',
    country_code: 'US',
    mobile_code: '+1',
    country: 'United States',
    reference: '',
  });

  useEffect(() => {
    publicService.countries().then((res) => {
      const list = (res.data as { countries?: typeof countries } | undefined)?.countries;
      if (list) setCountries(list);
    });
  }, []);

  const update = (key: string, value: string) => setForm((prev) => ({ ...prev, [key]: value }));

  const onSubmit = async () => {
    setLoading(true);
    try {
      await register(form);
      router.replace('/');
    } catch (error) {
      Alert.alert('Registration failed', getError(error));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
        <AuthLogo height={48} />
        <Title>Create account</Title>
        <Subtitle>Register once — your app stays synced with the web portal.</Subtitle>

        <AppInput placeholder="Username" autoCapitalize="none" value={form.username} onChangeText={(v) => update('username', v)} />
        <AppInput placeholder="Email" autoCapitalize="none" keyboardType="email-address" value={form.email} onChangeText={(v) => update('email', v)} />
        <AppInput placeholder="Mobile number" keyboardType="phone-pad" value={form.mobile} onChangeText={(v) => update('mobile', v)} />
        <AppInput placeholder="Referral username (optional)" autoCapitalize="none" value={form.reference} onChangeText={(v) => update('reference', v)} />
        <AppInput placeholder="Password" secureTextEntry value={form.password} onChangeText={(v) => update('password', v)} />
        <AppInput placeholder="Confirm password" secureTextEntry value={form.password_confirmation} onChangeText={(v) => update('password_confirmation', v)} />

        {countries.length > 0 && (
          <View style={styles.countryBox}>
            <Text style={styles.countryLabel}>Country</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false}>
              {countries.slice(0, 20).map((item) => (
                <Pressable
                  key={item.country_code}
                  style={[
                    styles.countryChip,
                    form.country_code === item.country_code && styles.countryChipActive,
                  ]}
                  onPress={() =>
                    setForm((prev) => ({
                      ...prev,
                      country_code: item.country_code,
                      mobile_code: item.dial_code,
                      country: item.country,
                    }))
                  }
                >
                  <Text
                    style={[
                      styles.countryChipText,
                      form.country_code === item.country_code && styles.countryChipTextActive,
                    ]}
                  >
                    {item.country_code}
                  </Text>
                </Pressable>
              ))}
            </ScrollView>
          </View>
        )}

        <PrimaryButton title="Register" loading={loading} onPress={onSubmit} />
        <LegalLinks privacyUrl={branding?.privacy_policy_url} termsUrl={branding?.terms_url} />
        <Text style={styles.link} onPress={() => router.back()}>
          Already have an account? Sign in
        </Text>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.authBg },
  scroll: { padding: spacing.lg },
  countryBox: { marginBottom: spacing.md },
  countryLabel: { color: colors.body, marginBottom: spacing.sm },
  countryChip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    backgroundColor: colors.surface2,
    borderRadius: 20,
    marginRight: 8,
  },
  countryChipActive: { backgroundColor: colors.primary },
  countryChipText: { color: colors.ink, fontWeight: '600' },
  countryChipTextActive: { color: colors.white },
  link: {
    textAlign: 'center',
    color: colors.primary,
    marginTop: spacing.md,
    fontWeight: '600',
  },
});
