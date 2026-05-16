// Powered by OnSpace.AI
import React from 'react';
import {
  FlatList,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { useProducts } from '@/hooks/useProducts';
import { ProductCard } from '@/components/feature/ProductCard';
import { Chip } from '@/components/ui/Chip';
import { Categories } from '@/services/products';
import { CartIconButton } from '@/components/feature/CartIconButton';

export default function ShopScreen() {
  const { products, category, setCategory, query, setQuery } = useProducts();

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.header}>
        <View>
          <Text style={styles.eyebrow}>BROWSE</Text>
          <Text style={styles.title}>The Collection</Text>
        </View>
        <CartIconButton />
      </View>

      <View style={styles.searchWrap}>
        <MaterialIcons name="search" size={20} color={Colors.textSubtle} />
        <TextInput
          value={query}
          onChangeText={setQuery}
          placeholder="Search brand or product"
          placeholderTextColor={Colors.textMuted}
          style={styles.search}
        />
      </View>

      <View style={styles.chipsWrap}>
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={{
            paddingHorizontal: Spacing.lg,
            alignItems: 'center',
          }}
        >
          {Categories.map((c) => (
            <Chip
              key={c}
              label={c}
              selected={category === c}
              onPress={() => setCategory(c)}
            />
          ))}
        </ScrollView>
      </View>

      <View style={styles.countRow}>
        <Text style={styles.countText}>
          {products.length} {products.length === 1 ? 'piece' : 'pieces'}
        </Text>
        <View style={styles.sortBtn}>
          <Text style={styles.sortText}>Recommended</Text>
          <MaterialIcons name="keyboard-arrow-down" size={18} color={Colors.text} />
        </View>
      </View>

      <FlatList
        data={products}
        keyExtractor={(i) => i.id}
        numColumns={2}
        columnWrapperStyle={{ gap: Spacing.lg, paddingHorizontal: Spacing.lg }}
        contentContainerStyle={{
          paddingBottom: Spacing.xxxl,
          gap: Spacing.xl,
        }}
        showsVerticalScrollIndicator={false}
        renderItem={({ item }) => (
          <View style={{ flex: 1 }}>
            <ProductCard product={item} />
          </View>
        )}
        ListEmptyComponent={
          <View style={styles.empty}>
            <MaterialIcons name="search-off" size={36} color={Colors.textMuted} />
            <Text style={styles.emptyTitle}>No results</Text>
            <Text style={styles.emptyBody}>Try adjusting filters or search terms.</Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: Colors.background },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
  },
  eyebrow: {
    ...Typography.caption,
    color: Colors.accent,
    letterSpacing: 2,
    marginBottom: 4,
  },
  title: { ...Typography.display, color: Colors.text },

  searchWrap: {
    marginHorizontal: Spacing.lg,
    height: 48,
    borderRadius: Radius.pill,
    backgroundColor: Colors.surface,
    borderWidth: 1,
    borderColor: Colors.border,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    marginTop: Spacing.md,
  },
  search: {
    flex: 1,
    marginLeft: Spacing.sm,
    ...Typography.body,
    color: Colors.text,
  },

  chipsWrap: {
    minHeight: 52,
    paddingVertical: Spacing.md,
  },

  countRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: Spacing.lg,
    paddingBottom: Spacing.md,
  },
  countText: { ...Typography.small, color: Colors.textSubtle, letterSpacing: 0.5 },
  sortBtn: { flexDirection: 'row', alignItems: 'center' },
  sortText: { ...Typography.small, color: Colors.text, fontWeight: '600' },

  empty: {
    paddingHorizontal: Spacing.xl,
    paddingTop: Spacing.xxxl,
    alignItems: 'center',
  },
  emptyTitle: { ...Typography.section, color: Colors.text, marginTop: Spacing.md },
  emptyBody: {
    ...Typography.small,
    color: Colors.textSubtle,
    marginTop: Spacing.xs,
    textAlign: 'center',
  },
});
