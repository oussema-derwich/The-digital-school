// Powered by OnSpace.AI
import React from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, View, ViewStyle } from 'react-native';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';

interface Props {
  label: string;
  onPress?: () => void;
  variant?: 'primary' | 'outline' | 'ghost';
  loading?: boolean;
  disabled?: boolean;
  style?: ViewStyle;
  icon?: React.ReactNode;
}

export function PrimaryButton({
  label,
  onPress,
  variant = 'primary',
  loading,
  disabled,
  style,
  icon,
}: Props) {
  const isDisabled = disabled || loading;
  return (
    <Pressable
      onPress={onPress}
      disabled={isDisabled}
      style={({ pressed }) => [
        styles.base,
        variant === 'primary' && styles.primary,
        variant === 'outline' && styles.outline,
        variant === 'ghost' && styles.ghost,
        isDisabled && styles.disabled,
        pressed && { opacity: 0.85, transform: [{ scale: 0.99 }] },
        style,
      ]}
    >
      {loading ? (
        <ActivityIndicator color={variant === 'primary' ? Colors.surface : Colors.text} />
      ) : (
        <View style={styles.row}>
          {icon ? <View style={styles.icon}>{icon}</View> : null}
          <Text
            style={[
              styles.label,
              variant === 'primary' && { color: Colors.surface },
              variant === 'outline' && { color: Colors.text },
              variant === 'ghost' && { color: Colors.text },
            ]}
          >
            {label}
          </Text>
        </View>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  base: {
    height: 54,
    borderRadius: Radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: Spacing.xl,
  },
  primary: { backgroundColor: Colors.primary },
  outline: {
    borderWidth: 1,
    borderColor: Colors.text,
    backgroundColor: 'transparent',
  },
  ghost: { backgroundColor: Colors.surfaceAlt },
  disabled: { opacity: 0.5 },
  row: { flexDirection: 'row', alignItems: 'center' },
  icon: { marginRight: Spacing.sm },
  label: { ...Typography.bodyStrong, letterSpacing: 0.4 },
});
