<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md transform transition-all duration-300">
      <img src="/assets/bitchest_logo.png" alt="logo" class="h-12 mx-auto mb-6" />
      <h2 class="text-2xl font-bold mb-2 text-center text-gray-900">Se connecter</h2>
      <p class="text-gray-600 text-center mb-6">Accédez à votre compte BitChest</p>
      
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input 
            v-model="email" 
            type="email" 
            required 
            :disabled="isLoading"
            placeholder="votre@email.com"
            class="w-full border-2 border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-blue-500 transition-colors duration-200 disabled:opacity-50"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
          <input 
            v-model="password" 
            type="password" 
            required 
            :disabled="isLoading"
            placeholder="••••••••"
            class="w-full border-2 border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:border-blue-500 transition-colors duration-200 disabled:opacity-50"
          />
        </div>

        <!-- Error message -->
        <transition name="slideDown">
          <div v-if="error" class="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ error }}
          </div>
        </transition>

        <!-- Submit button -->
        <button 
          type="submit"
          :disabled="isLoading" 
          class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <span v-if="!isLoading">Connexion</span>
          <span v-else class="flex items-center gap-2">
            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Connexion en cours...
          </span>
        </button>
      </form>

      <!-- Forgot password & Register links -->
      <div class="mt-6 space-y-3">
        <router-link to="/forgot-password" class="block text-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
          Mot de passe oublié ?
        </router-link>
        <div class="text-center text-gray-600">
          Pas encore de compte ?
          <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
            S'inscrire
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../services/useAuthStore'
import { formatApiError, isValidationError } from '../services/errorHandler'

export default defineComponent({
  name: 'LoginPage',
  setup() {
    const router = useRouter()
    const { performLogin } = useAuthStore()
    
    const email = ref('')
    const password = ref('')
    const error = ref('')
    const isLoading = ref(false)

    const submit = async () => {
      if (isLoading.value) return

      error.value = ''
      isLoading.value = true

      try {
        const response = await performLogin(email.value, password.value)

        // Ensure we have a valid user object
        if (!response.user) {
          throw new Error('Données utilisateur invalides')
        }

        // Redirect based on role - faster with computed property
        if (response.user.role === 'admin') {
          router.push({ name: 'AdminDashboard' }).catch(() => {})
        } else {
          router.push({ name: 'Dashboard' }).catch(() => {})
        }
      } catch (err: any) {
        // Format and display error
        const apiError = formatApiError(err)
        error.value = apiError.message || 'Erreur lors de la connexion. Vérifiez vos identifiants.'

        // Log validation errors if present
        if (isValidationError(err)) {
          console.log('Validation errors:', apiError.errors)
        }
      } finally {
        isLoading.value = false
      }
    }

    return { email, password, error, isLoading, submit }
  }
})
</script>

<style scoped>
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

.slideDown-enter-active {
  animation: slideDown 0.3s ease-out;
}

.slideDown-leave-active {
  animation: slideDown 0.3s ease-out reverse;
}
</style>
