import statsService from "@/services/statsService";

const state = {
  // Statistiques client
  userPortfolio: null,
  userCryptoDetails: [],
  userInvestments: [],
  portfolioComparison: null,
  cryptoProfitLoss: [],
  evolution: [],

  // Statistiques admin
  platformTotalValue: null,
  platformCryptoDetails: [],
  topCryptos: [],
  topCryptosByRevenue: [],
  topBuyers: [],
  topWallets: [],
  totalTransactionVolume: null,
  recentActivity: [],
  inactiveUsers: [],
};

const mutations = {
  // Statistiques client
  SET_USER_PORTFOLIO(state, data) {
    state.userPortfolio = data;
  },
  SET_USER_CRYPTO_DETAILS(state, data) {
    state.userCryptoDetails = data;
  },
  SET_USER_INVESTMENTS(state, data) {
    state.userInvestments = data;
  },
  SET_PORTFOLIO_COMPARISON(state, data) {
    state.portfolioComparison = data;
  },
  SET_CRYPTO_PROFIT_LOSS(state, data) {
    state.cryptoProfitLoss = data;
  },
  SET_EVOLUTION(state, data) {
    state.evolution = data;
  },

  // Statistiques admin
  SET_PLATFORM_TOTAL_VALUE(state, data) {
    state.platformTotalValue = data;
  },
  SET_PLATFORM_CRYPTO_DETAILS(state, data) {
    state.platformCryptoDetails = data;
  },
  SET_TOP_CRYPTOS(state, data) {
    state.topCryptos = data;
  },
  SET_TOP_CRYPTOS_BY_REVENUE(state, data) {
    state.topCryptosByRevenue = data;
  },
  SET_TOP_BUYERS(state, data) {
    state.topBuyers = data;
  },
  SET_TOP_WALLETS(state, data) {
    state.topWallets = data;
  },
  SET_TOTAL_TRANSACTION_VOLUME(state, data) {
    state.totalTransactionVolume = data;
  },
  SET_RECENT_ACTIVITY(state, data) {
    state.recentActivity = data;
  },
  SET_INACTIVE_USERS(state, data) {
    state.inactiveUsers = data;
  },
};

const actions = {
  // Statistiques client
  async fetchUserPortfolio({ commit }, userId) {
    const data = await statsService.getUserPortfolio(userId);
    commit("SET_USER_PORTFOLIO", data);
  },
  async fetchUserCryptoDetails({ commit }, userId) {
    const data = await statsService.getUserCryptoDetails(userId);
    commit("SET_USER_CRYPTO_DETAILS", data);
  },
  async fetchUserInvestments({ commit }, userId) {
    const data = await statsService.getUserInvestments(userId);
    commit("SET_USER_INVESTMENTS", data);
  },
  async fetchPortfolioComparison({ commit }, userId) {
    const data = await statsService.comparePortfolioValue(userId);
    commit("SET_PORTFOLIO_COMPARISON", data);
  },
  async fetchCryptoProfitLoss({ commit }, userId) {
    const data = await statsService.getCryptoProfitOrLoss(userId);
    commit("SET_CRYPTO_PROFIT_LOSS", data);
  },
  async loadPortfolioEvolution({ commit }, days = 30) {
    try {
      const data = await statsService.getPortfolioEvolution(days);
      commit("SET_EVOLUTION", data);
    } catch (error) {
      console.error(
        "Erreur lors du chargement de l'évolution du portefeuille :",
        error
      );
    }
  },

  // Statistiques admin
  async fetchPlatformTotalValue({ commit }) {
    const data = await statsService.getPlatformTotalValue();
    commit("SET_PLATFORM_TOTAL_VALUE", data);
  },
  async fetchPlatformCryptoDetails({ commit }) {
    const data = await statsService.getPlatformCryptoDetails();
    commit("SET_PLATFORM_CRYPTO_DETAILS", data);
  },
  async fetchTopCryptos({ commit }) {
    const data = await statsService.getTopCryptos();
    commit("SET_TOP_CRYPTOS", data);
  },
  async fetchTopCryptosByRevenue({ commit }) {
    const data = await statsService.getTopCryptosByRevenue();
    commit("SET_TOP_CRYPTOS_BY_REVENUE", data);
  },
  async fetchTopBuyers({ commit }) {
    const data = await statsService.getTopBuyers();
    commit("SET_TOP_BUYERS", data);
  },
  async fetchTopWallets({ commit }) {
    const data = await statsService.getTopWallets();
    commit("SET_TOP_WALLETS", data);
  },
  async fetchTotalTransactionVolume({ commit }) {
    const data = await statsService.getTotalTransactionVolume();
    commit("SET_TOTAL_TRANSACTION_VOLUME", data);
  },
  async fetchRecentActivity({ commit }) {
    const data = await statsService.getRecentActivity();
    commit("SET_RECENT_ACTIVITY", data);
  },
  async fetchInactiveUsers({ commit }) {
    const data = await statsService.getInactiveUsers();
    commit("SET_INACTIVE_USERS", data);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
