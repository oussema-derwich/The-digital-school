<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <ClientSidebar />
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">BC</div>
          <h1 class="text-xl font-bold text-gray-900">Mon Profil</h1>
        </div>
        <div class="flex items-center gap-4">
          <router-link 
            to="/notifications" 
            class="relative text-gray-600 hover:text-blue-600 transition"
          >
            🔔
            <span v-if="unreadCount > 0" class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
          </router-link>
          <span class="text-sm font-medium text-gray-700">{{ currentUser?.name }}</span>
          <button
            @click="handleLogout"
            :disabled="isLoggingOut"
            class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition disabled:opacity-50"
          >
            {{ isLoggingOut ? '...' : 'Déconnexion' }}
          </button>
        </div>
      </header>

      <!-- Scrollable Content -->
      <main class="flex-1 overflow-auto p-6 lg:p-8">
        <div class="max-w-4xl mx-auto space-y-6">
          <!-- Informations personnelles -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5">
              <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informations Personnelles
              </h2>
              
              <form @submit.prevent="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="Votre nom"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                      v-model="form.email"
                      type="email"
                      required
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="votre@email.com"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input
                      v-model="form.phone"
                      type="tel"
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="+33 6 XX XX XX XX"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                    <input
                      :value="currentUser?.role"
                      type="text"
                      disabled
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                    />
                  </div>
                </div>

                <div class="flex justify-end pt-4">
                  <button
                    type="submit"
                    :disabled="updatingProfile"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 transition-all disabled:opacity-50 flex items-center gap-2"
                  >
                    <span v-if="!updatingProfile">💾 Enregistrer</span>
                    <span v-else class="flex items-center gap-2">
                      <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Enregistrement...
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Photo de profil -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5">
              <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Photo de Profil
              </h2>

              <div class="flex items-center gap-6">
                <!-- Avatar Preview -->
                <div class="flex-shrink-0">
                  <img
                    v-if="currentUser?.avatar"
                    :src="currentUser.avatar"
                    alt="Avatar"
                    class="h-24 w-24 rounded-lg object-cover border-4 border-blue-100"
                    @error="$event.target.src = '/assets/default-avatar.png'"
                  />
                  <div
                    v-else
                    class="h-24 w-24 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-3xl font-bold text-blue-600"
                  >
                    {{ (currentUser?.name || 'U').charAt(0).toUpperCase() }}
                  </div>
                </div>

                <!-- Upload Section -->
                <div class="flex-1">
                  <div class="flex items-center gap-3">
                    <input
                      ref="fileInput"
                      type="file"
                      accept="image/*"
                      @change="handleAvatarUpload"
                      class="hidden"
                    />
                    <button
                      type="button"
                      @click="$refs.fileInput.click()"
                      class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-medium hover:bg-blue-100 transition-colors"
                    >
                      Télécharger
                    </button>
                    <button
                      v-if="currentUser?.avatar"
                      type="button"
                      @click="deleteAvatar"
                      class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors"
                    >
                      Supprimer
                    </button>
                  </div>
                  <p class="text-sm text-gray-500 mt-2">JPG, PNG ou GIF (Max 5MB)</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Mot de passe -->
          <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5">
              <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Sécurité
              </h2>

              <form @submit.prevent="updatePassword" class="space-y-4">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                    <input
                      v-model="passwordForm.old_password"
                      type="password"
                      required
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="••••••••"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input
                      v-model="passwordForm.password"
                      type="password"
                      required
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="••••••••"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                    <input
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      required
                      class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                      placeholder="••••••••"
                    />
                  </div>
                </div>

                <div class="flex justify-end pt-4">
                  <button
                    type="submit"
                    :disabled="updatingPassword"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 transition-all disabled:opacity-50 flex items-center gap-2"
                  >
                    <span v-if="!updatingPassword">🔐 Changer le mot de passe</span>
                    <span v-else class="flex items-center gap-2">
                      <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Mise à jour...
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Messages -->
          <transition name="slideDown">
            <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
              <p class="text-green-700 font-medium">✅ {{ successMessage }}</p>
            </div>
          </transition>

          <transition name="slideDown">
            <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
              <p class="text-red-700 font-medium">❌ {{ errorMessage }}</p>
            </div>
          </transition>
        </div>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../services/useAuthStore'
