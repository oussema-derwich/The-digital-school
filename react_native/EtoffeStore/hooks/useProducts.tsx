// Powered by OnSpace.AI
import { useMemo, useState } from 'react';
import { CategoryKey, productService } from '@/services/products';

export function useProducts() {
  const [category, setCategory] = useState<CategoryKey>('All');
  const [query, setQuery] = useState<string>('');

  const products = useMemo(
    () => productService.filter(category, query),
    [category, query]
  );

  const featured = useMemo(() => productService.featured(), []);
  const newArrivals = useMemo(() => productService.newArrivals(), []);

  return {
    products,
    featured,
    newArrivals,
    category,
    setCategory,
    query,
    setQuery,
  };
}
