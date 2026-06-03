import api from "@/services/api";

export default {
  // Fonction pour récupérer les cryptos du portefeuille
  async getWalletCryptos(token) {
    try {
      const response = await api.get('/crypto/wallet', {
        headers: { Authorization: `Bearer ${token}` },
      });
      return response.data; // On retourne directement la liste des cryptos
    } catch (error) {
      throw new Error(error.response?.data?.error || "Failed to load wallet.");
    }
  },

  // Fonction pour récupérer le total des achats de crypto
  async getTotalPurchases(token) {
    try {
      const response = await api.get('/crypto/total-purchases', {
        headers: { Authorization: `Bearer ${token}` },
      });
      // Assurez-vous de récupérer le bon champ de la réponse de l'API
      return response.data.totalCryptoPurchase || 0; // Vérifier que le champ est correctement renvoyé
    } catch (err) {
      throw new Error("Failed to fetch total purchases");
    }
  }
  
};
