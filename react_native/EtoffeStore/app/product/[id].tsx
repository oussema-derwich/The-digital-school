// Powered by OnSpace.AI
import React, { useMemo, useState } from 'react';
import {
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { Image } from 'expo-image';
import { MaterialIcons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { productService } from '@/services/products';
import { PrimaryButton } from '@/components/ui/PrimaryButton';
import { useCart } from '@/hooks/useCart';
import { useAlert } from '@/template';

export default function ProductDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const { addToCart } = useCart();
  const { showAlert } = useAlert();

  const product = useMemo(() => productService.byId(id as string), [id]);

  const [activeImage, setActiveImage] = useState(0);
  const [size, setSize] = useState(product?.sizes[1] ?? product?.sizes[0] ?? '');
  const [color, setColor] = useState(product?.colors[0] ?? '');
  const [favorite, setFavorite] = useState(false);

  if (!product) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.centered}>
          <Text style={styles.notFound}>Product not found</Text>
          <PrimaryButton label="Go back" onPress={() => router.back()} />
        </View>
      </SafeAreaView>
    );
  }

  const gallery = product.gallery.length > 0 ? product.gallery : [product.image];

  const handleAdd = () => {
    addToCart(product, size, color, 1);
    showAlert('Added to bag', `${product.name} (Size ${size}, ${color})`, [
      { text: 'Continue', style: 'cancel' },
      { text: 'View bag', onPress: () => router.push('/(tabs)/cart') },
    ]);
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.topBar}>
        <Pressable
          onPress={() => router.back()}
          hitSlop={12}
          style={({ pressed }) => [styles.iconCircle, pressed && { opacity: 0.7 }]}
        >
          <MaterialIcons name="arrow-back-ios-new" size={18} color={Colors.text} />
        </Pressable>
        <Pressable
          onPress={() => setFavorite((f) => !f)}
          hitSlop={12}
          style={({ pressed }) => [styles.iconCircle, pressed && { opacity: 0.7 }]}
        >
          <MaterialIcons
            name={favorite ? 'favorite' : 'favorite-border'}
            size={20}
            color={favorite ? Colors.danger : Colors.text}
          />
        </Pressable>
      </View>

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: 140 }}
      >
        <View style={styles.heroImageWrap}>
          <Image
            source={{ uri: gallery[activeImage] }}
            style={styles.heroImage}
            contentFit="cover"
            transition={250}
          />
        </View>

        {gallery.length > 1 ? (
          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.thumbRow}
          >
            {gallery.map((g, i) => (
              <Pressable
                key={g + i}
                onPress={() => setActiveImage(i)}
                style={[
                  styles.thumb,
                  activeImage === i && { borderColor: Colors.text },
                ]}
              >
                <Image source={{ uri: g }} style={styles.thumbImg} contentFit="cover" />
              </Pressable>
            ))}
          </ScrollView>
        ) : null}

        <View style={styles.body}>
          <Text style={styles.brand}>{product.brand}</Text>
          <Text style={styles.name}>{product.name}</Text>
          <View style={styles.priceRow}>
            <Text style={styles.price}>${product.price}</Text>
            {product.originalPrice ? (
              <Text style={styles.original}>${product.originalPrice}</Text>
            ) : null}
            <View style={styles.rating}>
              <MaterialIcons name="star" size={14} color={Colors.accent} />
              <Text style={styles.ratingText}>{product.rating}</Text>
            </View>
          </View>

          <Text style={styles.description}>{product.description}</Text>

          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>Color</Text>
              <Text style={styles.sectionMeta}>{color}</Text>
            </View>
            <View style={styles.optionRow}>
              {product.colors.map((c) => (
                <Pressable
                  key={c}
                  onPress={() => setColor(c)}
                  style={[
                    styles.colorChip,
                    color === c && styles.colorChipSelected,
                  ]}
                >
                  <Text
                    style={[
                      styles.colorChipText,
                      color === c && { color: Colors.surface },
                    ]}
                  >
                    {c}
                  </Text>
                </Pressable>
              ))}
            </View>
          </View>

          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>Size</Text>
              <Text style={styles.sectionMeta}>Size guide</Text>
            </View>
            <View style={styles.optionRow}>
              {product.sizes.map((s) => (
                <Pressable
                  key={s}
                  onPress={() => setSize(s)}
                  style={[styles.sizeBox, size === s && styles.sizeBoxSelected]}
                >
                  <Text
                    style={[
                      styles.sizeText,
                      size === s && { color: Colors.surface },
                    ]}
                  >
                    {s}
                  </Text>
                </Pressable>
              ))}
            </View>
          </View>

          <View style={styles.detailsCard}>
            <View style={styles.detailRow}>
              <MaterialIcons name="local-shipping" size={18} color={Colors.text} />
              <Text style={styles.detailText}>Free shipping on orders over $200</Text>
            </View>
            <View style={styles.detailRow}>
              <MaterialIcons name="autorenew" size={18} color={Colors.text} />
              <Text style={styles.detailText}>30-day complimentary returns</Text>
            </View>
            <View style={styles.detailRow}>
              <MaterialIcons name="verified" size={18} color={Colors.text} />
              <Text style={styles.detailText}>Authentic from {product.brand}</Text>
            </View>
          </View>
        </View>
      </ScrollView>

      <View style={styles.cta}>
        <View style={{ flex: 1 }}>
          <Text style={styles.ctaLabel}>Total</Text>
          <Text style={styles.ctaPrice}>${product.price}</Text>
        </View>
        <PrimaryButton
          label="Add to bag"
          onPress={handleAdd}
          style={{ flex: 1.4 }}
          icon={<MaterialIcons name="shopping-bag" size={18} color={Colors.surface} />}
        />
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: Colors.background },
  topBar: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    zIndex: 10,
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  iconCircle: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: Colors.surface,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
    elevation: 3,
  },

  heroImageWrap: {
    width: '100%',
    aspectRatio: 3 / 4,
    backgroundColor: Colors.surfaceAlt,
  },
  heroImage: { width: '100%', height: '100%' },

  thumbRow: {
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
    gap: Spacing.sm,
  },
  thumb: {
    width: 56,
    height: 70,
    borderRadius: Radius.sm,
    overflow: 'hidden',
    borderWidth: 2,
    borderColor: 'transparent',
    backgroundColor: Colors.surfaceAlt,
  },
  thumbImg: { width: '100%', height: '100%' },

  body: { padding: Spacing.lg, paddingTop: Spacing.lg },
  brand: {
    ...Typography.caption,
    color: Colors.textSubtle,
    letterSpacing: 2,
    marginBottom: Spacing.sm,
  },
  name: { ...Typography.display, color: Colors.text, marginBottom: Spacing.md },
  priceRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: Spacing.lg,
  },
  price: { ...Typography.title, color: Colors.text },
  original: {
    ...Typography.body,
    color: Colors.textMuted,
    textDecorationLine: 'line-through',
    marginLeft: Spacing.md,
  },
  rating: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 'auto',
  },
  ratingText: {
    ...Typography.small,
    fontWeight: '600',
    color: Colors.text,
    marginLeft: 4,
  },
  description: {
    ...Typography.body,
    color: Colors.textSubtle,
    lineHeight: 24,
    marginBottom: Spacing.xl,
  },

  section: { marginBottom: Spacing.xl },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: Spacing.md,
  },
  sectionTitle: { ...Typography.bodyStrong, color: Colors.text },
  sectionMeta: { ...Typography.small, color: Colors.textSubtle },

  optionRow: { flexDirection: 'row', flexWrap: 'wrap', gap: Spacing.sm },

  colorChip: {
    paddingHorizontal: Spacing.lg,
    height: 38,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: Radius.pill,
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.surface,
  },
  colorChipSelected: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  colorChipText: { ...Typography.small, fontWeight: '600', color: Colors.text },

  sizeBox: {
    minWidth: 56,
    height: 48,
    paddingHorizontal: Spacing.md,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: Radius.sm,
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.surface,
  },
  sizeBoxSelected: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  sizeText: { ...Typography.bodyStrong, color: Colors.text },

  detailsCard: {
    backgroundColor: Colors.surface,
    borderRadius: Radius.md,
    padding: Spacing.lg,
    borderWidth: 1,
    borderColor: Colors.border,
    gap: Spacing.md,
  },
  detailRow: { flexDirection: 'row', alignItems: 'center', gap: Spacing.md },
  detailText: { ...Typography.small, color: Colors.text },

  cta: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.xl,
    backgroundColor: Colors.surface,
    borderTopWidth: 1,
    borderTopColor: Colors.border,
    gap: Spacing.md,
  },
  ctaLabel: {
    ...Typography.caption,
    color: Colors.textSubtle,
    letterSpacing: 1.2,
  },
  ctaPrice: { ...Typography.title, color: Colors.text },

  centered: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: Spacing.xl },
  notFound: { ...Typography.section, color: Colors.text, marginBottom: Spacing.lg },
});
