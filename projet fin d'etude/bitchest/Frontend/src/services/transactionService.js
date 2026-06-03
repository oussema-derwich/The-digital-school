// services/transactionService.js
import api from './api';

const transactionService = {
  async fetchTransactions(token) {
    try {
      const response = await api.get('transactions', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      throw new Error('Error retrieving transactions: ' + error.message);
    }
  },
};

export default transactionService;
