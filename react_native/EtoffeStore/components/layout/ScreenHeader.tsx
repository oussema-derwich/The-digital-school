// Powered by OnSpace.AI
import React from 'react';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { Colors, Spacing, Typography } from '@/constants/theme';

interface Props {
  title?: string;
  showBack?: boolean;
  rightAction?: React.ReactNode;
  variant?: 'default' | 'transparent';
}

export function ScreenHeader({ title, showBack, rightAction, variant = 'default' }: Props) {
  const router = useRouter();

  return (
    <View
      style={[
        styles.container,
        variant === 'transparent' ? styles.transparent : styles.default,
      ]}
    >
      <View style={styles.side}>
        {showBack ? (
          <Pressable
            onPress={() => router.back()}
            hitSlop={12}
            style={({ pressed }) => [styles.iconBtn, pressed && { opacity: 0.6 }]}
          >
            <MaterialIcons name="arrow-back-ios-new" size={20} color={Colors.text} />
          </Pressable>
        ) : null}
      </View>
      <Text style={styles.title} numberOfLines={1}>
        {title ?? ''}
      </Text>
      <View style={[styles.side, { alignItems: 'flex-end' }]}>{rightAction}</View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    height: 52,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
  },
  default: { backgroundColor: Colors.background },
  transparent: { backgroundColor: 'transparent' },
  side: { width: 60, justifyContent: 'center' },
  iconBtn: {
    width: 40,
    height: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    flex: 1,
    textAlign: 'center',
    ...Typography.bodyStrong,
    color: Colors.text,
    letterSpacing: 0.5,
  },
});
