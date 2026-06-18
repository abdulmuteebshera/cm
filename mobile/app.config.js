export default {
  expo: {
    name: 'CrownMaire',
    slug: 'crownmaire-portal',
    version: '1.0.0',
    orientation: 'portrait',
    icon: './assets/icon.png',
    userInterfaceStyle: 'light',
    scheme: 'crownmaire',
    splash: {
      image: './assets/splash-icon.png',
      resizeMode: 'contain',
      backgroundColor: '#f2f3f5',
    },
    ios: {
      supportsTablet: true,
      bundleIdentifier: 'com.crownmaire.portal',
    },
    android: {
      package: 'com.crownmaire.portal',
      adaptiveIcon: {
        backgroundColor: '#1989BE',
        foregroundImage: './assets/android-icon-foreground.png',
        backgroundImage: './assets/android-icon-background.png',
      },
    },
    web: {
      favicon: './assets/favicon.png',
    },
    plugins: ['expo-router', 'expo-secure-store', 'expo-splash-screen', 'expo-web-browser', 'expo-font'],
    extra: {
      apiUrl: process.env.EXPO_PUBLIC_API_URL || 'https://crownmairecapital.com/api',
      siteUrl: process.env.EXPO_PUBLIC_SITE_URL || 'https://crownmairecapital.com',
      eas: {
        projectId: 'crownmaire-portal',
      },
    },
  },
};
