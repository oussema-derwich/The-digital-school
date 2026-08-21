<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Réinitialisation du mot de passe
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div v-if="!emailSent">
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse email
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="email"
                name="email"
                type="email"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <div v-if="emailSent" class="text-sm text-gray-600">
            <p>Un email contenant les instructions pour réinitialiser votre mot de passe a été envoyé à {{ email }}.</p>
            <p class="mt-2">Si vous ne recevez pas l'email, vérifiez votre dossier spam ou réessayez.</p>
          </div>

          <div>
            <button
              v-if="!emailSent"
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              {{ loading ? 'Envoi...' : 'Envoyer le lien de réinitialisation' }}
            </button>
          </div>

          <div v-if="error" class="text-red-600 text-sm mt-2">
            {{ error }}
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="relative flex justify-center text-sm">
              <router-link
                to="/login"
                class="text-blue-600 hover:text-blue-500"
              >
                Retour à la connexion
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import api from '@/services/api'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'ForgotPassword',

  setup() {
    const router = useRouter()
    const email = ref('')
    const loading = ref(false)
    const error = ref('')
    const emailSent = ref(false)

    const handleSubmit = async () => {
      loading.value = true
      error.value = ''

      try {
        await api.post('/forgot-password', { email: email.value })
        emailSent.value = true
      } catch (err: any) {
        error.value = err.response?.data?.message || 'Une erreur est survenue'
      } finally {
        loading.value = false
      }
    }

    return {
      email,
      loading,
      error,
      emailSent,
      handleSubmit
    }
  }
})
</script>