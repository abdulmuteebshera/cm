import { router } from 'expo-router';
import { useState } from 'react';
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { AppInput, GhostButton, PrimaryButton, Subtitle, Title } from '../../src/components/ui';
import { LegalLinks } from '../../src/components/LegalLinks';
import { AuthLogo, useApiError, useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { radius, spacing } from '../../src/theme/spacing';

export default function LoginScreen() {
  const { login, branding } = useAuth();
  const getError = useApiError();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const onSubmit = async () => {
    if (!username || !password) {
      Alert.alert('Missing fields', 'Please enter username and password.');
      return;
    }
    setLoading(true);
    try {
      await login(username.trim(), password);
      router.replace('/');
    } catch (error) {
      Alert.alert('Login failed', getError(error));
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.flex}>
        <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
          <View style={styles.logoWrap}>
            <AuthLogo height={56} />
            {!branding?.auth_logo && (
              <Text style={styles.fallbackBrand}>{branding?.site_name ?? 'CrownMaire Capital'}</Text>
            )}
          </View>

          <View style={styles.formCard}>
            <Title>{branding?.login_title ?? 'Welcome back'}</Title>
            <Subtitle>
              {branding?.login_subtitle ??
                'Sign in to manage investments, deposits, and withdrawals from your mobile portal.'}
            </Subtitle>

            <AppInput
              placeholder="Username or email"
              autoCapitalize="none"
              value={username}
              onChangeText={setUsername}
            />
            <AppInput placeholder="Password" secureTextEntry value={password} onChangeText={setPassword} />

            <PrimaryButton title="Sign In" loading={loading} onPress={onSubmit} />
            <GhostButton title="Create an account" onPress={() => router.push('/(auth)/register')} />
            <LegalLinks
              privacyUrl={branding?.privacy_policy_url}
              termsUrl={branding?.terms_url}
              prefix="View our"
            />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.authBg },
  flex: { flex: 1 },
  scroll: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: spacing.lg,
    paddingVertical: spacing.xl,
  },
  logoWrap: {
    alignItems: 'center',
    marginBottom: spacing.xl,
  },
  fallbackBrand: {
    marginTop: spacing.sm,
    fontSize: 22,
    fontWeight: '800',
    color: colors.primary,
  },
  formCard: {
    backgroundColor: colors.surface,
    borderRadius: radius.lg,
    padding: spacing.lg,
    borderWidth: 1,
    borderColor: colors.border,
  },
});
