import api from "@/services/api";

export default {
  // Récupérer toutes les alertes
  getAlerts() {
    return api.get("/alerts");
  },

  // Créer une alerte
  createAlert(alert) {
    return api.post("/alerts", alert);
  },

  // Modifier une alerte
  updateAlert(id, alert) {
    console.log(id, alert);
    return api.put(`/alerts/${id}`, alert);
  },

  // Activer ou désactiver une alerte
  toggleAlert(id) {
    return api.put(`/alerts/${id}/toggle-status`);
  },

  // Supprimer une alerte
  deleteAlert(id) {
    return api.delete(`/alerts/${id}`);
  },
};
