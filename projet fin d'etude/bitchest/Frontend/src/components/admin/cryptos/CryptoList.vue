<template>
  <div>
    <!-- Heading -->
    <h1
      class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center text-bitchest-black mb-6 lg:mb-8"
    >
      Crypto Management
    </h1>

    <!-- Bouton d'ajout de crypto -->
    <div class="flex justify-center mb-4" v-if="!isClient">
      <router-link
        to="/admin/crypto/add"
        class="bg-bitchest-success text-black text-base font-semibold px-6 py-3 rounded-md hover:bg-green-300 transition duration-200 shadow-md w-full sm:w-auto text-center"
      >
        Add crypto
      </router-link>
    </div>

    <!-- Conteneur principal -->
    <div class="flex flex-col lg:flex-row p-4 md:p-6 lg:p-8">
      <div v-if="loading" class="text-center w-full">
        <Loader />
      </div>
      <div v-else-if="error" class="text-center w-full text-red-500">
        <p>{{ error }}</p>
      </div>

      <!-- Liste des cryptos -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 w-full"
      >
        <div
          v-for="crypto in paginatedCryptos"
          :key="crypto.id"
          class="bg-bitchest-white w-full h-auto border border-gray-300 rounded-2xl p-6 text-center shadow-md transition-transform duration-300 hover:scale-105 hover:shadow-lg"
        >
          <img
            :src="getImageUrl(crypto.image_url)"
            :alt="crypto.name"
            class="mx-auto mb-4 w-16 md:w-20 lg:w-24 h-16 md:h-20 lg:h-24 object-contain"
          />
          <h3 class="text-bitchest-success font-bold text-lg">
            {{ crypto.name }}
          </h3>
          <h4 class="text-sm md:text-base lg:text-lg">
            {{ crypto.currentPrice }} €
          </h4>

          <div class="mt-2 space-x-4">
            <router-link
              :to="`/dashboard/crypto/${crypto.id}`"
              class="text-yellow-500 hover:text-yellow-600 transition duration-150"
            >
              View <i class="far fa-eye text-bitchest-secondary"></i>
            </router-link>

            <router-link
              v-if="isClient"
              to="#"
              @click.prevent="openBuyModal(crypto)"
              class="text-yellow-500 hover:text-yellow-600 transition duration-150"
            >
              Buy <i class="fas fa-cart-plus text-bitchest-success"></i>
            </router-link>

            <router-link
              v-else
              :to="`/admin/crypto/${crypto.id}/edit`"
              class="text-yellow-500 hover:text-yellow-600 transition duration-150"
            >
              Edit <i class="fas fa-edit text-bitchest-success"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      :currentPage="currentPage"
      :totalPages="totalPages"
      @goToPage="goToPage"
    />

    <!-- Modale pour l'achat de crypto -->
    <div
      v-if="showBuyModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md mx-4 sm:w-96 relative"
      >
        <button
          @click="showBuyModal = false"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
        >
          ✖
        </button>

        <h2 class="text-lg font-semibold mb-4 text-center">
          Buy {{ selectedCrypto.name }}
        </h2>

        <label class="block text-gray-700 mb-2">Quantity:</label>
        <input
          v-model="quantity"
          type="number"
          min="0.01"
          step="0.01"
          class="w-full border rounded p-2 mb-4"
        />

        <div class="flex justify-end space-x-2">
          <button
            @click="showBuyModal = false"
            class="bg-gray-400 px-4 py-2 rounded text-white"
          >
            Cancel
          </button>
          <button
            @click="buyCrypto()"
            class="bg-bitchest-success px-4 py-2 rounded text-white"
          >
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Loader from "@/components/utils/Loader.vue";
import cryptoService from "../../../services/cryptoService";
import Pagination from "../../Pagination.vue";
import { useToast } from "vue-toastification";

export default {
  props: {
    isClient: Boolean,
  },
  data() {
    return {
      cryptos: [],
      paginatedCryptos: [],
      loading: true,
      error: null,
      limit: 6,
      currentPage: 1,
      totalPages: 0,
      showBuyModal: false,
      selectedCrypto: null,
      quantity: 0,
      toast: useToast(),
    };
  },
  methods: {
    async fetchCryptos() {
      try {
        this.cryptos = await cryptoService.fetchCryptos();
        console.log(this.cryptos);
        this.totalPages = Math.ceil(this.cryptos.length / this.limit);
        this.updatePaginatedCryptos();
      } catch (err) {
        this.error = "Failed to load cryptos.";
      } finally {
        this.loading = false;
      }
    },
    updatePaginatedCryptos() {
      const startIndex = (this.currentPage - 1) * this.limit;
      const endIndex = startIndex + this.limit;
      this.paginatedCryptos = this.cryptos.slice(startIndex, endIndex);
    },
    goToPage(page) {
      this.currentPage = page;
      this.updatePaginatedCryptos();
    },
    getImageUrl(imagePath) {
      return `http://127.0.0.1:8000${imagePath}`;
    },
    openBuyModal(crypto) {
      this.selectedCrypto = crypto;
      this.showBuyModal = true;
    },
    async buyCrypto() {
      if (!this.quantity || this.quantity <= 0) {
        this.toast.error("Invalid quantity.");
        return;
      }

      try {
        const token = localStorage.getItem("token");
        if (!token) {
          this.toast.error("Token is missing. Please log in.");
          return;
        }

        await cryptoService.buyCrypto(
          this.selectedCrypto.id,
          this.quantity,
          this.selectedCrypto.currentPrice,
          token
        );

        this.toast.success("Purchase successful!");
        this.showBuyModal = false;
        this.$router.push("/dashboard/wallet");
      } catch (error) {
        this.toast.error(error.message || "Purchase failed!");
      }
    },
  },
  components: {
    Pagination,
    Loader,
  },
  mounted() {
    this.fetchCryptos();
  },
};
</script>
