import api from "@/services/api";

export default {
  async fetchCryptos() {
    try {
      const response = await api.get("/cryptos/current");
      console.log(response.data);
      return response.data;
    } catch (error) {
      throw new Error("Failed to fetch cryptos");
    }
  },

  async buyCrypto(cryptoId, quantity, price, token) {
    try {
      const response = await api.post(
        '/crypto/buy',
        { crypto_id: cryptoId, quantity, price },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      return response.data;
    } catch (error) {
      throw new Error("Failed to buy crypto");
    }
  },

  async getCryptoData(id, days) {
    try {
      const response = await api.get(`/cryptos/${id}/history?days=${days}`);
      return response.data;
    } catch (error) {
      console.error("Erreur lors du chargement des données crypto:", error);
      throw error;
    }
  }
};
