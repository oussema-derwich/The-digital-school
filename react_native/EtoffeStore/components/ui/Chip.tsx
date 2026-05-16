// Powered by OnSpace.AI
import React from 'react';
import { Pressable, StyleSheet, Text } from 'react-native';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';

interface Props {
  label: string;
  selected?: boolean;
  onPress?: () => void;
}

export function Chip({ label, selected, onPress }: Props) {
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => [
        styles.chip,
        selected ? styles.selected : styles.unselected,
        pressed && { opacity: 0.8 },
      ]}
    >
      <Text style={[styles.label, selected ? styles.labelSelected : styles.labelUnselected]}>
        {label}
      </Text>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  chip: {
    height: 38,
    paddingHorizontal: Spacing.lg,
    borderRadius: Radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: Spacing.sm,
  },
  selected: { backgroundColor: Colors.primary },
  unselected: {
    backgroundColor: Colors.surface,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  label: { ...Typography.small, fontWeight: '600' },
  labelSelected: { color: Colors.surface },
  labelUnselected: { color: Colors.text },
});
