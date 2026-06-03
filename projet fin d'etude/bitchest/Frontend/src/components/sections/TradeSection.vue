<template>
  <section
    id="trade"
    class="text-black flex items-center justify-center bg-lightGray p-6 min-h-screen"
  >
    <div
      class="flex flex-col lg:flex-row p-4 sm:p-5 md:p-6 lg:p-5 w-full max-w-6xl"
    >
      <!-- Section de texte -->
      <div class="w-full lg:w-1/3 lg:ml-10 lg:mr-16 mb-8 lg:mb-0">
        <h1
          class="text-2xl sm:text-3xl md:text-[2.5rem] lg:text-[3rem] font-bold mt-4 mb-6 lg:mt-7 lg:mb-7"
        >
          CryptoCurrency
        </h1>
        <p
          class="text-sm sm:text-base md:text-lg lg:text-[1.5rem] leading-6 sm:leading-7 md:leading-[2rem] lg:leading-[2.625rem] text-[#262626] mb-6 lg:mb-10"
        >
          Explore top cryptos like Bitcoin, Ethereum and Grow your portfolio
          automatically with daily, weekly, or monthly trades.
        </p>
        <custom-button
          class="text-bitchest-white font-bold text-lg sm:text-xl md:text-2xl lg:text-3xl border-0 rounded-lg lg:rounded-[20px] bg-bitchest-success px-6 py-3 sm:w-[160px] md:w-[180px] lg:w-[200px] mx-auto lg:mx-0 shadow-md flex justify-center hover:bg-gray-200 hover:text-bitchest-black transition-colors duration-300"
          @click="goToLogin"
        >
          <span style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2)"
            >See More</span
          >
        </custom-button>
      </div>

      <!-- Section des cartes -->
      <div v-if="loading" class="text-center w-full">
        <Loader />
      </div>

      <div v-else-if="error" class="text-center w-full text-red-500">
        <p>{{ error }}</p>
      </div>

      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 lg:gap-10 w-full"
      >
        <!-- Carte individuelle dynamique -->
        <div
          v-for="crypto in cryptos.slice(0, 6)"
          :key="crypto.crypto_name"
          class="bg-white w-full max-w-[220px] mx-auto h-[180px] sm:h-[190px] border border-gray-300 rounded-[1.5rem] p-4 sm:p-5 lg:p-6 text-center shadow-md transition-transform duration-300 ease-in-out hover:scale-105 hover:shadow-lg"
        >
          <img
            :src="getImageUrl(crypto.image_url)"
            :alt="crypto.crypto_name"
            class="mx-auto mb-4 w-12 h-12 object-contain"
          />
          <h3
            class="text-bitchest-success font-bold text-base sm:text-lg md:text-xl lg:text-2xl"
          >
            {{ crypto.name }}
          </h3>
          <h4 class="text-sm sm:text-base md:text-lg lg:text-xl">
            {{ formatNumber(crypto.currentPrice) }} $
          </h4>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Loader from "../utils/Loader.vue";
import cryptoService from "../../services/cryptoService";
export default {
  components: {
    Loader,
  },
  data() {
    return {
      cryptos: [],
      loading: true,
      error: null,
    };
  },
  methods: {
    formatNumber(value) {
      if (!value) return "0";
      return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
        useGrouping: true,
      })
        .format(parseFloat(value))
        .replace(/\s/g, " ");
    },

    goToLogin() {
      this.$router.push({ name: "login" });
    },
    async fetchCryptos() {
      try {
        this.cryptos = await cryptoService.fetchCryptos();
      } catch (err) {
        this.error = "Failed to load cryptos. Please try again later.";
      } finally {
        this.loading = false;
      }
    },
    getImageUrl(imagePath) {
      return `http://127.0.0.1:8000${imagePath}`;
    },
  },
  mounted() {
    this.fetchCryptos();
  },
};
</script>
