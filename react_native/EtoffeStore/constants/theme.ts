// Powered by OnSpace.AI
export const Colors = {
  background: '#FAF8F5',
  surface: '#FFFFFF',
  surfaceAlt: '#F2EEE8',
  text: '#111111',
  textSubtle: '#6B6B6B',
  textMuted: '#A0A0A0',
  border: '#E7E2DA',
  primary: '#1A1A1A',
  accent: '#B8916A',
  accentSoft: '#E8DCC8',
  success: '#3B7A57',
  danger: '#B0413E',
  overlay: 'rgba(17,17,17,0.45)',
};

export const Spacing = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 24,
  xxl: 32,
  xxxl: 48,
};

export const Radius = {
  sm: 6,
  md: 12,
  lg: 20,
  pill: 999,
};

export const Typography = {
  display: { fontSize: 34, fontWeight: '700' as const, letterSpacing: -0.5 },
  title: { fontSize: 22, fontWeight: '700' as const },
  section: { fontSize: 18, fontWeight: '600' as const },
  body: { fontSize: 16, fontWeight: '400' as const },
  bodyStrong: { fontSize: 16, fontWeight: '600' as const },
  small: { fontSize: 13, fontWeight: '400' as const },
  caption: { fontSize: 12, fontWeight: '500' as const, letterSpacing: 0.6 },
};

export const Shadow = {
  card: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.06,
    shadowRadius: 12,
    elevation: 3,
  },
  soft: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 1,
  },
};
