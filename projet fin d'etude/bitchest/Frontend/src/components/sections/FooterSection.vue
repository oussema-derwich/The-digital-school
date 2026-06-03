<template>
  <footer class="bg-gray-900 text-white py-10 relative">
    <div
      class="container mx-auto flex flex-col md:flex-row justify-between items-center px-6"
    >
      <!-- Logo et description -->
      <div class="text-center md:text-left mb-6 md:mb-0">
        <h2 class="text-2xl font-bold">Bitchest</h2>
        <p class="text-gray-400 text-sm mt-2 w-[90%] md:w-[70%]">
          Trade and grow your cryptocurrency portfolio securely with Bitchest.
        </p>
      </div>

      <!-- Liens de navigation -->
      <nav class="mb-6 md:mb-0">
        <ul class="flex flex-wrap justify-center gap-6">
          <li>
            <a
              href="#"
              class="text-gray-300 hover:text-bitchest-success transition"
              >Home</a
            >
          </li>
          <li>
            <a
              href="#"
              class="text-gray-300 hover:text-bitchest-success transition"
              >About</a
            >
          </li>
          <li>
            <a
              href="#"
              class="text-gray-300 hover:text-bitchest-success transition"
              >Cryptos</a
            >
          </li>
          <li>
            <a
              href="#"
              class="text-gray-300 hover:text-bitchest-success transition"
              >Contact</a
            >
          </li>
        </ul>
      </nav>
    </div>

    <!-- Copyright Dynamique -->
    <div
      class="text-center text-gray-500 text-sm mt-6 border-t border-gray-700 pt-4"
    >
      © {{ currentYear }} Bitchest. All rights reserved.
    </div>

    <!-- Bouton de retour en haut -->
    <button
      v-show="showScrollTop"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 bg-bitchest-success text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 ease-in-out transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
    >
      <!-- Icône "chevron-up" de Heroicons -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M5 15l7-7 7 7"
        />
      </svg>
    </button>
  </footer>
</template>
<script>
export default {
  data() {
    return {
      currentYear: new Date().getFullYear(), // Année dynamique
      showScrollTop: false,
    };
  },
  mounted() {
    window.addEventListener("scroll", this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener("scroll", this.handleScroll);
  },
  methods: {
    handleScroll() {
      this.showScrollTop = window.scrollY > 300; // Afficher après 300px de scroll
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: "smooth" });
    },
  },
};
</script>
