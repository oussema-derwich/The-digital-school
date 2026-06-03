<template>
  <div
    v-if="isLoading"
    class="fixed inset-0 flex items-center justify-center h-screen z-50"
  >
    <Loader />
  </div>
  <div v-else class="flex flex-col md:flex-row min-h-screen bg-gray-100">
    <!-- Sidebar gauche -->
    <aside
      class="w-64 text-bitchest-black font-bold flex flex-col justify-between min-h-screen md:relative fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out bg-gray-100"
      :class="{
        '-translate-x-full': isSmallScreen && !isSidebarOpen,
        'bg-sidebar-bg': isSmallScreen,
      }"
    >
      <div>
        <div class="p-6 flex justify-between items-center">
          <img
            src="@/assets/bitchest_logo.png"
            alt="Logo"
            class="h-12 mx-auto"
          />
          <button
            v-if="isSmallScreen"
            @click="toggleSidebar"
            class="text-bitchest-black text-2xl"
          >
            ✖
          </button>
        </div>
        <nav>
          <ul>
            <li
              v-for="item in menuItems"
              :key="item.label"
              class="py-3 px-6 hover:bg-gray-200 cursor-pointer flex items-center"
              :class="{ 'bg-gray-200': isActive(item.route) }"
              @click="navigateTo(item.route)"
            >
              <img
                :src="item.icon"
                alt="icon"
                class="w-6 h-6 mr-2"
                v-if="item.icon"
              />
              {{ item.label }}
            </li>
          </ul>
        </nav>
        <hr class="border-t border-gray-200 my-4 mx-6" />
        <nav>
          <ul>
            <li
              class="py-3 px-6 hover:bg-gray-200 cursor-pointer flex items-center"
              :class="{ 'bg-gray-200': isActive('/dashboard/profile') }"
              @click="
                () => {
                  this.$router.push('/dashboard/profile');
                  this.closeSidebar();
                }
              "
            >
              <img :src="profileIcon" alt="icon" class="w-5 h-5 mr-2" />
              Profile
            </li>
            <li
              class="py-3 px-6 hover:bg-bitchest-alert cursor-pointer flex items-center"
              @click="logout"
            >
              <img :src="logoutIcon" alt="icon" class="w-7 h-7 mr-2" />
              Logout
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Contenu principal -->
    <div class="flex-1 flex flex-col h-screen">
      <!-- Barre de navigation supérieure -->
      <header class="bg-white shadow p-4 flex justify-end items-center gap-10">
        <button
          class="md:hidden bg-gray-200 text-bitchest-black px-3 py-2 rounded text-2xl"
          @click="toggleSidebar"
        >
          {{ isSidebarOpen ? "✖" : "☰" }}
        </button>

        <h1 class="text-xl font-bold text-gray-700">Dashboard</h1>

        <!-- Bouton Notifications -->
        <div class="relative">
          <button class="ml-4 relative" @click="toggleNotifications">
            <span
              v-if="unreadNotificationsCount > 0"
              class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1"
            >
              {{ unreadNotificationsCount }}
            </span>
            <img
              src="@/assets/icons/notification.svg"
              alt="Notifications"
              class="w-9 h-8 sm:w-8 sm:h-7 xs:w-7 xs:h-6"
            />
          </button>

          <!-- Notifications Dropdown -->
          <div
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-64 bg-white shadow-md rounded-lg p-4 z-50 notification-dropdown"
          >
            <ul>
              <li
                v-for="(notification, index) in notifications.slice(0, 5)"
                :key="index"
                class="py-2 border-b last:border-b-0 notification-item"
              >
                {{ notification.message }}
              </li>
            </ul>
            <a
              href="/dashboard/notifications"
              class="block text-center text-blue-500 text-sm mt-2"
            >
              See All
            </a>
          </div>
        </div>

        <img
          v-if="isSmallScreen"
          :src="
            user.photo
              ? `http://localhost:8000${user.photo}`
              : '/images/unknown.png'
          "
          alt="User Profile"
          class="w-8 h-8 rounded-full ml-4"
        />
      </header>

      <main class="flex-1 p-6 bg-white w-full overflow-auto">
        <router-view />
      </main>
    </div>

    <!-- Sidebar droite (User Sidebar) -->
    <aside
      v-if="!isLoading"
      :class="[
        'w-64 bg-sidebar-bg p-6 fixed right-0 top-0 h-screen transform transition-transform duration-300',
        'translate-x-full',
        'md:relative md:translate-x-0',
      ]"
    >
      <div class="text-center text-bitchest-black">
        <img
          :src="
            user.photo
              ? `http://localhost:8000${user.photo}`
              : '/images/unknown.png'
          "
          alt="User Profile"
          class="w-16 h-16 rounded-full mx-auto"
        />
        <h2 class="text-lg font-semibold mt-2">{{ user.name }}</h2>
        <p class="text-sm text-bitchest-black">{{ user.role }}</p>
      </div>

      <div class="mt-6">
        <div v-if="this.user.role === 'admin'" class="space-y-3">
          <!-- Valeur totale de la plateforme -->
          <div class="stat-box bg-bitchest-primary text-bitchest-white">
            <p class="stat-title">Total Value</p>
            <p class="stat-value">
              {{ formatNumber(platformTotalValue?.totalValue) }} €
            </p>
          </div>
          <div class="flex gap-3">
            <!-- Volume total des achats -->
            <div class="stat-box bg-bitchest-secondary text-bitchest-white">
              <p class="stat-title">Total Buy</p>
              <p class="stat-value">
                {{ formatNumber(totalTransactionVolume?.total_buy) }} €
              </p>
            </div>

            <!-- Volume total des ventes -->
            <div class="stat-box bg-bitchest-alert text-bitchest-white">
              <p class="stat-title">Total Sell</p>
              <p class="stat-value">
                {{ formatNumber(totalTransactionVolume?.total_sell) }} €
              </p>
            </div>
          </div>
        </div>
        <div v-else class="space-y-3">
          <!-- Valeur totale de la plateforme -->
          <div class="stat-box bg-bitchest-primary text-bitchest-white">
            <p class="stat-title">Wallet balance</p>
            <p class="stat-value">
              {{ formatNumber(portfolioComparison?.walletBalance) }}€
            </p>
          </div>
          <div class="flex gap-3">
            <!-- Volume total des achats -->
            <div class="stat-box bg-bitchest-black text-bitchest-white">
              <p class="stat-title">Total Investment</p>
              <p class="stat-value">
                {{ formatNumber(portfolioComparison?.totalInvestmentValue) }}€
              </p>
            </div>

            <!-- Volume total des ventes -->
            <div
              class="stat-box text-bitchest-white"
              :class="
                portfolioComparison?.totalCurrentValue >=
                portfolioComparison?.totalInvestmentValue
                  ? 'bg-green-700'
                  : 'bg-bitchest-alert'
              "
            >
              <p class="stat-title">Current Value</p>
              <p class="stat-value">
                {{ formatNumber(portfolioComparison?.totalCurrentValue) }}€
              </p>
            </div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import MyStats from "@/components/sections/MyStats.vue";
