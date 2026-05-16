// Powered by OnSpace.AI
export interface Product {
  id: string;
  name: string;
  brand: string;
  price: number;
  originalPrice?: number;
  image: string;
  gallery: string[];
  category: 'Men' | 'Women' | 'Unisex';
  description: string;
  sizes: string[];
  colors: string[];
  rating: number;
  isNew?: boolean;
  tag?: string;
}

const PRODUCTS: Product[] = [
  {
    id: 'p1',
    name: 'Oversized Linen Shirt',
    brand: 'ATELIER NOIR',
    price: 89,
    originalPrice: 120,
    image: 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=900&q=80',
      'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=900&q=80',
      'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=900&q=80',
    ],
    category: 'Unisex',
    description: 'Crafted from 100% European linen, this oversized shirt drapes effortlessly. A timeless silhouette designed for everyday refinement.',
    sizes: ['XS', 'S', 'M', 'L', 'XL'],
    colors: ['Sand', 'Ivory', 'Charcoal'],
    rating: 4.8,
    isNew: true,
    tag: 'New In',
  },
  {
    id: 'p2',
    name: 'Wool Tailored Trousers',
    brand: 'MAISON BLANC',
    price: 145,
    image: 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1542272604-787c3835535d?w=900&q=80',
      'https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?w=900&q=80',
    ],
    category: 'Men',
    description: 'Italian wool trousers with a relaxed straight leg. Pleated front for a refined drape and hidden side adjusters.',
    sizes: ['28', '30', '32', '34', '36'],
    colors: ['Black', 'Navy', 'Camel'],
    rating: 4.6,
  },
  {
    id: 'p3',
    name: 'Cashmere Crewneck',
    brand: 'STILLE',
    price: 220,
    image: 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=900&q=80',
      'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&q=80',
    ],
    category: 'Women',
    description: 'Pure Mongolian cashmere knit in a classic crewneck. Lightweight warmth with a buttery soft finish.',
    sizes: ['XS', 'S', 'M', 'L'],
    colors: ['Cream', 'Stone', 'Black'],
    rating: 4.9,
    tag: 'Bestseller',
  },
  {
    id: 'p4',
    name: 'Pleated Midi Skirt',
    brand: 'ATELIER NOIR',
    price: 110,
    image: 'https://images.unsplash.com/photo-1583496661160-fb5886a13d44?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1583496661160-fb5886a13d44?w=900&q=80',
    ],
    category: 'Women',
    description: 'Fluid pleated midi skirt with a high waist. Crafted from recycled satin with elegant movement.',
    sizes: ['XS', 'S', 'M', 'L'],
    colors: ['Black', 'Bronze'],
    rating: 4.5,
  },
  {
    id: 'p5',
    name: 'Structured Wool Coat',
    brand: 'MAISON BLANC',
    price: 420,
    originalPrice: 520,
    image: 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=900&q=80',
      'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=900&q=80',
    ],
    category: 'Unisex',
    description: 'Double-breasted overcoat in pure virgin wool. Architectural shoulders with a soft, generous lapel.',
    sizes: ['S', 'M', 'L', 'XL'],
    colors: ['Camel', 'Black'],
    rating: 4.7,
    isNew: true,
  },
  {
    id: 'p6',
    name: 'Minimal Cotton Tee',
    brand: 'STILLE',
    price: 45,
    image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=900&q=80',
    ],
    category: 'Unisex',
    description: 'Heavyweight organic cotton tee. Boxy fit with a clean ribbed crew neck.',
    sizes: ['XS', 'S', 'M', 'L', 'XL'],
    colors: ['White', 'Black', 'Sand', 'Sage'],
    rating: 4.4,
  },
  {
    id: 'p7',
    name: 'High-Waist Denim',
    brand: 'ATELIER NOIR',
    price: 135,
    image: 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=900&q=80',
    ],
    category: 'Women',
    description: 'Rigid Japanese selvedge denim with a vintage straight leg. Made to soften and fade with wear.',
    sizes: ['24', '26', '28', '30', '32'],
    colors: ['Indigo', 'Washed'],
    rating: 4.6,
  },
  {
    id: 'p8',
    name: 'Silk Blouse',
    brand: 'STILLE',
    price: 175,
    image: 'https://images.unsplash.com/photo-1485518882345-15568b007407?w=900&q=80',
    gallery: [
      'https://images.unsplash.com/photo-1485518882345-15568b007407?w=900&q=80',
    ],
    category: 'Women',
    description: 'Mulberry silk blouse with a relaxed collar and mother-of-pearl buttons.',
    sizes: ['XS', 'S', 'M', 'L'],
    colors: ['Ivory', 'Black', 'Blush'],
    rating: 4.8,
    tag: 'Editor\u2019s Pick',
  },
];

export const Categories = ['All', 'Women', 'Men', 'Unisex'] as const;
export type CategoryKey = typeof Categories[number];

export const Brands = ['ATELIER NOIR', 'MAISON BLANC', 'STILLE'];

export const productService = {
  list(): Product[] {
    return PRODUCTS;
  },
  byId(id: string): Product | undefined {
    return PRODUCTS.find((p) => p.id === id);
  },
  filter(category: CategoryKey, query: string = ''): Product[] {
    const q = query.trim().toLowerCase();
    return PRODUCTS.filter((p) => {
      const inCategory = category === 'All' ? true : p.category === category;
      const inQuery = !q
        ? true
        : p.name.toLowerCase().includes(q) ||
          p.brand.toLowerCase().includes(q);
      return inCategory && inQuery;
    });
  },
  featured(): Product[] {
    return PRODUCTS.filter((p) => p.isNew || p.tag === 'Bestseller').slice(0, 4);
  },
  newArrivals(): Product[] {
    return PRODUCTS.filter((p) => p.isNew);
  },
};
