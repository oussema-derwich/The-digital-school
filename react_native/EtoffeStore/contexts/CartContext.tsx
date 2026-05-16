// Powered by OnSpace.AI
import React, { createContext, ReactNode, useCallback, useMemo, useState } from 'react';
import { Product } from '@/services/products';

export interface CartItem {
  id: string;
  product: Product;
  size: string;
  color: string;
  quantity: number;
}

export interface CartContextType {
  items: CartItem[];
  addToCart: (product: Product, size: string, color: string, quantity?: number) => void;
  updateQuantity: (id: string, quantity: number) => void;
  removeFromCart: (id: string) => void;
  clearCart: () => void;
  count: number;
  subtotal: number;
  shipping: number;
  total: number;
}

export const CartContext = createContext<CartContextType | undefined>(undefined);

const SHIPPING_FEE = 12;
const FREE_SHIPPING_THRESHOLD = 200;

export function CartProvider({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);

  const addToCart = useCallback(
    (product: Product, size: string, color: string, quantity: number = 1) => {
      setItems((prev) => {
        const itemId = `${product.id}-${size}-${color}`;
        const existing = prev.find((i) => i.id === itemId);
        if (existing) {
          return prev.map((i) =>
            i.id === itemId ? { ...i, quantity: i.quantity + quantity } : i
          );
        }
        return [...prev, { id: itemId, product, size, color, quantity }];
      });
    },
    []
  );

  const updateQuantity = useCallback((id: string, quantity: number) => {
    setItems((prev) => {
      if (quantity <= 0) return prev.filter((i) => i.id !== id);
      return prev.map((i) => (i.id === id ? { ...i, quantity } : i));
    });
  }, []);

  const removeFromCart = useCallback((id: string) => {
    setItems((prev) => prev.filter((i) => i.id !== id));
  }, []);

  const clearCart = useCallback(() => setItems([]), []);

  const { count, subtotal, shipping, total } = useMemo(() => {
    const c = items.reduce((acc, i) => acc + i.quantity, 0);
    const sub = items.reduce((acc, i) => acc + i.product.price * i.quantity, 0);
    const ship = sub === 0 || sub >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_FEE;
    return { count: c, subtotal: sub, shipping: ship, total: sub + ship };
  }, [items]);

  const value: CartContextType = {
    items,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
    count,
    subtotal,
    shipping,
    total,
  };

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}
