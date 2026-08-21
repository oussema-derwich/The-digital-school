import { ref } from 'vue'
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

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface UpdateProfilePayload {
  name?: string
  email?: string
  phone?: string
}

export const currentUser = ref<User | null>(null)
export const isAuthenticated = ref(false)
export const token = ref<string | null>(null)

export function loadUserFromStorage() {
  const storedToken = localStorage.getItem('token')
  const storedUser = localStorage.getItem('user')
  
  if (storedToken && storedUser) {
    try {
      token.value = storedToken
      currentUser.value = JSON.parse(storedUser)
      
      // Fix avatar URL if it's just a filename
      if (currentUser.value?.avatar && !currentUser.value.avatar.startsWith('http')) {
        currentUser.value.avatar = `http://localhost:8000/storage/${currentUser.value.avatar}`
      }
      
      isAuthenticated.value = true
      return true
    } catch (e) {
      console.error('Failed to load user from storage:', e)
      clearAuth()
      return false
    }
  }
  return false
}

export function setUser(user: User, authToken: string) {
  // Ensure avatar is a full URL
  if (user.avatar && !user.avatar.startsWith('http')) {
    user.avatar = `http://localhost:8000/storage/${user.avatar}`
  }
  
  currentUser.value = user
  token.value = authToken
  isAuthenticated.value = true
  localStorage.setItem('token', authToken)
  localStorage.setItem('user', JSON.stringify(user))
}

export function clearAuth() {
  currentUser.value = null
  token.value = null
  isAuthenticated.value = false
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}

// ===== Authentication APIs =====

export async function login(email: string, password: string): Promise<LoginResponse> {
  try {
    const res = await api.post('/auth/login', { email, password })
    
    // Handle different response structures
    const data = res.data
    let user = null
    let token = null

    // Check for nested data structure (data.data.user, data.data.token)
    if (data.data?.user && data.data?.token) {
      user = data.data.user
      token = data.data.token
    }
    // Check for direct structure (data.user, data.token)
    else if (data.user && data.token) {
      user = data.user
      token = data.token
    }

    if (user && token) {
      setUser(user, token)
      return { user, token, message: data.message }
    }

    throw new Error('Structure de réponse invalide: utilisateur ou token manquant')
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur de connexion' }
  }
}

export async function register(payload: RegisterPayload): Promise<LoginResponse> {
  try {
    const res = await api.post('/auth/register', payload)
    
    // Handle different response structures
    const data = res.data
    let user = null
    let token = null

    // Check for nested data structure
    if (data.data?.user && data.data?.token) {
      user = data.data.user
      token = data.data.token
    }
    // Check for direct structure
    else if (data.user && data.token) {
      user = data.user
      token = data.token
    }

    if (user && token) {
      setUser(user, token)
      return { user, token, message: data.message }
    }

    throw new Error('Structure de réponse invalide')
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'inscription' }
  }
}

export async function logout(): Promise<void> {
  try {
    await api.post('/auth/logout')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  } finally {
    clearAuth()
  }
}

export async function fetchUserProfile(): Promise<User> {
  try {
    const res = await api.get('/auth/profile')
    if (res.data.user) {
      let user = res.data.user
      
      // Ensure avatar is a full URL
      if (user.avatar && !user.avatar.startsWith('http')) {
        user.avatar = `http://localhost:8000/storage/${user.avatar}`
      }
      
      localStorage.setItem('user', JSON.stringify(user))
      currentUser.value = user
      return user
    }
    throw new Error('Profil utilisateur non trouvé')
  } catch (error: any) {
    console.error('Failed to fetch user profile:', error)
    clearAuth()
    throw error.response?.data || { message: 'Erreur lors de la récupération du profil' }
  }
}

export async function updateProfile(payload: UpdateProfilePayload): Promise<User> {
  try {
    const res = await api.put('/auth/profile', payload)
    if (res.data.user) {
      let user = res.data.user
      if (user.avatar && !user.avatar.startsWith('http')) {
        user.avatar = `http://localhost:8000/storage/${user.avatar}`
      }
      localStorage.setItem('user', JSON.stringify(user))
      currentUser.value = user
      return user
    }
    throw new Error('Réponse du serveur invalide')
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la mise à jour du profil' }
  }
}

export async function uploadAvatar(file: File): Promise<User> {
  try {
    const formData = new FormData()
    formData.append('avatar', file)
    const res = await api.post('/auth/avatar/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (res.data.user) {
      let user = res.data.user
      if (user.avatar && !user.avatar.startsWith('http')) {
        user.avatar = `http://localhost:8000/storage/${user.avatar}`
      }
      localStorage.setItem('user', JSON.stringify(user))
      currentUser.value = user
      return user
    }
    throw new Error('Réponse du serveur invalide')
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors du téléchargement de l\'avatar' }
  }
}

export async function deleteAvatar(): Promise<void> {
  try {
    await api.delete('/auth/avatar')
    if (currentUser.value) {
      currentUser.value.avatar = undefined
      localStorage.setItem('user', JSON.stringify(currentUser.value))
    }
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de l\'avatar' }
  }
}
