import { fileURLToPath, URL } from "node:url";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vueJsx from "@vitejs/plugin-vue-jsx";
import vueDevTools from "vite-plugin-vue-devtools";





// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueJsx(),
       // Add vueDevTools only in development mode
       process.env.NODE_ENV === "development" ? vueDevTools() : null,
      ].filter(Boolean), // Remove null if vueDevTools is not added
  resolve: {
    alias: {
     "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});

