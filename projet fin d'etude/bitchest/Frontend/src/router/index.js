import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Dashboard from "../views/Dashboard.vue";
import Login from "../views/Login.vue";

import AdminUserList from "../components/admin/AdminUserList.vue";
import AddUserForm from "../components/admin/AddUserForm.vue";
import EditUser from "../components/admin/EditUser.vue";
import RegistrationRequestsList from "@/components/admin/RegistrationRequestsList.vue";
import ProfileManager from "@/components/sections/ProfileManager.vue";
import CryptoList from "@/components/admin/cryptos/CryptoList.vue";
import CryptoDetails from "@/components/sections/CryptoDetails.vue";
import MyStats from "@/components/sections/MyStats.vue";
import CryptoWallet from "@/components/wallet/CryptoWallet.vue";
import Transactions from "@/components/Transactions.vue";
import CryptoPurchaseDetails from "@/components/wallet/CryptoPurchaseDetails.vue";
import AlertSection from "@/components/sections/AlertSection.vue";
import NotificationsList from "@/components/sections/NotificationsList.vue";
import CompeteProfile from "@/views/CompeteProfile.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: Home,
    },
    {
      path: "/login",
      name: "login",
      component: Login,
    },
    {
      path: "/complete-profile",
      name: "complete-profile",
      component: CompeteProfile,
    },
    {
      path: "/dashboard",
      name: "dashboard",
      component: Dashboard,
      meta: { requiresAuth: true }, // Route protégée
      children: [
        {
          path: "",
          name: "my-stats",
          component: MyStats,
        },
        {
          path: "registration-requests",
          name: "registration-requests",
          component: RegistrationRequestsList,
        },
        {
          path: "manage-users",
          name: "manage-users",
          component: AdminUserList,
        },
        {
          path: "admin/users/add",
          name: "add-user",
          component: AddUserForm,
        },
        {
          path: "admin/users/edit/:id",
          name: "edit-user",
          component: EditUser,
        },
        {
          path: "profile",
          name: "profile",
          component: ProfileManager,
        },
        {
          path: "manage-crypto",
          name: "manage-crypto",
          component: CryptoList,
          props: { isClient: false },
        },
        {
          path: "crypto/:id",
          name: "crypto-detail",
          component: CryptoDetails,
          props: true,
        },
        {
          path: "trading-market",
          name: "trade-market",
          component: CryptoList,
          props: { isClient: true },
        },
        //affiche des achats
        {
          path: "wallet",
          name: "crypto-wallet",
          component: CryptoWallet,
        },
        //cryptoPurchaseDetails
        {
          path: "/crypto/:id/purchases",
          name: "cryptoPurchaseDetails",
          component: CryptoPurchaseDetails,
          props: true,
        },
        //affiche des transactions
        {
          path: "transactions",
          name: "transactions",
          component: Transactions,
        },
        {
          path: "alerts",
          name: "alerts",
          component: AlertSection,
        },
        {
          path: "notifications",
          name: "notifications",
          component: NotificationsList,
        },
      ],
    },
  ],
});

// Garde de navigation pour vérifier l'authentification
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem("token"); // Vérifiez si l'utilisateur est authentifié
  if (to.meta.requiresAuth && !isAuthenticated) {
    next("/login"); // Redirigez vers la page de connexion
  } else {
    next(); // Continuez la navigation
  }
});

export default router;
