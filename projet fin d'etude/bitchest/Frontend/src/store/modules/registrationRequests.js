import axios from "axios";

export default {
  namespaced: true,
  state: {
    requests: [],
  },
  mutations: {
    SET_REQUESTS(state, requests) {
      state.requests = requests;
    },
    APPROVE_REQUEST(state, requestId) {
      const request = state.requests.find((req) => req.id === requestId);
      if (request) {
        request.is_approved = true;
        request.is_rejected = false;
      }
    },
    REJECT_REQUEST(state, requestId) {
      const request = state.requests.find((req) => req.id === requestId);
      if (request) {
        request.is_approved = false;
        request.is_rejected = true;
      }
    },
  },
  actions: {
    async fetchRequests({ commit }) {
      try {
        const token = localStorage.getItem("token"); // 🔹 Récupérer le token du localStorage
        if (!token)
          throw new Error("Aucun token trouvé, utilisateur non authentifié.");

        const response = await axios.get(
          "http://localhost:8000/api/admin/registration-requests",
          {
            headers: {
              Authorization: `Bearer ${token}`, // ✅ Ajouter le token
              Accept: "application/json",
            },
          }
        );

        commit("SET_REQUESTS", response.data);
      } catch (error) {
        console.error("Erreur lors du chargement des demandes :", error);
        throw error;
      }
    },

    async approveRequest({ commit }, requestId) {
      try {
        const response = await axios.post(
          `http://localhost:8000/api/admin/registration-requests/${requestId}/approve`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          }
        );
        commit("APPROVE_REQUEST", requestId);
        return response.data;
      } catch (error) {
        throw error;
      }
    },
    async rejectRequest({ commit }, requestId) {
      try {
        await axios.post(
          `http://localhost:8000/api/admin/registration-requests/${requestId}/reject`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          }
        );

        commit("REJECT_REQUEST", requestId);
      } catch (error) {
        console.error("Error while rejecting request :", error);
        throw error;
      }
    },
  },
  getters: {
    allRequests: (state) => state.requests,
  },
};
