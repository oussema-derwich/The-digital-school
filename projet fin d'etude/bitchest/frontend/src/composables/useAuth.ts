import { computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../services/useAuthStore'

/**
 * Composable for using auth store in components
 */
export function useAuth() {
  const router = useRouter()
  const authStore = useAuthStore()

  // Watch for auth changes and redirect if needed
  watch(() => authStore.isAuthenticated, (isAuth) => {
    if (!isAuth && router.currentRoute.value.meta?.requiresAuth) {
      router.push('/login').catch(() => {})
    }
  })

  return {
    currentUser: authStore.currentUser,
    token: authStore.token,
    isAuthenticated: authStore.isAuthenticated,
    isAdmin: authStore.isAdmin,
    isLoggingOut: authStore.isLoggingOut,
    isLoggingIn: authStore.isLoggingIn,
    performLogin: authStore.performLogin,
    performLogout: authStore.performLogout,
    fetchProfile: authStore.fetchProfile,
    updateUserProfile: authStore.updateUserProfile,
    uploadUserAvatar: authStore.uploadUserAvatar,
    deleteUserAvatar: authStore.deleteUserAvatar
  }
}
