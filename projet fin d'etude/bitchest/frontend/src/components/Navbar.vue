<template>
  <nav
    v-if="!hideOnClientArea"
    :class="[
      'w-full transition-all duration-300 shadow-sm z-40',
      isAdmin ? 'bg-gray-900 text-white' : 'bg-white text-black'
    ]"
  >
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <router-link to="/" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
          <img src="/assets/bitchest_logo.png" alt="BitChest Logo" class="h-10 w-auto" />
        </router-link>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link
            v-for="link in filteredNavLinks"
            :key="link.to"
            :to="link.to"
            class="text-sm font-medium transition-all duration-200 relative"
            :class="[
              isAdmin 
                ? 'hover:text-gray-300 text-gray-100' 
                : 'hover:text-blue-600 text-gray-800',
              'after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5',
              isAdmin ? 'after:bg-gray-300' : 'after:bg-blue-600',
              'after:transition-all after:duration-300 hover:after:w-full'
            ]"
            active-class="font-semibold"
          >
            {{ link.text }}
          </router-link>
        </div>

        <!-- Actions -->
        <div class="hidden md:flex items-center space-x-4 ml-auto">
          <!-- Notifications -->
          <router-link 
            v-if="isLogged" 
            to="/notifications" 
            class="relative p-2 rounded-lg transition-all duration-200"
            :class="isAdmin ? 'hover:bg-white/10' : 'hover:bg-blue-600/10'"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h11z"/>
            </svg>
            <span
              v-if="unreadCount > 0"
              class="absolute top-0 right-0 flex items-center justify-center bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 animate-pulse"
            >{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
          </router-link>

          <!-- Login / Register -->
          <template v-if="!isLogged">
            <router-link 
              to="/login" 
              class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition-all duration-200 font-medium"
            >
              Se connecter
            </router-link>
            <router-link 
              to="/register" 
              class="px-4 py-2 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 transition-all duration-200 font-medium"
            >
              S'inscrire
            </router-link>
          </template>

          <!-- Profile Dropdown -->
          <div v-else ref="profileMenuRef" class="relative">
            <button 
              @click="toggleProfileMenu" 
              :class="[
                'flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200',
                showProfileMenu 
                  ? (isAdmin ? 'bg-gray-800' : 'bg-gray-100')
                  : (isAdmin ? 'hover:bg-gray-800' : 'hover:bg-gray-100')
              ]"
            >
              <img
                :src="currentUser?.avatar || '/assets/default-avatar.png'"
                alt="avatar"
                class="h-8 w-8 rounded-full border-2 transition-all duration-200"
                :class="isAdmin ? 'border-gray-600' : 'border-blue-600'"
              />
              <div class="hidden sm:flex flex-col items-start">
                <span class="text-sm font-medium">{{ currentUser?.name || 'Mon compte' }}</span>
                <span class="text-xs opacity-70">{{ currentUser?.role === 'admin' ? 'Admin' : 'Utilisateur' }}</span>
              </div>
              <svg 
                class="h-4 w-4 transition-transform duration-300"
                :class="{ 'rotate-180': showProfileMenu }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <transition 
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 scale-95 translate-y-2"
              enter-to-class="opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100 scale-100 translate-y-0"
              leave-to-class="opacity-0 scale-95 translate-y-2"
            >
              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl z-50 overflow-hidden"
                :class="isAdmin ? 'bg-gray-800 text-white' : 'bg-white text-gray-900'"
              >
                <div :class="['px-4 py-3 border-b', isAdmin ? 'border-gray-700' : 'border-gray-200']">
                  <p class="text-sm font-semibold">{{ currentUser?.name }}</p>
                  <p class="text-xs opacity-70">{{ currentUser?.email }}</p>
                </div>

                <div class="py-2">
                  <router-link 
                    v-if="isAdmin" 
                    to="/admin" 
                    class="block px-4 py-2 text-sm transition-colors duration-150"
                    :class="isAdmin ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
                    @click="closeMenu"
                  >
                    📊 Tableau de bord admin
                  </router-link>
                  <router-link 
                    to="/profile" 
                    class="block px-4 py-2 text-sm transition-colors duration-150"
                    :class="isAdmin ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
                    @click="closeMenu"
                  >
                    👤 Mon profil
                  </router-link>
                  <router-link 
                    v-if="!isAdmin"
                    to="/wallet" 
                    class="block px-4 py-2 text-sm transition-colors duration-150"
                    :class="isAdmin ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
                    @click="closeMenu"
                  >
                    💼 Portefeuille
                  </router-link>
                  <router-link 
                    v-if="!isAdmin"
                    to="/transactions" 
                    class="block px-4 py-2 text-sm transition-colors duration-150"
                    :class="isAdmin ? 'hover:bg-gray-700' : 'hover:bg-gray-100'"
                    @click="closeMenu"
                  >
                    📈 Transactions
                  </router-link>
                </div>

                <div :class="['px-4 py-3 border-t', isAdmin ? 'border-gray-700' : 'border-gray-200']">
                  <button
                    @click="handleLogout"
                    :disabled="isLoggingOut"
                    class="w-full px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
                    :class="[
                      isLoggingOut ? 'opacity-50 cursor-not-allowed' : '',
                      isAdmin 
                        ? 'bg-red-900 hover:bg-red-800 text-white' 
                        : 'bg-red-50 hover:bg-red-100 text-red-600'
                    ]"
                  >
                    <span v-if="!isLoggingOut">🚪 Déconnexion</span>
                    <span v-else class="flex items-center gap-2">
                      <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Déconnexion...
                    </span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Mobile menu toggle -->
        <button 
          @click="isMenuOpen = !isMenuOpen" 
          class="md:hidden p-2 rounded-lg transition-colors duration-200"
          :class="isAdmin ? 'hover:bg-gray-800' : 'hover:bg-gray-200'"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              v-if="!isMenuOpen"
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
            <path
              v-else
              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <transition
      enter-active-class="transition-all duration-300"
      leave-active-class="transition-all duration-300"
      enter-from-class="max-h-0 opacity-0"
      enter-to-class="max-h-96 opacity-100"
      leave-from-class="max-h-96 opacity-100"
      leave-to-class="max-h-0 opacity-0"
    >
      <div v-show="isMenuOpen" class="md:hidden border-t overflow-hidden" :class="isAdmin ? 'border-gray-700' : 'border-gray-200'">
        <div class="py-3 px-4">
          <router-link
            v-for="link in filteredNavLinks"
            :key="link.to"
            :to="link.to"
            class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200"
            :class="isAdmin ? 'hover:bg-gray-800' : 'hover:bg-gray-100'"
            @click="isMenuOpen = false"
          >
            {{ link.text }}
          </router-link>

          <div class="px-3 py-3 border-t mt-3" :class="isAdmin ? 'border-gray-700' : 'border-gray-200'">
            <router-link
              v-if="!isLogged"
              to="/login"
              class="block w-full text-center px-4 py-2 bg-black text-white rounded-lg font-medium mb-2 transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Se connecter
            </router-link>
            <router-link
              v-if="!isLogged"
              to="/register"
              class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-900 rounded-lg font-medium transition-all duration-200"
              @click="isMenuOpen = false"
            >
              S'inscrire
            </router-link>
            <button
              v-if="isLogged"
              @click="handleLogout"
              :disabled="isLoggingOut"
              class="block w-full px-4 py-2 bg-red-500 text-white rounded-lg font-medium transition-all duration-200 disabled:opacity-50"
            >
              {{ isLoggingOut ? 'Déconnexion...' : 'Déconnexion' }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { onClickOutside } from '@vueuse/core'
import { useAuthStore } from '../services/useAuthStore'

const router = useRouter()
const route = useRoute()
const { currentUser, isAuthenticated, isAdmin, isLoggingOut, performLogout } = useAuthStore()

const isMenuOpen = ref(false)
const showProfileMenu = ref(false)
const profileMenuRef = ref<HTMLElement | null>(null)

// Close profile menu when clicking outside
onClickOutside(profileMenuRef, () => {
  showProfileMenu.value = false
})

const isLogged = computed(() => isAuthenticated.value)

// Nav links with role-based filtering
const navLinks = [
  { to: '/', text: 'Accueil', requiresAuth: false },
  { to: '/market', text: 'Marché', requiresAuth: false },
  { to: '/alerts', text: 'Alertes', requiresAuth: true },
  { to: '/admin', text: 'Administration', requiresAuth: true, requiresAdmin: true }
]

const filteredNavLinks = computed(() =>
  navLinks.filter(link => {
    if (link.requiresAuth && !isLogged.value) return false
    if (link.requiresAdmin && !isAdmin.value) return false
    return true
  })
)

// Hide navbar on specific pages
const clientPaths = ['/dashboard', '/portfolio', '/wallet', '/transactions', '/buy', '/sell', '/profile-page', '/alerts-page', '/cryptos', '/history', '/notifications']
const adminPaths = ['/admin']
const hideOnClientArea = computed(() =>
  clientPaths.some(p => route.path.startsWith(p)) ||
  adminPaths.some(p => route.path.startsWith(p))
)

const handleLogout = async () => {
  try {
    await performLogout()
    closeMenu()
    router.push('/')
  } catch (error) {
    console.error('Logout error:', error)
    closeMenu()
  }
}

const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value
}

const closeMenu = () => {
  isMenuOpen.value = false
  showProfileMenu.value = false
}

// Get unread notifications count from localStorage with caching
const getUnreadCount = (): number => {
  const cached = localStorage.getItem('notifications_unread')
  return cached ? parseInt(cached, 10) : 0
}

const unreadCount = ref(getUnreadCount())

// Watch for route changes and close menus
watch(() => route.path, closeMenu)

// Optional: Listen for storage events to update notification count
window.addEventListener('storage', (e) => {
  if (e.key === 'notifications_unread') {
    unreadCount.value = getUnreadCount()
  }
})
</script>

<style scoped>
/* Remove unused animation classes since they're handled by Tailwind */
</style>