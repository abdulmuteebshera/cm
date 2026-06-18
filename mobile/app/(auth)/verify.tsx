import { router } from 'expo-router';
import { useMemo, useState } from 'react';
import { Alert, ScrollView, StyleSheet, Text } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { AppInput, PrimaryButton, Subtitle, Title } from '../../src/components/ui';
import { AuthLogo, useApiError, useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { spacing } from '../../src/theme/spacing';

export default function VerifyScreen() {
  const { user, verifyCode, resendCode } = useAuth();
  const getError = useApiError();
  const [code, setCode] = useState('');
  const [loading, setLoading] = useState(false);

  const verifyType = useMemo<'email' | 'sms' | '2fa'>(() => {
    if (!user?.ev) return 'email';
    if (!user?.sv) return 'sms';
    return '2fa';
  }, [user]);

  const title = verifyType === 'email' ? 'Verify email' : verifyType === 'sms' ? 'Verify mobile' : 'Two-factor auth';

  const onSubmit = async () => {
    setLoading(true);
    try {
      await verifyCode(verifyType, code);
      router.replace('/');
    } catch (error) {
      Alert.alert('Verification failed', getError(error));
    } finally {
      setLoading(false);
    }
  };

  const onResend = async () => {
    try {
      await resendCode(verifyType === '2fa' ? '2fa' : verifyType);
      Alert.alert('Sent', 'A new verification code has been sent.');
    } catch (error) {
      Alert.alert('Could not resend', getError(error));
    }
  };

  return (
    <SafeAreaView style={styles.safe}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <AuthLogo height={48} />
        <Title>{title}</Title>
        <Subtitle>Enter the verification code to activate your account.</Subtitle>
        <AppInput placeholder="Enter code" keyboardType="number-pad" value={code} onChangeText={setCode} />
        <PrimaryButton title="Verify" loading={loading} onPress={onSubmit} />
        {verifyType !== '2fa' && (
          <Text style={styles.link} onPress={onResend}>
            Resend code
          </Text>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.authBg },
  scroll: { padding: spacing.lg },
  link: { textAlign: 'center', color: colors.primary, marginTop: spacing.lg, fontWeight: '600' },
});
