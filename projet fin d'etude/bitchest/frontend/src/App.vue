<template>
  <div class="min-h-screen flex flex-col bg-white">
    <Navbar />
    <main class="flex-1 w-full">
      <router-view />
    </main>
    <!-- Footer - Hidden on dashboard and admin pages -->
    <div v-if="!isOnDashboard" class="w-full border-t border-gray-200 mt-auto">
      <!-- <Footer /> -->
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'
import { initializeAuth } from './services/useAuthStore'

export default defineComponent({
  components: { Navbar, Footer },
  setup() {
    const route = useRoute()

    // Initialize auth on app mount
    initializeAuth()

    // Pages where footer should NOT appear
    const dashboardPages = [
      '/dashboard',
      '/portfolio',
      '/wallet', 
      '/transactions',
      '/buy',
      '/sell',
      '/alerts',
      '/alerts-page',
      '/admin',
      '/profile',
      '/profile-page',
      '/notifications'
    ]

    const isOnDashboard = computed(() =>
      dashboardPages.some(page => route.path.startsWith(page))
    )

    return {
      isOnDashboard
    }
  }
})
</script>
