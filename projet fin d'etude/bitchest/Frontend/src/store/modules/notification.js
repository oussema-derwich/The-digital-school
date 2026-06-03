import notificationService from "@/services/notificationService";

const state = {
  notifications: [], // Liste des notifications
};

const mutations = {
  SET_NOTIFICATIONS(state, notifications) {
    state.notifications = notifications;
  },
  MARK_NOTIFICATION_AS_READ(state, notificationId) {
    const notification = state.notifications.find(
      (n) => n.id === notificationId
    );
    if (notification) {
      notification.is_read = true;
    }
  },
  ADD_NOTIFICATION(state, notification) {
    state.notifications.unshift(notification);
  },
};

const actions = {
  // Récupérer les notifications d'un utilisateur
  async fetchNotifications({ commit }) {
    try {
      const response = await notificationService.getNotifications();
      console.log(response.data);
      commit("SET_NOTIFICATIONS", response.data);
    } catch (error) {
      console.error("Failed to fetch notifications:", error);
    }
  },

  // Ajouter une notification (utilisé pour les notifications en temps réel)
  addNotification({ commit }, notification) {
    commit("ADD_NOTIFICATION", notification);
  },

  async markNotificationAsRead({ commit }, notificationId) {
    try {
      await notificationService.markAsRead(notificationId);
      commit("MARK_NOTIFICATION_AS_READ", notificationId);
    } catch (error) {
      console.error("Error marking notification as read:", error);
    }
  },
};

const getters = {
  notifications: (state) => state.notifications,
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
