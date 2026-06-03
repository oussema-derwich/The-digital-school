import api from "@/services/api";

export default {
  // Statistiques client
  async getUserPortfolio(userId) {
    const response = await api.post("/stats/portfolio", { user_id: userId });
    console.log(response.data);
    return response.data;
  },
  async getUserCryptoDetails(userId) {
    const response = await api.post("/stats/crypto-details", {
      user_id: userId,
    });
    return response.data;
  },
  async getUserInvestments(userId) {
    const response = await api.post("/stats/investments", { user_id: userId });
    return response.data;
  },
  async comparePortfolioValue(userId) {
    const response = await api.post("/stats/compare-portfolio", {
      user_id: userId,
    });
    return response.data;
  },
  async getCryptoProfitOrLoss(userId) {
    const response = await api.post("/stats/crypto-profit-loss", {
      user_id: userId,
    });
    return response.data;
  },
  async getMostPopularCryptos() {
    const response = await api.get("/stats/most-popular-cryptos");
    return response.data;
  },
  async getPortfolioEvolution(days = 30) {
    try {
      const response = await api.get(`/stats/portfolio-evolution?days=${days}`);
      return response.data.data;
    } catch (error) {
      console.error(
        "Erreur lors de la récupération de l'évolution du portefeuille",
        error
      );
      return [];
    }
  },

  // Statistiques admin
  async getPlatformTotalValue() {
    const response = await api.get("/stats/total-value");
    return response.data;
  },
  async getPlatformCryptoDetails() {
    const response = await api.get("/stats/crypto-details");
    return response.data;
  },
  async getTopCryptos() {
    const response = await api.get("/stats/top-cryptos");
    return response.data;
  },
  async getTopCryptosByRevenue() {
    const response = await api.get("/stats/top-cryptos-by-revenue");
    return response.data;
  },
  async getTopBuyers() {
    const response = await api.get("/stats/top-buyers");
    return response.data;
  },
  async getTopWallets() {
    const response = await api.get("/stats/top-wallets");
    return response.data;
  },
  async getTotalTransactionVolume() {
    const response = await api.get("/stats/total-transaction-volume");
    return response.data;
  },
  async getRecentActivity() {
    const response = await api.get("/stats/recent-activity");
    return response.data;
  },
  async getInactiveUsers() {
    const response = await api.get("/stats/inactive-users");
    return response.data;
  },
};
