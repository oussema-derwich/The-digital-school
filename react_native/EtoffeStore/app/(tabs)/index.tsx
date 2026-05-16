// Powered by OnSpace.AI
import React from 'react';
import {
  FlatList,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { Image } from 'expo-image';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { MaterialIcons } from '@expo/vector-icons';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { useProducts } from '@/hooks/useProducts';
import { ProductCard } from '@/components/feature/ProductCard';
import { CartIconButton } from '@/components/feature/CartIconButton';
import { Brands } from '@/services/products';

export default function HomeScreen() {
  const router = useRouter();
  const { featured, newArrivals } = useProducts();

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.header}>
        <View>
          <Text style={styles.brand}>ÉTOFFE</Text>
          <Text style={styles.tagline}>Curated essentials</Text>
        </View>
        <View style={styles.headerActions}>
          <Pressable
            onPress={() => router.push('/(tabs)/shop')}
            style={({ pressed }) => [styles.iconBtn, pressed && { opacity: 0.6 }]}
            hitSlop={10}
          >
            <MaterialIcons name="search" size={22} color={Colors.text} />
          </Pressable>
          <CartIconButton />
        </View>
      </View>

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingBottom: Spacing.xxxl }}
      >
        {/* Hero */}
        <Pressable onPress={() => router.push('/(tabs)/shop')} style={styles.hero}>
          <Image
            source={{
              uri: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80',
            }}
            style={styles.heroImage}
            contentFit="cover"
            transition={300}
          />
          <View style={styles.heroOverlay} />
          <View style={styles.heroContent}>
            <Text style={styles.heroEyebrow}>SS / 26 EDIT</Text>
            <Text style={styles.heroTitle}>Quiet{'\n'}Luxury.</Text>
            <View style={styles.heroCta}>
              <Text style={styles.heroCtaText}>Shop the edit</Text>
              <MaterialIcons name="arrow-forward" size={16} color={Colors.surface} />
            </View>
          </View>
        </Pressable>

        {/* Brand strip */}
        <View style={styles.brandStrip}>
          {Brands.map((b) => (
            <Text key={b} style={styles.brandChip}>
              {b}
            </Text>
          ))}
        </View>

        {/* Featured */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View>
              <Text style={styles.sectionEyebrow}>FEATURED</Text>
              <Text style={styles.sectionTitle}>Editor&apos;s picks</Text>
            </View>
            <Pressable onPress={() => router.push('/(tabs)/shop')}>
              <Text style={styles.viewAll}>View all</Text>
            </Pressable>
          </View>
          <FlatList
            data={featured}
            horizontal
            keyExtractor={(i) => i.id}
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={{ paddingHorizontal: Spacing.lg }}
            ItemSeparatorComponent={() => <View style={{ width: Spacing.md }} />}
            renderItem={({ item }) => (
              <ProductCard product={item} width={220} variant="feature" />
            )}
          />
        </View>

        {/* Category banner */}
        <View style={styles.bannerRow}>
          <Pressable
            style={styles.bannerCard}
            onPress={() => router.push('/(tabs)/shop')}
          >
            <Image
              source={{
                uri: 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&q=80',
              }}
              style={styles.bannerImg}
              contentFit="cover"
            />
            <View style={styles.bannerLabel}>
              <Text style={styles.bannerLabelText}>WOMEN</Text>
            </View>
          </Pressable>
          <Pressable
            style={styles.bannerCard}
            onPress={() => router.push('/(tabs)/shop')}
          >
            <Image
              source={{
                uri: 'https://images.unsplash.com/photo-1516257984-b1b4d707412e?w=600&q=80',
              }}
              style={styles.bannerImg}
              contentFit="cover"
            />
            <View style={styles.bannerLabel}>
              <Text style={styles.bannerLabelText}>MEN</Text>
            </View>
          </Pressable>
        </View>

        {/* New arrivals */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <View>
              <Text style={styles.sectionEyebrow}>JUST IN</Text>
              <Text style={styles.sectionTitle}>New arrivals</Text>
            </View>
          </View>
          <View style={styles.grid}>
            {newArrivals.map((p) => (
              <View key={p.id} style={styles.gridItem}>
                <ProductCard product={p} />
              </View>
            ))}
          </View>
        </View>

        {/* Promise */}
        <View style={styles.promise}>
          <Text style={styles.promiseTitle}>Considered. Crafted. Lasting.</Text>
          <Text style={styles.promiseBody}>
            Free shipping on orders over $200. 30-day easy returns.
          </Text>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: Colors.background },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
  },
  brand: {
    fontSize: 24,
    fontWeight: '800',
    letterSpacing: 4,
    color: Colors.text,
  },
  tagline: {
    ...Typography.caption,
    color: Colors.textSubtle,
    marginTop: 2,
    letterSpacing: 1,
  },
  headerActions: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  iconBtn: { width: 40, height: 40, alignItems: 'center', justifyContent: 'center' },

  hero: {
    marginHorizontal: Spacing.lg,
    height: 460,
    borderRadius: Radius.lg,
    overflow: 'hidden',
    marginTop: Spacing.sm,
  },
  heroImage: { width: '100%', height: '100%' },
  heroOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.25)',
  },
  heroContent: {
    position: 'absolute',
    left: Spacing.xl,
    bottom: Spacing.xl,
    right: Spacing.xl,
  },
  heroEyebrow: {
    ...Typography.caption,
    color: Colors.surface,
    letterSpacing: 3,
    marginBottom: Spacing.sm,
  },
  heroTitle: {
    fontSize: 48,
    fontWeight: '800',
    color: Colors.surface,
    lineHeight: 50,
    letterSpacing: -1,
    marginBottom: Spacing.lg,
  },
  heroCta: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    paddingBottom: 4,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surface,
  },
  heroCtaText: {
    color: Colors.surface,
    ...Typography.bodyStrong,
    marginRight: Spacing.sm,
    letterSpacing: 0.5,
  },

  brandStrip: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
    paddingVertical: Spacing.xl,
    marginTop: Spacing.sm,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: Colors.border,
    marginHorizontal: Spacing.lg,
  },
  brandChip: {
    ...Typography.caption,
    color: Colors.textSubtle,
    letterSpacing: 2,
    fontWeight: '700',
  },

  section: { marginTop: Spacing.xxl },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.lg,
  },
  sectionEyebrow: {
    ...Typography.caption,
    color: Colors.accent,
    letterSpacing: 2,
    marginBottom: 4,
  },
  sectionTitle: { ...Typography.title, color: Colors.text },
  viewAll: {
    ...Typography.small,
    color: Colors.text,
    textDecorationLine: 'underline',
    fontWeight: '600',
  },

  bannerRow: {
    flexDirection: 'row',
    paddingHorizontal: Spacing.lg,
    marginTop: Spacing.xxl,
    gap: Spacing.md,
  },
  bannerCard: {
    flex: 1,
    height: 220,
    borderRadius: Radius.md,
    overflow: 'hidden',
    backgroundColor: Colors.surfaceAlt,
  },
  bannerImg: { width: '100%', height: '100%' },
  bannerLabel: {
    position: 'absolute',
    bottom: Spacing.md,
    left: Spacing.md,
    backgroundColor: Colors.surface,
    paddingHorizontal: Spacing.md,
    paddingVertical: 6,
    borderRadius: Radius.pill,
  },
  bannerLabelText: {
    ...Typography.caption,
    color: Colors.text,
    letterSpacing: 1.5,
    fontWeight: '700',
  },

  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    paddingHorizontal: Spacing.lg,
    gap: Spacing.lg,
  },
  gridItem: { width: '47%' },

  promise: {
    marginTop: Spacing.xxxl,
    marginHorizontal: Spacing.lg,
    paddingVertical: Spacing.xl,
    paddingHorizontal: Spacing.xl,
    borderRadius: Radius.md,
    backgroundColor: Colors.accentSoft,
    alignItems: 'center',
  },
  promiseTitle: {
    ...Typography.section,
    color: Colors.text,
    marginBottom: Spacing.sm,
    textAlign: 'center',
  },
  promiseBody: {
    ...Typography.small,
    color: Colors.textSubtle,
    textAlign: 'center',
    lineHeight: 20,
  },
});
