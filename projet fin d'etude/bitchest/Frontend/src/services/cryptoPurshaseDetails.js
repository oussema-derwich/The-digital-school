// services/purchaseService.js

import api from "./api";


const cryptoPurshaseDetails = {
  async fetchPurchases(cryptoId, token) {
    try {
      const response = await api.get(
        `/crypto/wallet/${cryptoId}/purchases`,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || "Failed to load purchase details.");
    }
  },
};

export default cryptoPurshaseDetails;
