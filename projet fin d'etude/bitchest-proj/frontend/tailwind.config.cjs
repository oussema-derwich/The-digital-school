/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0B63F6',
        'primary-dark': '#0A2B6B',
        secondary: '#1E1E1E',
        background: '#F7F9FC',
        success: '#16A34A',
        danger: '#DC2626',
        accent: '#4C8BFF'
      },
      fontFamily: {
        sans: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans],
        heading: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans]
      },
      borderRadius: {
        md: '8px'
      },
      boxShadow: {
        card: '0 4px 14px rgba(0,0,0,0.06)'
      },
      transitionProperty: {
        DEFAULT: 'all'
      },
      transitionDuration: {
        DEFAULT: '150ms',
        '250': '250ms'
      },
      spacing: {
        'nav-gap': '32px'
      },
      height: {
        'navbar': '64px'
      }
    }
  },
  plugins: []
}