<template>
  <div class="min-h-screen bg-gray-100 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Mon Profil</h1>

      <!-- Informations personnelles -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h2>
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                  Nom complet
                </label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Adresse email
                </label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="updating"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                {{ updating ? 'Mise à jour...' : 'Mettre à jour' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Changement de mot de passe -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Changer le mot de passe</h2>
          <form @submit.prevent="updatePassword" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">
                  Mot de passe actuel
                </label>
                <input
                  type="password"
                  id="current_password"
                  v-model="passwordForm.current_password"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">
                  Nouveau mot de passe
                </label>
                <input
                  type="password"
                  id="new_password"
                  v-model="passwordForm.new_password"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                  Confirmer le nouveau mot de passe
                </label>
                <input
                  type="password"
                  id="new_password_confirmation"
                  v-model="passwordForm.new_password_confirmation"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="updatingPassword"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                {{ updatingPassword ? 'Mise à jour...' : 'Changer le mot de passe' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Double authentification -->
      <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <TwoFactorAuth
            :initial-enabled="form.two_factor_enabled"
            @status-change="handle2FAStatusChange"
          />
        </div>
      </div>

      <!-- Suppression du compte -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Supprimer mon compte</h2>
          <div class="text-sm text-gray-500 mb-4">
            <p>Cette action est irréversible. Toutes vos données seront définitivement supprimées.</p>
          </div>
          <button
            @click="confirmDelete"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Supprimer mon compte
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import TwoFactorAuth from '@/components/TwoFactorAuth.vue'

export default defineComponent({
  name: 'Profile',
  components: {
    TwoFactorAuth
  },

  setup() {
    const router = useRouter()
    const form = ref({
      name: '',
      email: '',
      two_factor_enabled: false
    })

    const passwordForm = ref({
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    })

    const updating = ref(false)
    const updatingPassword = ref(false)

    const loadProfile = async () => {
      try {
        const response = await api.get('/profile')
        form.value = {
          name: response.data.name,
          email: response.data.email,
          two_factor_enabled: response.data.two_factor_enabled || false
        }
      } catch (error) {
        console.error('Erreur lors du chargement du profil:', error)
      }
    }

    const updateProfile = async () => {
      updating.value = true
      try {
        await api.put('/profile', form.value)
        // Mettre à jour le store/état global si nécessaire
      } catch (error) {
        console.error('Erreur lors de la mise à jour du profil:', error)
      } finally {
        updating.value = false
      }
    }

    const updatePassword = async () => {
      if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
        alert('Les mots de passe ne correspondent pas')
        return
      }

      updatingPassword.value = true
      try {
        await api.put('/profile/password', {
          current_password: passwordForm.value.current_password,
          password: passwordForm.value.new_password,
          password_confirmation: passwordForm.value.new_password_confirmation
        })

        passwordForm.value = {
          current_password: '',
          new_password: '',
          new_password_confirmation: ''
        }
      } catch (error) {
        console.error('Erreur lors du changement de mot de passe:', error)
      } finally {
        updatingPassword.value = false
      }
    }

    const confirmDelete = async () => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')) {
        return
      }

      try {
        await api.delete('/profile')
        // Déconnexion et redirection
        router.push('/login')
      } catch (error) {
        console.error('Erreur lors de la suppression du compte:', error)
      }
    }

    const handle2FAStatusChange = (enabled: boolean) => {
      form.value.two_factor_enabled = enabled
    }

    onMounted(loadProfile)

    return {
      form,
      passwordForm,
      updating,
      updatingPassword,
      updateProfile,
      updatePassword,
      confirmDelete,
      handle2FAStatusChange
    }
  }
})
</script>