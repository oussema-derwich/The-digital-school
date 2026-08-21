import { ref, computed, watch } from 'vue'
import api from './api'

export interface User {
  id: number
  name: string
  email: string
  role: string
  is_active: boolean
  avatar?: string
  phone?: string
  created_at?: string
}

export interface LoginResponse {
  user: User
  token: string
  message?: string
}

// Global state
const currentUser = ref<User | null>(null)
const token = ref<string | null>(null)
const isLoggingOut = ref(false)
const isLoggingIn = ref(false)

// Computed properties
const isAuthenticated = computed(() => !!token.value && !!currentUser.value)
const isAdmin = computed(() => currentUser.value?.role === 'admin')

/**
 * Initialize auth state from localStorage
 */
export function initializeAuth() {
  const storedToken = localStorage.getItem('token')
  const storedUser = localStorage.getItem('user')

  if (storedToken && storedUser) {
    try {
      token.value = storedToken
      currentUser.value = JSON.parse(storedUser)
      normalizeAvatarUrl()
      return true
    } catch (e) {
      console.error('Failed to initialize auth:', e)
      clearAuthState()
      return false
    }
  }
  return false
}

/**
 * Normalize avatar URL to full URL if needed
 */
function normalizeAvatarUrl() {
  if (currentUser.value?.avatar && !currentUser.value.avatar.startsWith('http')) {
    currentUser.value.avatar = `http://localhost:8000/storage/${currentUser.value.avatar}`
  }
}

/**
 * Set user and token
 */
export function setAuth(user: User, authToken: string) {
  currentUser.value = user
  token.value = authToken
  normalizeAvatarUrl()
  localStorage.setItem('token', authToken)
  localStorage.setItem('user', JSON.stringify(currentUser.value))
}

/**
 * Clear all auth state
 */
export function clearAuthState() {
  currentUser.value = null
  token.value = null
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('notifications_unread')
}

/**
 * Perform login
 */
export async function performLogin(email: string, password: string): Promise<LoginResponse> {
  isLoggingIn.value = true
  try {
    const res = await api.post('/auth/login', { email, password })
    const data = res.data

    // Handle both response structures
    const user = data.data?.user || data.user
    const authToken = data.data?.token || data.token

    if (!user || !authToken) {
      throw new Error('Invalid response structure: missing user or token')
    }

    setAuth(user, authToken)
    return { user, token: authToken, message: data.message }
  } catch (error: any) {
    clearAuthState()
    throw error.response?.data || { message: 'Login failed' }
  } finally {
    isLoggingIn.value = false
  }
}

/**
 * Perform logout - optimized for speed
 */
export async function performLogout(): Promise<void> {
  isLoggingOut.value = true
  
  try {
    // Send logout request to backend (non-blocking)
    // Don't wait for response - clear immediately
    api.post('/auth/logout').catch(() => {
      // Silently fail - logout will still work on frontend
    })
  } finally {
    // Clear auth immediately for fast UX
    clearAuthState()
    isLoggingOut.value = false
  }
}

/**
 * Fetch user profile
 */
export async function fetchProfile(): Promise<User> {
  try {
    const res = await api.get('/auth/profile')
    const user = res.data.user || res.data.data

    if (!user) {
      throw new Error('User profile not found')
    }

    currentUser.value = user
    normalizeAvatarUrl()
    localStorage.setItem('user', JSON.stringify(currentUser.value))
    return user
  } catch (error: any) {
    clearAuthState()
    throw error.response?.data || { message: 'Failed to fetch profile' }
  }
}

/**
 * Update user profile
 */
export async function updateUserProfile(payload: any): Promise<User> {
  try {
    const res = await api.put('/auth/profile', payload)
    const user = res.data.user || res.data.data

    if (!user) {
      throw new Error('Invalid response')
    }

    currentUser.value = user
    normalizeAvatarUrl()
    localStorage.setItem('user', JSON.stringify(currentUser.value))
    return user
  } catch (error: any) {
    throw error.response?.data || { message: 'Failed to update profile' }
  }
}

/**
 * Upload avatar
 */
export async function uploadUserAvatar(file: File): Promise<User> {
  try {
    const formData = new FormData()
    formData.append('avatar', file)
    const res = await api.post('/auth/avatar/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    const user = res.data.user || res.data.data

    if (!user) {
      throw new Error('Invalid response')
    }

    currentUser.value = user
    normalizeAvatarUrl()
    localStorage.setItem('user', JSON.stringify(currentUser.value))
    return user
  } catch (error: any) {
    throw error.response?.data || { message: 'Failed to upload avatar' }
  }
}

/**
 * Delete avatar
 */
export async function deleteUserAvatar(): Promise<void> {
  try {
    await api.delete('/auth/avatar')
    if (currentUser.value) {
      currentUser.value.avatar = undefined
      localStorage.setItem('user', JSON.stringify(currentUser.value))
    }
  } catch (error: any) {
    throw error.response?.data || { message: 'Failed to delete avatar' }
  }
}

/**
 * Export reactive properties
 */
export function useAuthStore() {
  return {
    // State
    currentUser,
    token,
    isLoggingOut,
    isLoggingIn,

    // Computed
    isAuthenticated,
    isAdmin,

    // Methods
    initializeAuth,
    setAuth,
    clearAuthState,
    performLogin,
    performLogout,
    fetchProfile,
    updateUserProfile,
    uploadUserAvatar,
    deleteUserAvatar
  }
}

// Watch for token changes to update API interceptor
watch(token, (newToken) => {
  if (newToken) {
    api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  } else {
    delete api.defaults.headers.common['Authorization']
  }
})
