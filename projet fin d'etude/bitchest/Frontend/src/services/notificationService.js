import api from "@/services/api";

export default {
  // Récupérer les notifications de l'utilisateur connecté
  getNotifications() {
    return api.get("/notifications");
  },

  // Marquer une notification comme lue
  markAsRead(id) {
    return api.patch(`/notifications/${id}/mark-as-read`);
  },
};
