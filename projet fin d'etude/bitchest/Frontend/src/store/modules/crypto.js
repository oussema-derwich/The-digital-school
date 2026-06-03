import cryptoService from "@/services/cryptoService";
import api from "@/services/api";

const state = {
  cryptos: [], // Liste des cryptos
  priceHistory: [], // Historique des prix
  crypto: null, // Détails de la crypto sélectionnée
};

const mutations = {
  SET_PRICE_HISTORY(state, history) {
    state.priceHistory = history.map((entry) => ({
      ...entry,
      value: parseFloat(entry.value),
    }));
  },
  SET_CRYPTO(state, crypto) {
    state.crypto = crypto;
  },
};

const actions = {
  async loadPriceHistory({ commit }, { cryptoId, days = 30 }) {
    try {
      const response = await api.get(
        `/cryptos/${cryptoId}/history?days=${days}`
      );

      if (!response.data || !response.data.original)
        throw new Error("Données invalides");

      const { crypto, history } = response.data.original;
      console.log(response.data);
      commit("SET_PRICE_HISTORY", history || []);
      commit("SET_CRYPTO", crypto || null);
    } catch (error) {
      console.error(
        "Erreur lors de la récupération de l'historique des prix",
        error
      );
      commit("SET_PRICE_HISTORY", []);
    }
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
