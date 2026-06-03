// store/modules/transactions.js
import transactionService from '@/services/transactionService'; // Import du service

export default {
  state: {
    transactions: [],
    isLoading: false, // Ajout de l'état de chargement
  },
  mutations: {
    SET_TRANSACTIONS(state, transactions) {
      state.transactions = transactions;
    },
    SET_LOADING(state, isLoading) {
      state.isLoading = isLoading;
    },
  },
  actions: {
    async fetchTransactions({ commit }) {
      commit('SET_LOADING', true); // Début du chargement
      try {
        const token = localStorage.getItem('token'); // Récupérer le token stocké
        if (!token) {
          throw new Error('Token is missing');
        }

        // Utiliser le service pour récupérer les transactions
        const transactions = await transactionService.fetchTransactions(token);
        commit('SET_TRANSACTIONS', transactions);
      } catch (error) {
        console.error(error.message);
      } finally {
        commit('SET_LOADING', false); // Fin du chargement
      }
    },
  },
  getters: {
    transactions: state => state.transactions,
    isLoading: state => state.isLoading, // Ajout d'un getter pour l'état de chargement
  },
};
