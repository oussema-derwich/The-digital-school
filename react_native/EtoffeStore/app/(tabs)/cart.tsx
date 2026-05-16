// Powered by OnSpace.AI
import React from 'react';
import {
  FlatList,
  Pressable,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { Image } from 'expo-image';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { useCart } from '@/hooks/useCart';
import { PrimaryButton } from '@/components/ui/PrimaryButton';

export default function CartScreen() {
  const router = useRouter();
  const { items, updateQuantity, removeFromCart, subtotal, shipping, total, count } = useCart();

  const isEmpty = items.length === 0;

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <View style={styles.header}>
        <View>
          <Text style={styles.eyebrow}>YOUR BAG</Text>
          <Text style={styles.title}>
            {count} {count === 1 ? 'item' : 'items'}
          </Text>
        </View>
      </View>

      {isEmpty ? (
        <View style={styles.empty}>
          <View style={styles.emptyIcon}>
            <MaterialIcons name="shopping-bag" size={36} color={Colors.accent} />
          </View>
          <Text style={styles.emptyTitle}>Your bag is empty</Text>
          <Text style={styles.emptyBody}>
            Discover timeless pieces from our curated collection.
          </Text>
          <PrimaryButton
            label="Start shopping"
            onPress={() => router.push('/(tabs)/shop')}
            style={{ marginTop: Spacing.xl, alignSelf: 'stretch' }}
          />
        </View>
      ) : (
        <>
          <FlatList
            data={items}
            keyExtractor={(i) => i.id}
            contentContainerStyle={{
              paddingHorizontal: Spacing.lg,
              paddingBottom: 280,
            }}
            ItemSeparatorComponent={() => <View style={styles.sep} />}
            renderItem={({ item }) => (
              <View style={styles.row}>
                <Pressable
                  onPress={() => router.push(`/product/${item.product.id}`)}
                  style={styles.imageWrap}
                >
                  <Image
                    source={{ uri: item.product.image }}
                    style={styles.image}
                    contentFit="cover"
                  />
                </Pressable>
                <View style={styles.info}>
                  <View style={styles.infoTop}>
                    <View style={{ flex: 1, paddingRight: Spacing.md }}>
                      <Text style={styles.brand}>{item.product.brand}</Text>
                      <Text style={styles.name} numberOfLines={1}>
                        {item.product.name}
                      </Text>
                      <Text style={styles.meta}>
                        Size {item.size} · {item.color}
                      </Text>
                    </View>
                    <Pressable
                      onPress={() => removeFromCart(item.id)}
                      hitSlop={10}
                    >
                      <MaterialIcons name="close" size={18} color={Colors.textSubtle} />
                    </Pressable>
                  </View>

                  <View style={styles.infoBottom}>
                    <View style={styles.qty}>
                      <Pressable
                        onPress={() => updateQuantity(item.id, item.quantity - 1)}
                        style={styles.qtyBtn}
                      >
                        <MaterialIcons name="remove" size={16} color={Colors.text} />
                      </Pressable>
                      <Text style={styles.qtyValue}>{item.quantity}</Text>
                      <Pressable
                        onPress={() => updateQuantity(item.id, item.quantity + 1)}
                        style={styles.qtyBtn}
                      >
                        <MaterialIcons name="add" size={16} color={Colors.text} />
                      </Pressable>
                    </View>
                    <Text style={styles.price}>
                      ${item.product.price * item.quantity}
                    </Text>
                  </View>
                </View>
              </View>
            )}
          />

          <View style={styles.summary}>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Subtotal</Text>
              <Text style={styles.summaryValue}>${subtotal}</Text>
            </View>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Shipping</Text>
              <Text style={styles.summaryValue}>
                {shipping === 0 ? 'Free' : `$${shipping}`}
              </Text>
            </View>
            <View style={styles.divider} />
            <View style={styles.summaryRow}>
              <Text style={styles.totalLabel}>Total</Text>
              <Text style={styles.totalValue}>${total}</Text>
            </View>
            <PrimaryButton
              label="Proceed to checkout"
              onPress={() => router.push('/checkout')}
              style={{ marginTop: Spacing.lg }}
            />
          </View>
        </>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: Colors.background },
  header: {
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
    paddingBottom: Spacing.lg,
  },
  eyebrow: {
    ...Typography.caption,
    color: Colors.accent,
    letterSpacing: 2,
    marginBottom: 4,
  },
  title: { ...Typography.display, color: Colors.text },

  empty: {
    flex: 1,
    paddingHorizontal: Spacing.xl,
    alignItems: 'center',
    justifyContent: 'center',
  },
  emptyIcon: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: Colors.accentSoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: Spacing.lg,
  },
  emptyTitle: { ...Typography.title, color: Colors.text, marginBottom: Spacing.sm },
  emptyBody: {
    ...Typography.body,
    color: Colors.textSubtle,
    textAlign: 'center',
    lineHeight: 24,
  },

  row: { flexDirection: 'row', paddingVertical: Spacing.md },
  imageWrap: {
    width: 96,
    height: 120,
    borderRadius: Radius.md,
    overflow: 'hidden',
    backgroundColor: Colors.surfaceAlt,
  },
  image: { width: '100%', height: '100%' },
  info: { flex: 1, marginLeft: Spacing.md, justifyContent: 'space-between' },
  infoTop: { flexDirection: 'row', alignItems: 'flex-start' },
  brand: {
    ...Typography.caption,
    color: Colors.textSubtle,
    letterSpacing: 1.2,
    marginBottom: 2,
  },
  name: { ...Typography.body, color: Colors.text, marginBottom: 4 },
  meta: { ...Typography.small, color: Colors.textSubtle },
  infoBottom: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: Spacing.md,
  },
  qty: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: Radius.pill,
    backgroundColor: Colors.surface,
  },
  qtyBtn: {
    width: 30,
    height: 30,
    alignItems: 'center',
    justifyContent: 'center',
  },
  qtyValue: {
    ...Typography.bodyStrong,
    color: Colors.text,
    minWidth: 24,
    textAlign: 'center',
  },
  price: { ...Typography.bodyStrong, color: Colors.text },
  sep: { height: 1, backgroundColor: Colors.border },

  summary: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: Colors.surface,
    borderTopLeftRadius: Radius.lg,
    borderTopRightRadius: Radius.lg,
    paddingHorizontal: Spacing.xl,
    paddingTop: Spacing.lg,
    paddingBottom: Spacing.xl,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -8 },
    shadowOpacity: 0.06,
    shadowRadius: 16,
    elevation: 8,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
  },
  summaryLabel: { ...Typography.body, color: Colors.textSubtle },
  summaryValue: { ...Typography.body, color: Colors.text },
  totalLabel: { ...Typography.section, color: Colors.text },
  totalValue: { ...Typography.section, color: Colors.text },
  divider: {
    height: 1,
    backgroundColor: Colors.border,
    marginVertical: Spacing.sm,
  },
});
