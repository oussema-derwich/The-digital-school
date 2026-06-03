import alertService from "@/services/alertService";

const state = {
  alerts: [], // Liste des alertes
};

const mutations = {
  SET_ALERTS(state, alerts) {
    state.alerts = alerts;
  },
  ADD_ALERT(state, alert) {
    state.alerts.push(alert);
  },
  UPDATE_ALERT(state, updatedAlert) {
    const index = state.alerts.findIndex((a) => a.id === updatedAlert.id);
    if (index !== -1) {
      state.alerts.splice(index, 1, updatedAlert);
    }
  },
  TOGGLE_ALERT_STATUS(state, id) {
    const index = state.alerts.findIndex((a) => a.id === id);
    if (index !== -1) {
      state.alerts[index].status = !state.alerts[index].status;
    }
  },
  DELETE_ALERT(state, id) {
    state.alerts = state.alerts.filter((a) => a.id !== id);
  },
};

const actions = {
  // Récupérer toutes les alertes
  async fetchAlerts({ commit }) {
    try {
      const response = await alertService.getAlerts();
      commit("SET_ALERTS", response.data);
    } catch (error) {
      console.error("Failed to fetch alerts:", error);
    }
  },

  // Créer une alerte
  async createAlert({ commit }, alert) {
    try {
      const response = await alertService.createAlert(alert);
      commit("ADD_ALERT", response.data);
    } catch (error) {
      console.error("Failed to create alert:", error);
      throw error;
    }
  },

  // Modifier une alerte
  async updateAlert({ commit }, { id, alert }) {
    try {
      const response = await alertService.updateAlert(id, alert);
      commit("UPDATE_ALERT", response.data);
    } catch (error) {
      console.error("Failed to update alert:", error);
      throw error;
    }
  },
  // Activer ou désactiver une alerte
  async toggleAlertStatus({ commit }, id) {
    try {
      const response = await alertService.toggleAlert(id);
      commit("TOGGLE_ALERT_STATUS", id);
    } catch (error) {
      console.error("Failed to toggle alert:", error);
      throw error;
    }
  },

  // Supprimer une alerte
  async deleteAlert({ commit }, id) {
    try {
      await alertService.deleteAlert(id);
      commit("DELETE_ALERT", id);
    } catch (error) {
      console.error("Failed to delete alert:", error);
      throw error;
    }
  },
};

const getters = {
  alerts: (state) => state.alerts,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
