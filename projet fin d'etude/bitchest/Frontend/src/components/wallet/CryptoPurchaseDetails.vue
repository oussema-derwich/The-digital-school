<template>
  <div>
    <!-- Affichage du statut de chargement -->
    <div v-if="loading" class="text-center text-gray-500 text-lg">
      Loading purchase details...
    </div>

    <!-- Affichage des erreurs -->
    <div v-else-if="error" class="text-center text-red-500 text-lg">
      <p>{{ error }}</p>
    </div>

    <!-- Affichage des détails d'achat -->
    <div v-else-if="purchases.length" class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Purchase Details</h2>
      <ul class="space-y-4">
        <li v-for="purchase in purchases" :key="purchase.id" class="p-4 border rounded-md">
          <p><strong>Date:</strong> {{ formatDate(purchase.date) }}</p>
          <p><strong>Quantity:</strong> {{ formatQuantity(purchase.quantity) }} units</p>
          <p><strong>Unit Price:</strong> {{ formatCurrency(purchase.unitPrice) }}</p>
          <p><strong>Total Price:</strong> {{ formatCurrency(purchase.totalPrice) }}</p>
        </li>
      </ul>
      <button @click="goBack" class="bg-bitchest-success text-black text-sm sm:text-base font-semibold mt-2 px-2 sm:px-4  sm:py-1 rounded-md hover:bg-green-300 transition duration-200 shadow-md w-full sm:w-auto text-center">
        Back
      </button>
    </div>

    <!-- Message si aucune donnée d'achat -->
    <div v-else class="text-center text-gray-600 text-lg">
      No purchases found for this cryptocurrency.
    </div>
  </div>
</template>

<script>
import cryptoPurshaseDetails from "@/services/cryptoPurshaseDetails"; // Import du service

export default {
  props: ["id"],
  data() {
    return {
      purchases: [],
      loading: true,
      error: null,
    };
  },
  async mounted() {
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        throw new Error("Token is missing. Please log in.");
      }

      // Récupérer l'ID de la crypto à partir de params de la route
      const cryptoId = this.$route.params.id;

      // Utiliser le service pour récupérer les achats
      this.purchases = await cryptoPurshaseDetails.fetchPurchases(cryptoId, token);
    } catch (err) {
      this.error = err.message;
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
      return parseFloat(quantity).toFixed(8);
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString("en-GB");
    },
    goBack() {
      this.$router.go(-1); // Retour à la page précédente
    },
  },
};
</script>