import RegistrationRequestsList from "@/components/admin/RegistrationRequestsList.vue";
import ProfileManager from "@/components/sections/ProfileManager.vue";
import AdminUserList from "@/components/admin/AdminUserList.vue";
import Loader from "@/components/utils/Loader.vue";

import dashboardIcon from "@/assets/icons/dashboard.png";
import registrationIcon from "@/assets/icons/request.png";
import transactionsIcon from "@/assets/icons/transactions.png";
import alertIcon from "@/assets/icons/alert.png";
import usersIcon from "@/assets/icons/user.png";
import cryptoIcon from "@/assets/icons/crypto.png";
import walletIcon from "@/assets/icons/wallet.png";
import tradingIcon from "@/assets/icons/trading.png";
import logoutIcon from "@/assets/icons/logout.png";
import profileIcon from "@/assets/icons/settings.png";
import pusher from "@/pusher";

export default {
  name: "Dashboard",
  components: {
    MyStats,
    RegistrationRequestsList,
    ProfileManager,
    AdminUserList,
    Loader,
  },
  data() {
    return {
      isLoading: true,
      isSidebarOpen: false,
      isSmallScreen: window.innerWidth <= 768,

      currentView: "MyStats",
      menuItems: [],
      profileIcon,
      logoutIcon,
      showNotifications: false,
    };
  },
  computed: {
    ...mapState("auth", ["user"]),
    ...mapState("stats", [
      "userPortfolio",
      "portfolioComparison",
      "platformTotalValue",
      "totalTransactionVolume",
    ]),
    ...mapState("notification", ["notifications"]),

    filteredMenuItems() {
      return this.user.role === "admin"
        ? [
            {
              label: "Dashboard",
              route: "/dashboard",
              component: "MyStats",
              icon: dashboardIcon,
            },
            {
              label: "Registration Requests",
              route: "/dashboard/registration-requests",
              component: "RegistrationRequestsList",
              icon: registrationIcon,
            },
            {
              label: "Transactions",
              route: "/dashboard/transactions",
              component: "TransactionsList",
              icon: transactionsIcon,
            },
            {
              label: "Manage Users",
              route: "/dashboard/manage-users",
              component: "AdminUserList",
              icon: usersIcon,
            },
            {
              label: "Manage Crypto",
              route: "/dashboard/manage-crypto",
              component: "CryptoManagement",
              icon: cryptoIcon,
            },
          ]
        : [
            {
              label: "Dashboard",
              route: "/dashboard",
              component: "MyStats",
              icon: dashboardIcon,
            },
            {
              label: "Transactions",
              route: "/dashboard/transactions",
              component: "TransactionsList",
              icon: transactionsIcon,
            },
            {
              label: "Wallet",
              route: "/dashboard/wallet",
              component: "CryptoWallet",
              icon: walletIcon,
            },
            {
              label: "Trading & Market",
              route: "/dashboard/trading-market",
              component: "TradingMarket",
              icon: tradingIcon,
            },
            {
              label: "Alerts & Notifications",
              route: "/dashboard/alerts",
              component: "AlertSection",
              icon: alertIcon,
            },
          ];
    },
    unreadNotificationsCount() {
      return this.notifications.filter((notification) => !notification.is_read)
        .length;
    },
  },
  async created() {
    await this.fetchProfile();
    await this.fetchNotifications();

    // S'abonner au canal 'notifications'
    const channel = pusher.subscribe(`notifications.${this.user.id}`);

    // Écouter l'événement 'new-notification'
    channel.bind("new-notification", (data) => {
      data.created_at = data.date;
      console.log("New notification received:", data);
      this.$store.commit("notification/ADD_NOTIFICATION", data);
    });

    if (this.user.role === "admin") {
      await this.fetchPlatformTotalValue();
      await this.fetchTotalTransactionVolume();
    } else if (this.user.role === "client") {
      await this.fetchUserPortfolio(this.user.id);
      await this.fetchPortfolioComparison(this.user.id);
    }
    this.isLoading = false;
    this.menuItems = this.filteredMenuItems;

    window.addEventListener("resize", this.checkScreenSize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);

    const channel = pusher.subscribe(`notifications.${this.user.id}`);
    channel.unbind("new-notification");
    pusher.unsubscribe(`notifications.${this.user.id}`);
  },
  methods: {
    ...mapActions("auth", ["fetchProfile"]),
    ...mapActions("stats", [
      "fetchUserPortfolio",
      "fetchPortfolioComparison",
      "fetchPlatformTotalValue",
      "fetchTotalTransactionVolume",
    ]),
    ...mapActions("notification", ["fetchNotifications"]),
    navigateTo(route) {
      this.$router.push(route);
      this.menuItems = this.filteredMenuItems;
      this.closeSidebar();
    },

    isActive(route) {
      return this.$route.path === route;
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },

    closeSidebar() {
      if (this.isSmallScreen) {
        this.isSidebarOpen = false; // Fermer le sidebar sur les petits écrans
      }
    },

    checkScreenSize() {
      this.isSmallScreen = window.innerWidth <= 768;
    },
    async logout() {
      localStorage.removeItem("token");
      this.$router.push({ name: "login" });
      this.closeSidebar(); // Fermer le sidebar après le logout
    },
    formatNumber(value) {
      if (!value) return "0";
      return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
        useGrouping: true,
      })
        .format(parseFloat(value))
        .replace(/\s/g, " ");
    },
    toggleNotifications() {
      this.showNotifications = !this.showNotifications;
      if (this.showNotifications) {
        setTimeout(() => {
          document.addEventListener("click", this.handleClickOutside);
        }, 0);
      } else {
        document.removeEventListener("click", this.handleClickOutside);
      }
    },

    handleClickOutside(event) {
      const dropdown = this.$el.querySelector(".notification-dropdown");
      const button = this.$el.querySelector("button");

      if (
        dropdown &&
        button &&
        !dropdown.contains(event.target) &&
        !button.contains(event.target)
      ) {
        this.showNotifications = false;
        document.removeEventListener("click", this.handleClickOutside);
      }
    },
  },
};
</script>
<style scoped>
.stat-box {
  padding: 12px;
  border-radius: 6px;
  font-weight: bold;
  text-align: center;
}

.stat-title {
  font-size: 10px;
  text-transform: uppercase;
}

.stat-value {
  font-size: 18px;
  margin-top: 4px;
}
.notification-dropdown {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  transition: background-color 0.2s ease;
}

.notification-item:hover {
  background-color: #f3f4f6;
}
</style>
