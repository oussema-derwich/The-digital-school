<template>
  <div class="max-w-4xl mx-auto p-6">
    <WalletInfo />
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6 mt-12">
      Your Crypto Wallet
    </h1>

    <!-- Affichage du statut de chargement -->
    <div v-if="loading" class="text-center text-gray-500 text-lg">
      Loading wallet...
    </div>

    <!-- Affichage des erreurs -->
    <div v-else-if="error" class="text-center text-red-500 text-lg">
      <p>{{ error }}</p>
    </div>

    <!-- Affichage des cryptos dans le portefeuille -->
    <div
      v-else-if="paginatedCryptos.length"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="crypto in paginatedCryptos"
        :key="crypto.id"
        class="bg-white p-4 rounded-xl shadow-md border border-gray-200"
      >
        <!-- Image de la crypto -->
        <div class="flex justify-center mb-4">
          <img
            :src="crypto.imageUrl"
            alt="crypto image"
            class="w-10 h-10 object-contain"
          />
        </div>
        <h3 class="text-xl font-bold text-bitchest-success mb-2">
          {{ crypto.name }}
        </h3>

        <!-- Quantité -->
        <h4 class="text-[16px] font-bold">
          Quantity:
          <span class="text-gray-600"
            >{{ formatQuantity(crypto.quantity) }} units</span
          >
        </h4>

        <!-- Prix total -->
        <h3 class="text-xl font-bold mt-2">
          Total Price:
          <span class="text-gray-600">{{
            formatCurrency(crypto.totalPrice)
          }}</span>
        </h3>

        <!-- Bouton pour voir les détails d'achat -->
        <div class="mt-2">
          <button
            @click="viewPurchaseDetails(crypto.id)"
            class="text-bitchest-primary text-sm underline sm:text-base mt-2 mr-12 px-2 hover:text-bitchest-secondary"
          >
            Details
          </button>
          <!-- Link pour voir la vente-->
          <router-link
            @click="viewPurchaseDetails(crypto.id)"
            class="bg-bitchest-secondary text-white text-sm sm:text-base mt-2 px-4 py-1 rounded-md hover:bg-blue-500 transition duration-200 shadow-md w-full sm:w-auto text-center"
          >
            <i class="fas fa-euro-sign mr-1 text-white"></i> Sell
          </router-link>
        </div>
      </div>
    </div>

    <!-- Message si aucun crypto en portefeuille -->
    <div v-else class="text-center text-gray-600 text-lg">
      No cryptocurrencies found in your wallet.
    </div>

    <!-- Pagination -->
    <Pagination
      :currentPage="currentPage"
      :totalPages="totalPages"
      @goToPage="goToPage"
    />

    <router-link
      to="/dashboard/trading-market"
      class="flex items-center text-black mt-4"
    >
      <i class="fas fa-arrow-left mr-2"></i> Back
    </router-link>
  </div>
</template>

<script>
import WalletInfo from "./WalletInfo.vue";
import walletService from "@/services/walletService"; // Importer le service
import Pagination from "@/components/Pagination.vue"; // Importer la pagination

export default {
  components: {
    WalletInfo,
    Pagination,
  },
  data() {
    return {
      cryptos: [], // Stocke les cryptos du portefeuille
      loading: true, // Indicateur de chargement
      error: null, // Stocke les erreurs
      currentPage: 1, // Page actuelle
      limit: 3, // Nombre de cryptos par page
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.cryptos.length / this.limit);
    },
    paginatedCryptos() {
      const startIndex = (this.currentPage - 1) * this.limit;
      const endIndex = startIndex + this.limit;
      return this.cryptos.slice(startIndex, endIndex);
    },
  },
  async mounted() {
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        throw new Error("Token is missing. Please log in.");
      }

      // Récupérer les cryptos du portefeuille via le service
      this.cryptos = await walletService.getWalletCryptos(token);
      console.log(this.cryptos);
      // Ajouter l'URL complète pour l'image et les données nécessaires
      this.cryptos = this.cryptos.map((crypto) => ({
        ...crypto,
        imageUrl: `http://127.0.0.1:8000/storage/cryptos/${crypto.image}`, // Préfixe l'URL avec la base de ton serveur
      }));
    } catch (err) {
      this.error = err.message || "Failed to load wallet.";
    } finally {
      this.loading = false;
    }
  },
  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "EUR",
      }).format(value);
    },
    formatQuantity(quantity) {
      return parseFloat(quantity).toFixed(8); // Affichage avec 8 décimales comme dans la réponse API
    },
    viewPurchaseDetails(cryptoId) {
      if (!cryptoId) {
        console.error("Error: cryptoId is missing");
        return;
      }
      this.$router.push({
        name: "cryptoPurchaseDetails",
        params: { id: cryptoId },
      });
    },
    goToPage(page) {
      this.currentPage = page;
    },
  },
};
</script>