import ClientSidebar from '../components/ClientSidebar.vue'
import api from '../services/api'

export default defineComponent({
  name: 'ProfilePage',
  components: { ClientSidebar },
  setup() {
    const router = useRouter()
    const { currentUser, isLoggingOut, performLogout, updateUserProfile, uploadUserAvatar, deleteUserAvatar } = useAuthStore()

    const form = ref({
      name: '',
      email: '',
      phone: ''
    })

    const passwordForm = ref({
      old_password: '',
      password: '',
      password_confirmation: ''
    })

    const fileInput = ref<HTMLInputElement | null>(null)
    const updatingProfile = ref(false)
    const updatingPassword = ref(false)
    const successMessage = ref('')
    const errorMessage = ref('')

    const unreadCount = computed(() => {
      const val = localStorage.getItem('notifications_unread')
      return val ? parseInt(val, 10) : 0
    })

    const showMessage = (message: string, isError = false) => {
      if (isError) {
        errorMessage.value = message
        successMessage.value = ''
      } else {
        successMessage.value = message
        errorMessage.value = ''
      }
      setTimeout(() => {
        successMessage.value = ''
        errorMessage.value = ''
      }, 3000)
    }

    const updateProfile = async () => {
      updatingProfile.value = true
      try {
        await updateUserProfile({
          name: form.value.name,
          email: form.value.email,
          phone: form.value.phone
        })
        showMessage('Profil mis à jour avec succès!')
      } catch (error: any) {
        showMessage(error.message || 'Erreur lors de la mise à jour du profil', true)
      } finally {
        updatingProfile.value = false
      }
    }

    const updatePassword = async () => {
      if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
        showMessage('Les mots de passe ne correspondent pas', true)
        return
      }

      updatingPassword.value = true
      try {
        await api.post('/auth/change-password', {
          old_password: passwordForm.value.old_password,
          password: passwordForm.value.password,
          password_confirmation: passwordForm.value.password_confirmation
        })
        showMessage('Mot de passe changé avec succès!')
        passwordForm.value = { old_password: '', password: '', password_confirmation: '' }
      } catch (error: any) {
        showMessage(error.response?.data?.message || 'Erreur lors du changement de mot de passe', true)
      } finally {
        updatingPassword.value = false
      }
    }

    const handleAvatarUpload = async (event: Event) => {
      const target = event.target as HTMLInputElement
      const file = target.files?.[0]
      if (!file) return

      if (file.size > 5 * 1024 * 1024) {
        showMessage('Le fichier ne doit pas dépasser 5MB', true)
        return
      }

      updatingProfile.value = true
      try {
        await uploadUserAvatar(file)
        showMessage('Avatar mise à jour avec succès!')
      } catch (error: any) {
        showMessage(error.message || 'Erreur lors du téléchargement', true)
      } finally {
        updatingProfile.value = false
        if (target) target.value = ''
      }
    }

    const deleteAvatar = async () => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer votre avatar ?')) return

      updatingProfile.value = true
      try {
        await deleteUserAvatar()
        showMessage('Avatar supprimé avec succès!')
      } catch (error: any) {
        showMessage(error.message || 'Erreur lors de la suppression', true)
      } finally {
        updatingProfile.value = false
      }
    }

    const handleLogout = async () => {
      try {
        await performLogout()
        router.push('/').catch(() => {})
      } catch (e) {
        console.error('Logout error:', e)
      }
    }

    onMounted(() => {
      if (currentUser.value) {
        form.value.name = currentUser.value.name
        form.value.email = currentUser.value.email
        form.value.phone = currentUser.value.phone || ''
      }
    })

    return {
      form,
      passwordForm,
      fileInput,
      currentUser,
      isLoggingOut,
      updatingProfile,
      updatingPassword,
      successMessage,
      errorMessage,
      unreadCount,
      updateProfile,
      updatePassword,
      handleAvatarUpload,
      deleteAvatar,
      handleLogout
    }
  }
})
</script>

<style scoped>
.slideDown-enter-active {
  animation: slideDown 0.3s ease-out;
}

.slideDown-leave-active {
  animation: slideDown 0.3s ease-out reverse;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>