// Powered by OnSpace.AI
import React, { useState } from 'react';
import {
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Colors, Radius, Spacing, Typography } from '@/constants/theme';
import { useCart } from '@/hooks/useCart';
import { PrimaryButton } from '@/components/ui/PrimaryButton';
import { ScreenHeader } from '@/components/layout/ScreenHeader';
import { useAlert } from '@/template';

const PAYMENT_METHODS = [
  { id: 'card', label: 'Credit Card', icon: 'credit-card' as const },
  { id: 'apple', label: 'Apple Pay', icon: 'phone-iphone' as const },
  { id: 'paypal', label: 'PayPal', icon: 'account-balance-wallet' as const },
];

const SHIPPING_OPTIONS = [
  { id: 'standard', label: 'Standard', meta: '5-7 business days', price: 0 },
  { id: 'express', label: 'Express', meta: '2-3 business days', price: 18 },
];

export default function CheckoutScreen() {
  const router = useRouter();
  const { items, subtotal, total, clearCart } = useCart();
  const { showAlert } = useAlert();

  const [email, setEmail] = useState('');
  const [name, setName] = useState('');
  const [address, setAddress] = useState('');
  const [city, setCity] = useState('');
  const [zip, setZip] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('card');
  const [shipping, setShipping] = useState('standard');
  const [loading, setLoading] = useState(false);

  const shippingPrice = SHIPPING_OPTIONS.find((s) => s.id === shipping)?.price ?? 0;
  const finalTotal = subtotal + shippingPrice;

  const isValid = email && name && address && city && zip;

  const handlePlaceOrder = () => {
    if (!isValid) {
      showAlert('Missing details', 'Please complete all shipping fields.');
      return;
    }
    setLoading(true);
    setTimeout(() => {
      setLoading(false);
      clearCart();
      showAlert(
        'Order confirmed',
        `Thank you ${name.split(' ')[0]}. A confirmation has been sent to ${email}.`,
        [
          {
            text: 'Continue shopping',
            onPress: () => router.replace('/(tabs)'),
          },
        ]
      );
    }, 1200);
  };

  return (
    <SafeAreaView style={styles.container} edges={['top']}>
      <ScreenHeader title="Checkout" showBack />
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={{ flex: 1 }}
      >
        <ScrollView
          showsVerticalScrollIndicator={false}
          contentContainerStyle={{ paddingBottom: 200 }}
          keyboardShouldPersistTaps="handled"
        >
          {/* Steps */}
          <View style={styles.steps}>
            {['Bag', 'Details', 'Payment'].map((s, i) => (
              <View key={s} style={styles.stepItem}>
                <View style={[styles.stepDot, i <= 2 && styles.stepDotActive]}>
                  <Text style={styles.stepNumber}>{i + 1}</Text>
                </View>
                <Text style={styles.stepLabel}>{s}</Text>
              </View>
            ))}
          </View>

          {/* Contact */}
          <Section title="Contact">
            <Field
              label="Email"
              value={email}
              onChangeText={setEmail}
              placeholder="you@example.com"
              keyboardType="email-address"
            />
          </Section>

          {/* Shipping address */}
          <Section title="Shipping address">
            <Field
              label="Full name"
              value={name}
              onChangeText={setName}
              placeholder="Jane Doe"
            />
            <Field
              label="Address"
              value={address}
              onChangeText={setAddress}
              placeholder="123 Atelier Street"
            />
            <View style={styles.fieldRow}>
              <View style={{ flex: 2, marginRight: Spacing.md }}>
                <Field
                  label="City"
                  value={city}
                  onChangeText={setCity}
                  placeholder="Paris"
                />
              </View>
              <View style={{ flex: 1 }}>
                <Field
                  label="Postal"
                  value={zip}
                  onChangeText={setZip}
                  placeholder="75001"
                  keyboardType="number-pad"
                />
              </View>
            </View>
          </Section>

          {/* Shipping method */}
          <Section title="Shipping method">
            {SHIPPING_OPTIONS.map((opt) => (
              <Pressable
                key={opt.id}
                onPress={() => setShipping(opt.id)}
                style={[
                  styles.optionCard,
                  shipping === opt.id && styles.optionCardActive,
                ]}
              >
                <View style={styles.radio}>
                  {shipping === opt.id ? <View style={styles.radioInner} /> : null}
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.optionLabel}>{opt.label}</Text>
                  <Text style={styles.optionMeta}>{opt.meta}</Text>
                </View>
                <Text style={styles.optionPrice}>
                  {opt.price === 0 ? 'Free' : `$${opt.price}`}
                </Text>
              </Pressable>
            ))}
          </Section>

          {/* Payment */}
          <Section title="Payment method">
            <View style={styles.paymentRow}>
              {PAYMENT_METHODS.map((p) => (
                <Pressable
                  key={p.id}
                  onPress={() => setPaymentMethod(p.id)}
                  style={[
                    styles.payCard,
                    paymentMethod === p.id && styles.payCardActive,
                  ]}
                >
                  <MaterialIcons
                    name={p.icon}
                    size={22}
                    color={paymentMethod === p.id ? Colors.surface : Colors.text}
                  />
                  <Text
                    style={[
                      styles.payText,
                      paymentMethod === p.id && { color: Colors.surface },
                    ]}
                  >
                    {p.label}
                  </Text>
                </Pressable>
              ))}
            </View>
          </Section>

          {/* Summary */}
          <Section title="Order summary">
            <View style={styles.summaryCard}>
              <View style={styles.row}>
                <Text style={styles.rowLabel}>{items.length} items</Text>
                <Text style={styles.rowValue}>${subtotal}</Text>
              </View>
              <View style={styles.row}>
                <Text style={styles.rowLabel}>Shipping</Text>
                <Text style={styles.rowValue}>
                  {shippingPrice === 0 ? 'Free' : `$${shippingPrice}`}
                </Text>
              </View>
              <View style={styles.divider} />
              <View style={styles.row}>
                <Text style={styles.totalLabel}>Total</Text>
                <Text style={styles.totalValue}>${finalTotal}</Text>
              </View>
            </View>
          </Section>
        </ScrollView>

        <View style={styles.footer}>
          <PrimaryButton
            label={loading ? 'Processing\u2026' : `Place order · $${finalTotal}`}
            onPress={handlePlaceOrder}
            loading={loading}
            disabled={items.length === 0}
          />
          <Text style={styles.secure}>
            <MaterialIcons name="lock" size={12} color={Colors.textSubtle} /> Secure
            checkout · SSL encrypted
          </Text>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <View style={styles.section}>
      <Text style={styles.sectionTitle}>{title}</Text>
      {children}
    </View>
  );
}

