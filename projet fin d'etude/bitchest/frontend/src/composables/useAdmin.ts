import { ref } from 'vue'
import {
  getAdminUsers,
  getAdminCryptos,
  getAdminTransactions,
  getAdminAlerts,
  getAdminStats,
  getAdminSettings,
  updateAdminSettings,
  createAdminUser,
  updateAdminUser,
  deleteAdminUser,
  getAdminRegistrationRequests,
  approveRegistrationRequest,
  rejectRegistrationRequest
} from '@/services/adminApi'
import { formatApiError } from '@/services/errorHandler'

export interface AdminState {
  users: any[]
  cryptos: any[]
  transactions: any[]
  alerts: any[]
  stats: any
  settings: any
  registrationRequests: any[]
  loading: boolean
  error: string | null
  success: string | null
}

export function useAdmin() {
  const state = ref<AdminState>({
    users: [],
    cryptos: [],
    transactions: [],
    alerts: [],
    stats: null,
    settings: null,
    registrationRequests: [],
    loading: false,
    error: null,
    success: null
  })

  // Load users
  const loadUsers = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getAdminUsers(page, 20)
      state.value.users = response.data || response
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load cryptos
  const loadCryptos = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getAdminCryptos(page, 20)
      state.value.cryptos = response.data || response
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load transactions
  const loadTransactions = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getAdminTransactions(page, 20)
      state.value.transactions = response.data || response
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load alerts
  const loadAlerts = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getAdminAlerts(page, 20)
      state.value.alerts = response.data || response
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load stats
  const loadStats = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.stats = await getAdminStats()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load settings
  const loadSettings = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.settings = await getAdminSettings()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Update settings
  const handleUpdateSettings = async (settings: any) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      state.value.settings = await updateAdminSettings(settings)
      state.value.success = 'Paramètres mis à jour avec succès'
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Create user
  const handleCreateUser = async (data: any) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      const user = await createAdminUser(data)
      await loadUsers()
      state.value.success = 'Utilisateur créé avec succès'
      return user
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Update user
  const handleUpdateUser = async (id: number, data: any) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      const user = await updateAdminUser(id, data)
      await loadUsers()
      state.value.success = 'Utilisateur mis à jour avec succès'
      return user
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Delete user
  const handleDeleteUser = async (id: number) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      await deleteAdminUser(id)
      await loadUsers()
      state.value.success = 'Utilisateur supprimé avec succès'
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Load registration requests
  const loadRegistrationRequests = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getAdminRegistrationRequests(page, 20)
      state.value.registrationRequests = response.data || response
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Approve registration
  const handleApproveRegistration = async (id: number) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      await approveRegistrationRequest(id)
      await loadRegistrationRequests()
      state.value.success = 'Demande approuvée avec succès'
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Reject registration
  const handleRejectRegistration = async (id: number, reason?: string) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      await rejectRegistrationRequest(id, reason)
      await loadRegistrationRequests()
      state.value.success = 'Demande rejetée avec succès'
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Clear messages
  const clearMessages = () => {
    state.value.error = null
    state.value.success = null
  }

  return {
    state,
    loadUsers,
    loadCryptos,
    loadTransactions,
    loadAlerts,
    loadStats,
    loadSettings,
    handleUpdateSettings,
    handleCreateUser,
    handleUpdateUser,
    handleDeleteUser,
    loadRegistrationRequests,
    handleApproveRegistration,
    handleRejectRegistration,
    clearMessages
  }
}
