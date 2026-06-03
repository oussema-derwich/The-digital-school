<template>
  <div class="flex flex-col space-y-4">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">My Wallet</h2>
    <div v-if="loading" class="text-gray-600">Loading...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <div v-else class="text-gray-700">
      <p><strong>Balance :</strong> <span class="font-medium">{{ balance }} €</span></p>
      <p v-if="totalPurchases !== null"><strong>Total Purchases :</strong> <span class="font-medium">{{ formatCurrency(totalPurchases) }}</span></p>
      <!-- <p><strong>Public Address :</strong> <span class="break-all text-blue-600">{{ publicAddress }}</span></p>-->
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import walletService from "@/services/walletService"; // Importer le service

export default {
  data() {
    return {
      balance: 0,
      publicAddress: '',
      error: null,
      loading: false,
      totalPurchases: null, // Ajout de la variable pour le total des achats
    };
  },
  mounted() {
    this.fetchWalletInfo();
    this.fetchTotalPurchases(); // Récupérer le total des achats dès le montage du composant
  },
  methods: {
    async fetchWalletInfo() {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/api/wallet', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
        });
        this.balance = response.data.balance;
        this.publicAddress = response.data.public_address;
      } catch (err) {
        this.error = err.response?.data?.error || "Error occurred.";
      } finally {
        this.loading = false;
      }
    },
    async fetchTotalPurchases() {
  try {
    const token = localStorage.getItem("token");
    if (!token) throw new Error("Token is missing. Please log in.");

    // Récupérer le total des achats via le service
    const total = await walletService.getTotalPurchases(token);
    this.totalPurchases = total; // Stocker le total des achats
  } catch (err) {
    this.error = err.message || "Failed to fetch total purchases";
  }
},

    formatCurrency(value) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "EUR",
      }).format(value); // Formater le total comme une devise
    }
  },
};
</script>
