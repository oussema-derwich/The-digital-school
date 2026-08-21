import { Router } from 'vue-router'
import { isAuthenticated, currentUser, loadUserFromStorage } from './auth'

export function setupAuthGuard(router: Router) {
  router.beforeEach((to, from, next) => {
    // Load user from storage on first check
    if (!isAuthenticated.value && localStorage.getItem('token')) {
      loadUserFromStorage()
    }

    const requiresAuth = to.meta.requiresAuth
    const requiresAdmin = to.meta.requiresAdmin

    // If route requires authentication
    if (requiresAuth) {
      if (!isAuthenticated.value) {
        // Not authenticated, redirect to login
        next({
          name: 'Login',
          query: { redirect: to.fullPath }
        })
        return
      }

      // If route requires admin role
      if (requiresAdmin && currentUser.value?.role !== 'admin') {
        // Not admin, redirect to dashboard
        next({ name: 'Dashboard' })
        return
      }
    }

    // If already authenticated and trying to access login/register
    if (isAuthenticated.value && (to.name === 'Login' || to.name === 'Register')) {
      // Redirect to dashboard if already logged in
      next({ name: 'Dashboard' })
      return
    }

    // Allow navigation
    next()
  })
}
