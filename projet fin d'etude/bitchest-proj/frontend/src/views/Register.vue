<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#E6F0FF] to-[#F3E8FF]">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
      <div class="flex items-center justify-center">
        <img src="/assets/signup.png" alt="signup" class="max-w-full h-64 object-contain" />
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Créer un compte</h2>
        <p class="text-gray-600 text-sm mb-4">Remplissez le formulaire ci-dessous. Vous recevrez un email avec votre mot de passe après approbation de l'administrateur.</p>
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
            <input v-model="name" placeholder="Ex: Jean Dupont" required class="w-full border px-3 py-2 rounded" />
            <div v-if="errors?.name" class="text-red-600 text-sm mt-1">{{ errors.name.join(' ') }}</div>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="email" type="email" placeholder="example@email.com" required class="w-full border px-3 py-2 rounded" />
            <div v-if="errors?.email" class="text-red-600 text-sm mt-1">{{ errors.email.join(' ') }}</div>
          </div>
          <div v-if="error" class="text-red-600 mb-2 text-sm bg-red-50 p-2 rounded">{{ error }}</div>
          <div v-if="success" class="text-green-600 mb-2 text-sm bg-green-50 p-2 rounded">{{ success }}</div>
          <button :disabled="isLoading" :class="{ 'opacity-50 cursor-not-allowed': isLoading }" class="w-full btn-accent">
            {{ isLoading ? 'Envoi en cours...' : 'Soumettre la demande' }}
          </button>
        </form>
        <p class="text-gray-600 text-xs mt-4 text-center">
          Vous avez déjà un compte? <router-link to="/login" class="text-blue-600 hover:underline">Se connecter</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { createRegistrationRequest } from '../services/registrationApi'
import { formatApiError, isValidationError } from '../services/errorHandler'

export default defineComponent({
  setup() {
    const router = useRouter()
    const name = ref('')
    const email = ref('')
    const error = ref('')
    const success = ref('')
    const isLoading = ref(false)
    const errors = ref<Record<string, string[] | undefined> | null>(null)

    const submit = async () => {
      if (isLoading.value) return

      error.value = ''
      success.value = ''
      errors.value = null

      isLoading.value = true

      try {
        // Submission de la demande d'inscription avec email et nom seulement
        const response = await createRegistrationRequest({
          name: name.value,
          email: email.value,
          role: 'client' // Automatically set to client
        })

        success.value = 'Demande d\'inscription créée avec succès! Vous recevrez un email avec votre mot de passe après approbation de l\'administrateur.'
        name.value = ''
        email.value = ''
        setTimeout(() => router.push({ name: 'Login' }), 3000)
      } catch (err: any) {
        const apiError = formatApiError(err)
        error.value = apiError.message || 'Erreur lors de l\'inscription.'
        if (isValidationError(err)) {
          errors.value = apiError.errors
        }
      } finally {
        isLoading.value = false
      }
    }

    return { name, email, error, success, errors, isLoading, submit }
  }
})
</script>
