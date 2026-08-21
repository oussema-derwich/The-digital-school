import { ref } from 'vue'
import { currentUser, fetchUserProfile, updateProfile, uploadAvatar, deleteAvatar } from '@/services/auth'
import { formatApiError } from '@/services/errorHandler'

export interface ProfileState {
  loading: boolean
  error: string | null
  success: string | null
}

export function useUserProfile() {
  const state = ref<ProfileState>({
    loading: false,
    error: null,
    success: null
  })

  // Fetch user profile
  const loadProfile = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      const user = await fetchUserProfile()
      return user
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Update user profile
  const handleUpdateProfile = async (data: { name?: string; email?: string; phone?: string }) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      const user = await updateProfile(data)
      state.value.success = 'Profil mis à jour avec succès'
      return user
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Upload avatar
  const handleUploadAvatar = async (file: File) => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      const user = await uploadAvatar(file)
      state.value.success = 'Avatar mis à jour avec succès'
      return user
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Delete avatar
  const handleDeleteAvatar = async () => {
    state.value.loading = true
    state.value.error = null
    state.value.success = null
    try {
      await deleteAvatar()
      state.value.success = 'Avatar supprimé avec succès'
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
    currentUser,
    state: ref(() => state.value),
    loadProfile,
    handleUpdateProfile,
    handleUploadAvatar,
    handleDeleteAvatar,
    clearMessages
  }
}
