// Powered by OnSpace.AI
import React from 'react';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { Image } from 'expo-image';
import { useRouter } from 'expo-router';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { Product } from '@/services/products';

interface Props {
  product: Product;
  width?: number;
  variant?: 'grid' | 'feature';
}

export function ProductCard({ product, width, variant = 'grid' }: Props) {
  const router = useRouter();
  const cardWidth = width ?? '100%';
  const imageHeight = variant === 'feature' ? 280 : 220;

  return (
    <Pressable
      onPress={() => router.push(`/product/${product.id}`)}
      style={({ pressed }) => [
        styles.card,
        { width: cardWidth as any },
        pressed && { opacity: 0.92 },
      ]}
    >
      <View style={[styles.imageWrap, { height: imageHeight }]}>
        <Image
          source={{ uri: product.image }}
          style={styles.image}
          contentFit="cover"
          transition={250}
        />
        {product.tag ? (
          <View style={styles.tag}>
            <Text style={styles.tagText}>{product.tag}</Text>
          </View>
        ) : null}
      </View>
      <View style={styles.info}>
        <Text style={styles.brand} numberOfLines={1}>
          {product.brand}
        </Text>
        <Text style={styles.name} numberOfLines={1}>
          {product.name}
        </Text>
        <View style={styles.priceRow}>
          <Text style={styles.price}>${product.price}</Text>
          {product.originalPrice ? (
            <Text style={styles.original}>${product.originalPrice}</Text>
          ) : null}
        </View>
      </View>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: 'transparent' },
  imageWrap: {
    width: '100%',
    borderRadius: Radius.md,
    overflow: 'hidden',
    backgroundColor: Colors.surfaceAlt,
    marginBottom: Spacing.md,
  },
  image: { width: '100%', height: '100%' },
  tag: {
    position: 'absolute',
    top: Spacing.md,
    left: Spacing.md,
    backgroundColor: Colors.surface,
    paddingHorizontal: Spacing.md,
    paddingVertical: 6,
    borderRadius: Radius.pill,
  },
  tagText: { ...Typography.caption, color: Colors.text, textTransform: 'uppercase' },
  info: { paddingHorizontal: 2 },
  brand: {
    ...Typography.caption,
    color: Colors.textSubtle,
    textTransform: 'uppercase',
    marginBottom: 4,
  },
  name: { ...Typography.body, color: Colors.text, marginBottom: 4 },
  priceRow: { flexDirection: 'row', alignItems: 'baseline' },
  price: { ...Typography.bodyStrong, color: Colors.text },
  original: {
    ...Typography.small,
    color: Colors.textMuted,
    textDecorationLine: 'line-through',
    marginLeft: Spacing.sm,
  },
});
