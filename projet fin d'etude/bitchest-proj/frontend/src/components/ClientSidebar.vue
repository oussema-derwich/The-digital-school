<template>
  <aside class="w-64 bg-primary-dark text-white min-h-screen p-6 shadow-lg flex flex-col">
    <!-- Logo -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center font-bold text-primary-dark">BC</div>
        <h1 class="text-xl font-bold">BitChest</h1>
      </div>
      <p class="text-sm text-blue-200">Trading Platform</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 space-y-2">
      <router-link
        to="/dashboard"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/dashboard') ? 'bg-primary' : ''
        ]"
      >
        🏠 Tableau de Bord
      </router-link>

      <router-link
        to="/portfolio"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/portfolio') ? 'bg-primary' : ''
        ]"
      >
        💼 Portefeuille
      </router-link>

      <router-link
        to="/cryptos"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/cryptos') ? 'bg-primary' : ''
        ]"
      >
        💱 Cryptos
      </router-link>

      <router-link
        to="/history"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/history') ? 'bg-primary' : ''
        ]"
      >
        📊 Historique
      </router-link>

      <router-link
        to="/alerts-page"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/alerts-page') ? 'bg-primary' : ''
        ]"
      >
        🔔 Alertes
      </router-link>

      <router-link
        to="/profile-page"
        :class="[
          'block px-4 py-3 rounded-lg font-medium transition hover:bg-primary',
          isActive('/profile-page') ? 'bg-primary' : ''
        ]"
      >
        👤 Profil
      </router-link>
    </nav>

    <!-- Logout Button -->
    <button
      @click="logout"
      class="w-full px-4 py-3 bg-red-500 text-white rounded-lg font-bold hover:bg-red-600 transition flex items-center justify-center gap-2"
    >
      🚪 Déconnexion
    </button>
  </aside>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { logout } from '../services/auth'

export default defineComponent({
  setup() {
    const router = useRouter()
    const route = useRoute()

    const isActive = (path: string) => {
      return route.path === path
    }

    const handleLogout = async () => {
      try {
        await logout()
        router.push('/login')
      } catch (e) {
        console.error('Logout error:', e)
        // Still redirect even if logout fails
        router.push('/login')
      }
    }

    return {
      isActive,
      logout: handleLogout
    }
  }
})
</script>

<style scoped>
a {
  text-decoration: none;
  color: inherit;
}

a:hover {
  text-decoration: none;
}
</style>
