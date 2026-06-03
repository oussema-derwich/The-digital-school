<template>
  <div class="container mx-auto p-4 sm:p-6">
    <h1
      class="text-2xl sm:text-3xl font-bold text-center text-bitchest-black mb-4 sm:mb-6"
    >
      Notifications
    </h1>

    <!-- Liste des notifications -->
    <div v-if="loading" class="text-center">
      <Loader />
    </div>
    <div
      v-else-if="notifications.length === 0"
      class="text-center text-gray-500"
    >
      <p>No notifications found.</p>
    </div>
    <div v-else>
      <ul class="space-y-4">
        <li
          v-for="notification in paginatedNotifications"
          :key="notification.id"
          @click="openNotificationModal(notification)"
          class="p-4 rounded-lg shadow-md cursor-pointer transition-all duration-200"
          :class="{
            'bg-gray-100': notification.is_read,
            'bg-blue-50': !notification.is_read,
            'hover:shadow-lg': true,
          }"
        >
          <div class="flex justify-between items-center">
            <p class="text-sm font-medium">
              {{ notification.message }}
            </p>
            <span
              v-if="!notification.is_read"
              class="bg-red-500 text-white text-xs rounded-full px-2 py-1"
            >
              New
            </span>
          </div>
          <p class="text-xs text-gray-500 mt-2">
            {{ formatDate(notification.created_at) }}
          </p>
        </li>
      </ul>

      <!-- Pagination -->
      <Pagination
        v-if="totalPages > 1"
        :currentPage="currentPage"
        :totalPages="totalPages"
        @goToPage="changePage"
      />
    </div>

    <!-- Modal pour afficher les détails de la notification -->
    <div
      v-if="selectedNotification"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
      @click.self="closeNotificationModal"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Notification Details</h2>
        <p class="text-sm text-gray-700">
          {{ selectedNotification.message }}
        </p>
        <p class="text-xs text-gray-500 mt-2">
          {{ formatDate(selectedNotification.created_at) }}
        </p>
        <div class="flex justify-end mt-4">
          <button
            @click="closeNotificationModal"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import Loader from "@/components/utils/Loader.vue";
import Pagination from "@/components/Pagination.vue";
import pusher from "@/pusher";

export default {
  name: "NotificationsList",
  components: {
    Loader,
    Pagination,
  },
  data() {
    return {
      loading: true,
      selectedNotification: null,
      currentPage: 1,
      itemsPerPage: 5, // Nombre de notifications par page
    };
  },
  computed: {
    ...mapState("notification", ["notifications"]),
    ...mapState("auth", ["user"]),

    totalPages() {
      return Math.ceil(this.notifications.length / this.itemsPerPage);
    },

    paginatedNotifications() {
      // Trier les notifications par date (du plus récent au plus ancien)
      const sortedNotifications = this.notifications.slice().sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
      });

      // Paginer les notifications triées
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return sortedNotifications.slice(start, end);
    },
  },
  async created() {
    await this.fetchNotifications();
    this.loading = false;

    // S'abonner au canal 'notifications'
    const channel = pusher.subscribe(`notifications.${this.user.id}`);

    // Écouter l'événement 'new-notification'
    channel.bind("new-notification", (data) => {
      data.created_at = data.date;
      console.log("New notification received:", data);

      const exists = this.notifications.some((notif) => notif.id === data.id);
      if (!exists) {
        this.notifications.unshift(data);
      }
    });
  },
  methods: {
    ...mapActions("notification", [
      "fetchNotifications",
      "markNotificationAsRead",
    ]),

    openNotificationModal(notification) {
      this.selectedNotification = notification;
      if (!notification.is_read) {
        this.markNotificationAsRead(notification.id);
      }
    },

    closeNotificationModal() {
      this.selectedNotification = null;
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString("en-EN", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },

    changePage(page) {
      this.currentPage = page;
    },
  },
  beforeUnmount() {
    const channel = pusher.subscribe(`notifications.${this.user.id}`);
    channel.unbind("new-notification");
    pusher.unsubscribe(`notifications.${this.user.id}`);
  },
};
</script>
