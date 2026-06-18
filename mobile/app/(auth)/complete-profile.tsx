import { router } from 'expo-router';
import { useState } from 'react';
import { Alert, ScrollView, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { AppInput, PrimaryButton, Subtitle, Title } from '../../src/components/ui';
import { AuthLogo, useApiError, useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { spacing } from '../../src/theme/spacing';

export default function CompleteProfileScreen() {
  const { completeProfile } = useAuth();
  const getError = useApiError();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    firstname: '',
    lastname: '',
    address: '',
    city: '',
    state: '',
    zip: '',
  });

  const update = (key: string, value: string) => setForm((prev) => ({ ...prev, [key]: value }));

  const onSubmit = async () => {
    setLoading(true);
    try {
      await completeProfile(form);
      router.replace('/');
    } catch (error) {
      Alert.alert('Profile error', getError(error));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <AuthLogo height={48} />
        <Title>Complete profile</Title>
        <Subtitle>One last step before accessing your dashboard.</Subtitle>
        <AppInput placeholder="First name" value={form.firstname} onChangeText={(v) => update('firstname', v)} />
        <AppInput placeholder="Last name" value={form.lastname} onChangeText={(v) => update('lastname', v)} />
        <AppInput placeholder="Address" value={form.address} onChangeText={(v) => update('address', v)} />
        <AppInput placeholder="City" value={form.city} onChangeText={(v) => update('city', v)} />
        <AppInput placeholder="State" value={form.state} onChangeText={(v) => update('state', v)} />
        <AppInput placeholder="Zip code" value={form.zip} onChangeText={(v) => update('zip', v)} />
        <PrimaryButton title="Save & Continue" loading={loading} onPress={onSubmit} />
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.authBg },
  scroll: { padding: spacing.lg },
});
