<template>
  <aside class="w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-2xl fixed h-screen overflow-y-auto">
    <!-- Logo Section -->
    <div class="p-6 border-b border-blue-700 sticky top-0 bg-blue-900 z-10">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-900 font-bold">BC</div>
        <div>
          <h1 class="text-lg font-bold">BitChest</h1>
          <p class="text-xs text-blue-200">Administration</p>
        </div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4 space-y-2">
      <RouterLink
        to="/admin/dashboard"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/dashboard'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">🏠</span>
        <span>Dashboard</span>
      </RouterLink>

      <RouterLink
        to="/admin/users"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/users'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">👥</span>
        <span>Utilisateurs</span>
      </RouterLink>

      <RouterLink
        to="/admin/cryptos"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/cryptos'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">💰</span>
        <span>Cryptomonnaies</span>
      </RouterLink>

      <RouterLink
        to="/admin/registration-requests"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/registration-requests'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">📝</span>
        <span>Demandes d'inscription</span>
      </RouterLink>

      <RouterLink
        to="/admin/transactions"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/transactions'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">💳</span>
        <span>Transactions</span>
      </RouterLink>

      <RouterLink
        to="/admin/alerts"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/alerts'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">🚨</span>
        <span>Alertes</span>
      </RouterLink>

      <RouterLink
        to="/admin/settings"
        :class="[
          'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200',
          route.path === '/admin/settings'
            ? 'bg-blue-600 text-white font-semibold shadow-lg'
            : 'text-blue-100 hover:bg-blue-700'
        ]"
      >
        <span class="text-lg">⚙️</span>
        <span>Paramètres</span>
      </RouterLink>

      <hr class="border-blue-700 my-4" />

      <button
        @click="logout"
        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-blue-100 hover:bg-red-600 transition-all duration-200"
      >
        <span class="text-lg">🚪</span>
        <span>Déconnexion</span>
      </button>
    </nav>
  </aside>
</template>

<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()

const logout = async () => {
  try {
    await api.post('/auth/logout')
    router.push('/')
  } catch (e) {
    console.error('Logout error:', e)
    router.push('/')
  }
}
</script>

<style scoped>
aside {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

aside::-webkit-scrollbar {
  width: 8px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
