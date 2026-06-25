import { router } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useRef } from 'react';
import { Animated, Easing, Image, StyleSheet, View } from 'react-native';
import { colors } from '../src/theme/colors';
import { session } from '../src/state/session';

const LOGO_W = 300;
const LOGO_H = 100;
const PIECES = 4;
const PIECE_W = LOGO_W / PIECES;

// Each quarter slides in from an alternating outward offset and joins center.
const START_OFFSETS = [-120, -55, 55, 120];

export default function IntroScreen() {
  const pieceX = useRef(START_OFFSETS.map((o) => new Animated.Value(o))).current;
  const pieceOpacity = useRef(START_OFFSETS.map(() => new Animated.Value(0))).current;
  const logoScale = useRef(new Animated.Value(0.86)).current;
  const taglineOpacity = useRef(new Animated.Value(0)).current;
  const taglineY = useRef(new Animated.Value(14)).current;
  const underline = useRef(new Animated.Value(0)).current;
  const glow = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    session.introShown = true;

    const joinPieces = Animated.parallel(
      pieceX.map((x, i) =>
        Animated.parallel([
          Animated.timing(x, {
            toValue: 0,
            duration: 760,
            delay: i * 110,
            easing: Easing.out(Easing.cubic),
            useNativeDriver: true,
          }),
          Animated.timing(pieceOpacity[i], {
            toValue: 1,
            duration: 520,
            delay: i * 110,
            easing: Easing.out(Easing.quad),
            useNativeDriver: true,
          }),
        ])
      )
    );

    Animated.sequence([
      Animated.timing(glow, {
        toValue: 1,
        duration: 600,
        easing: Easing.out(Easing.quad),
        useNativeDriver: true,
      }),
      Animated.parallel([
        joinPieces,
        Animated.timing(logoScale, {
          toValue: 1,
          duration: 900,
          easing: Easing.out(Easing.back(1.4)),
          useNativeDriver: true,
        }),
      ]),
      Animated.parallel([
        Animated.timing(underline, {
          toValue: 1,
          duration: 520,
          easing: Easing.out(Easing.cubic),
          useNativeDriver: false,
        }),
        Animated.timing(taglineOpacity, {
          toValue: 1,
          duration: 620,
          easing: Easing.out(Easing.quad),
          useNativeDriver: true,
        }),
        Animated.timing(taglineY, {
          toValue: 0,
          duration: 620,
          easing: Easing.out(Easing.cubic),
          useNativeDriver: true,
        }),
      ]),
      Animated.delay(900),
    ]).start(() => {
      router.replace('/(tabs)');
    });

    // Safety fallback in case the animation callback is interrupted.
    const fallback = setTimeout(() => router.replace('/(tabs)'), 5200);
    return () => clearTimeout(fallback);
  }, []);

  const glowScale = glow.interpolate({ inputRange: [0, 1], outputRange: [0.6, 1] });
  const underlineWidth = underline.interpolate({ inputRange: [0, 1], outputRange: [0, 188] });

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <Animated.View style={[styles.glow, { opacity: glow, transform: [{ scale: glowScale }] }]} />

      <Animated.View style={[styles.logoWrap, { transform: [{ scale: logoScale }] }]}>
        {START_OFFSETS.map((_, i) => (
          <Animated.View
            key={i}
            style={[
              styles.piece,
              {
                left: i * PIECE_W,
                opacity: pieceOpacity[i],
                transform: [{ translateX: pieceX[i] }],
              },
            ]}
          >
            <Image
              source={require('../assets/logo.png')}
              style={{ width: LOGO_W, height: LOGO_H, marginLeft: -(i * PIECE_W) }}
              resizeMode="contain"
            />
          </Animated.View>
        ))}
      </Animated.View>

      <Animated.View
        style={[styles.taglineWrap, { opacity: taglineOpacity, transform: [{ translateY: taglineY }] }]}
      >
        <Animated.View style={[styles.underline, { width: underlineWidth }]} />
        <Animated.Text style={styles.tagline}>The intelligence behind modern wealth</Animated.Text>
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#000000',
    alignItems: 'center',
    justifyContent: 'center',
  },
  glow: {
    position: 'absolute',
    width: 460,
    height: 460,
    borderRadius: 230,
    backgroundColor: 'rgba(25, 137, 190, 0.20)',
    top: '24%',
  },
  logoWrap: {
    width: LOGO_W,
    height: LOGO_H,
    flexDirection: 'row',
  },
  piece: {
    position: 'absolute',
    top: 0,
    width: PIECE_W,
    height: LOGO_H,
    overflow: 'hidden',
  },
  taglineWrap: {
    marginTop: 26,
    alignItems: 'center',
  },
  underline: {
    height: 2,
    borderRadius: 2,
    backgroundColor: colors.primary,
    marginBottom: 14,
  },
  tagline: {
    fontSize: 14,
    letterSpacing: 1.5,
    color: 'rgba(255, 255, 255, 0.88)',
    fontWeight: '600',
    textTransform: 'uppercase',
    textAlign: 'center',
  },
});
