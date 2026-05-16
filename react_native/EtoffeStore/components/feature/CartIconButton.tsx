// Powered by OnSpace.AI
import React from 'react';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { Colors, Typography } from '@/constants/theme';
import { useCart } from '@/hooks/useCart';

export function CartIconButton() {
  const router = useRouter();
  const { count } = useCart();
  return (
    <Pressable
      onPress={() => router.push('/(tabs)/cart')}
      hitSlop={12}
      style={({ pressed }) => [styles.btn, pressed && { opacity: 0.7 }]}
    >
      <MaterialIcons name="shopping-bag" size={22} color={Colors.text} />
      {count > 0 ? (
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{count}</Text>
        </View>
      ) : null}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  btn: { width: 40, height: 40, alignItems: 'center', justifyContent: 'center' },
  badge: {
    position: 'absolute',
    top: 4,
    right: 2,
    minWidth: 18,
    height: 18,
    paddingHorizontal: 5,
    borderRadius: 9,
    backgroundColor: Colors.accent,
    alignItems: 'center',
    justifyContent: 'center',
  },
  badgeText: { ...Typography.caption, color: Colors.surface, fontSize: 11 },
});
