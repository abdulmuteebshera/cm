export default {
  expo: {
    name: 'Crownmaire Capital',
    slug: 'crownmaire-portal',
    owner: 'abdulshera',
    version: '1.1.0',
    orientation: 'portrait',
    icon: './assets/icon.png',
    userInterfaceStyle: 'light',
    scheme: 'crownmaire',
    splash: {
      image: './assets/splash-icon.png',
      resizeMode: 'contain',
      backgroundColor: '#ffffff',
    },
    ios: {
      supportsTablet: true,
      bundleIdentifier: 'com.crownmaire.portal',
    },
    android: {
      package: 'com.crownmaire.portal',
      adaptiveIcon: {
        backgroundColor: '#ffffff',
        foregroundImage: './assets/android-icon-foreground.png',
        monochromeImage: './assets/android-icon-monochrome.png',
      },
    },
    web: {
      favicon: './assets/favicon.png',
    },
    plugins: [
      'expo-router',
      'expo-secure-store',
      [
        'expo-splash-screen',
        {
          image: './assets/splash-icon.png',
          resizeMode: 'contain',
          backgroundColor: '#ffffff',
          imageWidth: 240,
        },
      ],
      'expo-web-browser',
      'expo-font',
    ],
    extra: {
      apiUrl: process.env.EXPO_PUBLIC_API_URL || 'https://crownmairecapital.com/api',
      siteUrl: process.env.EXPO_PUBLIC_SITE_URL || 'https://crownmairecapital.com',
      eas: {
        projectId: 'd76e78d3-39c7-4ca3-acdb-0a8440a09bf1',
      },
    },
  },
};