function Field({
  label,
  value,
  onChangeText,
  placeholder,
  keyboardType,
}: {
  label: string;
  value: string;
  onChangeText: (t: string) => void;
  placeholder?: string;
  keyboardType?: 'default' | 'email-address' | 'number-pad';
}) {
  return (
    <View style={styles.field}>
      <Text style={styles.fieldLabel}>{label}</Text>
      <TextInput
        value={value}
        onChangeText={onChangeText}
        placeholder={placeholder}
        placeholderTextColor={Colors.textMuted}
        style={styles.input}
        keyboardType={keyboardType ?? 'default'}
        autoCapitalize={keyboardType === 'email-address' ? 'none' : 'sentences'}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: Colors.background },

  steps: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: Spacing.lg,
    paddingHorizontal: Spacing.xl,
  },
  stepItem: { alignItems: 'center' },
  stepDot: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: Colors.surfaceAlt,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 6,
  },
  stepDotActive: { backgroundColor: Colors.primary },
  stepNumber: { color: Colors.surface, fontSize: 12, fontWeight: '700' },
  stepLabel: { ...Typography.caption, color: Colors.textSubtle, letterSpacing: 1 },

  section: { paddingHorizontal: Spacing.lg, marginBottom: Spacing.xl },
  sectionTitle: {
    ...Typography.section,
    color: Colors.text,
    marginBottom: Spacing.md,
  },

  field: { marginBottom: Spacing.md },
  fieldLabel: {
    ...Typography.caption,
    color: Colors.textSubtle,
    letterSpacing: 1,
    marginBottom: 6,
  },
  input: {
    height: 52,
    borderRadius: Radius.md,
    backgroundColor: Colors.surface,
    borderWidth: 1,
    borderColor: Colors.border,
    paddingHorizontal: Spacing.lg,
    ...Typography.body,
    color: Colors.text,
  },
  fieldRow: { flexDirection: 'row' },

  optionCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: Spacing.lg,
    borderRadius: Radius.md,
    backgroundColor: Colors.surface,
    borderWidth: 1,
    borderColor: Colors.border,
    marginBottom: Spacing.sm,
  },
  optionCardActive: { borderColor: Colors.primary, backgroundColor: Colors.surface },
  radio: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 1.5,
    borderColor: Colors.text,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: Spacing.md,
  },
  radioInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: Colors.primary,
  },
  optionLabel: { ...Typography.bodyStrong, color: Colors.text },
  optionMeta: { ...Typography.small, color: Colors.textSubtle, marginTop: 2 },
  optionPrice: { ...Typography.bodyStrong, color: Colors.text },

  paymentRow: { flexDirection: 'row', gap: Spacing.sm },
  payCard: {
    flex: 1,
    height: 80,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: Radius.md,
    backgroundColor: Colors.surface,
    borderWidth: 1,
    borderColor: Colors.border,
    gap: 6,
  },
  payCardActive: { backgroundColor: Colors.primary, borderColor: Colors.primary },
  payText: { ...Typography.caption, color: Colors.text, fontWeight: '600' },

  summaryCard: {
    backgroundColor: Colors.surface,
    borderRadius: Radius.md,
    padding: Spacing.lg,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
  },
  rowLabel: { ...Typography.body, color: Colors.textSubtle },
  rowValue: { ...Typography.body, color: Colors.text },
  divider: {
    height: 1,
    backgroundColor: Colors.border,
    marginVertical: Spacing.sm,
  },
  totalLabel: { ...Typography.section, color: Colors.text },
  totalValue: { ...Typography.section, color: Colors.text },

  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: Colors.surface,
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.lg,
    paddingBottom: Spacing.xl,
    borderTopWidth: 1,
    borderTopColor: Colors.border,
  },
  secure: {
    ...Typography.caption,
    color: Colors.textSubtle,
    textAlign: 'center',
    marginTop: Spacing.md,
  },
});
