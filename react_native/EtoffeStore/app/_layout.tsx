// Powered by OnSpace.AI
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { AlertProvider } from '@/template';
import { CartProvider } from '@/contexts/CartContext';

export default function RootLayout() {
  return (
    <AlertProvider>
      <SafeAreaProvider>
        <CartProvider>
          <StatusBar style="dark" />
          <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: '#FAF8F5' } }}>
            <Stack.Screen name="(tabs)" />
            <Stack.Screen name="product/[id]" options={{ animation: 'slide_from_right' }} />
            <Stack.Screen name="checkout" options={{ animation: 'slide_from_bottom' }} />
          </Stack>
        </CartProvider>
      </SafeAreaProvider>
    </AlertProvider>
  );
}
