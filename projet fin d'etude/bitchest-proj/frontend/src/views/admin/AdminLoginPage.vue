<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
      <!-- Logo -->
      <div class="flex justify-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center text-white text-3xl font-bold">
          BC
        </div>
      </div>

      <!-- Title -->
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">BitChest Admin</h1>
      <p class="text-center text-gray-500 mb-8">Espace d'administration sécurisé</p>

      <!-- Form -->
      <form @submit.prevent="login" class="space-y-6">
        <!-- Email Field -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="admin@bitchest.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            required
          />
        </div>

        <!-- Password Field -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            required
          />
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3">
          <span class="text-2xl">⚠️</span>
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <span v-if="loading">⏳</span>
          <span>{{ loading ? 'Connexion en cours...' : 'Se connecter' }}</span>
        </button>
      </form>

      <!-- Info Footer -->
      <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-xs text-gray-600 text-center">
          <strong>Compte admin pour test:</strong><br/>
          Email: admin@bitchest.com<br/>
          Mot de passe: 12345678
        </p>
      </div>

      <!-- Security Info -->
      <p class="text-xs text-gray-500 text-center mt-6 flex items-center justify-center gap-2">
        <span>🔒</span>
        Connexion sécurisée avec JWT
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const loading = ref(false)
const error = ref('')

const form = ref({
  email: '',
  password: ''
})

const login = async () => {
  if (loading.value) return // Empêcher les clics multiples
  
  loading.value = true
  error.value = ''

  try {
    const response = await api.post('/auth/login', form.value)
    
    // Vérifier que l'utilisateur est admin
    if (response.data.user.role !== 'admin' && response.data.user.role !== 'Administrator') {
      error.value = 'Accès refusé. Vous n\'êtes pas administrateur.'
      loading.value = false
      return
    }

    // Stocker le token
    localStorage.setItem('token', response.data.token || response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))

    // Redirection vers dashboard admin
    router.push('/admin/dashboard')
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Identifiants invalides'
    console.error('Login error:', e)
    loading.value = false
  }
}
</script>

<style scoped>
input::placeholder {
  color: #d1d5db;
}
</style>
