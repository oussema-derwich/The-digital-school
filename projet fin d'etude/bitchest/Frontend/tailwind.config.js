/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        "bitchest-primary": "#38618C",
        "bitchest-secondary": "#35A7FF",
        "bitchest-white": "#FFFFFF",
        "bitchest-black": "#434445",
        "bitchest-alert": "#FF5964",
        "bitchest-success": "#01FF19",
        "sidebar-bg": "F9F9F9",
        lightGray: "#F8F8F8",
      },
      boxShadow: {
        "text-shadow": "0 4px 6px rgba(0, 0, 0, 0.1)",
      },

      fontFamily: {
        bitchest: ["Celias", "sans-serif"],
      },
      screens: {
        xs: "480px",
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1280px",
        "2xl": "1536px",
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
};
