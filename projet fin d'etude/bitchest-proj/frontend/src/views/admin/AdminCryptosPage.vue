<template>
  <AdminLayout pageTitle="Gestion des Cryptomonnaies" pageDescription="Gérez le catalogue des cryptomonnaies disponibles">
    <!-- Action Buttons -->
    <div class="flex gap-4 mb-6">
      <button
        @click="showAddModal = true"
        class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2"
      >
        <span>➕</span>
        <span>Ajouter une crypto</span>
      </button>
      <button 
        @click="refreshCryptos"
        :disabled="isRefreshing"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50"
      >
        <span v-if="!isRefreshing">🔄</span>
        <span v-else class="animate-spin">⏳</span>
        <span>{{ isRefreshing ? 'Actualisation...' : 'Actualiser les cours' }}</span>
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
      <input
        v-model="searchQuery"
        @input="handleSearch"
        type="text"
        placeholder="Rechercher par nom ou symbole..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <!-- Message de succès/erreur -->
    <div v-if="successMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
      {{ errorMessage }}
    </div>

    <!-- Cryptos Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div v-if="isLoading" class="p-6 text-center text-gray-500">
        ⏳ Chargement des cryptomonnaies...
      </div>
      <div v-else-if="cryptos.length === 0" class="p-6 text-center text-gray-500">
        Aucune cryptomonnaie trouvée
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50 border-b-2 border-gray-200">
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Logo</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Nom</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Symbole</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Prix actuel</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Variation 24h</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Statut</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="crypto in cryptos" :key="crypto.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
              <td class="py-4 px-6">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-lg">
                  <img v-if="crypto.logo" :src="crypto.logo" :alt="crypto.name" class="w-8 h-8" @error="($event.target as any).src = '/assets/default.png'" />
                  <span v-else>{{ crypto.symbol.charAt(0) }}</span>
                </div>
              </td>
              <td class="py-4 px-6 font-medium text-gray-800">{{ crypto.name }}</td>
              <td class="py-4 px-6 font-bold text-gray-700">{{ crypto.symbol }}</td>
              <td class="py-4 px-6 text-lg font-bold text-gray-800">{{ formatCurrency(crypto.price) }}</td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-flex items-center gap-1 font-bold',
                    (crypto.change_24h || 0) >= 0 ? 'text-green-600' : 'text-red-600'
                  ]"
                >
                  <span>{{ (crypto.change_24h || 0) >= 0 ? '📈' : '📉' }}</span>
                  {{ (crypto.change_24h || 0) >= 0 ? '+' : '' }}{{ ((crypto.change_24h || 0)).toFixed(2) }}%
                </span>
              </td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    crypto.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ crypto.is_active ? 'Actif' : 'Inactif' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="flex gap-2 flex-wrap">
                  <button
                    @click="editCrypto(crypto)"
                    class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-medium hover:bg-yellow-200 transition"
                  >
                    ✏️ Modifier
                  </button>
                  <button
                    @click="viewDetails(crypto)"
                    class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-200 transition"
                  >
                    📊 Détails
                  </button>
                  <button
                    @click="deleteCrypto(crypto.id)"
                    class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs font-medium hover:bg-red-200 transition"
                  >
                    🗑 Supprimer
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.last_page > 1" class="flex items-center justify-between bg-white rounded-xl shadow-md p-4 mb-6">
      <div class="text-sm text-gray-600">
        Affichage {{ pagination.from }} à {{ pagination.to }} sur {{ pagination.total }} cryptomonnaies
      </div>
      <div class="flex gap-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage <= 1"
          class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          ← Précédent
        </button>
        <button
          v-for="page in pageNumbers"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-3 py-2 rounded-lg border',
            currentPage === page
              ? 'bg-blue-600 text-white border-blue-600'
              : 'border-gray-300 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage >= pagination.last_page"
          class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Suivant →
        </button>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">{{ editingCrypto ? 'Modifier une crypto' : 'Ajouter une crypto' }}</h3>

        <form @submit.prevent="saveCrypto" class="space-y-4">
          <!-- Nom -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nom (ex: Bitcoin)</label>
            <input
              v-model="formData.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <!-- Symbole -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Symbole (ex: BTC)</label>
            <input
              v-model="formData.symbol"
              type="text"
              maxlength="10"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <!-- Prix -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prix de référence</label>
            <input
              v-model.number="formData.price"
              type="number"
              step="0.01"
              min="0"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              v-model="formData.description"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="3"
            ></textarea>
          </div>

          <!-- Statut -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">État</label>
            <select v-model="formData.is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option :value="true">Actif</option>
              <option :value="false">Inactif</option>
            </select>
          </div>

          <!-- Erreur de formulaire -->
          <div v-if="formError" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
            {{ formError }}
          </div>

          <!-- Buttons -->
          <div class="flex gap-4 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ isSubmitting ? 'Veuillez patienter...' : (editingCrypto ? 'Modifier' : 'Ajouter') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Details Modal -->
    <div v-if="showDetailsModal && selectedCrypto" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-bold text-gray-800">Détails - {{ selectedCrypto.name }}</h3>
          <button @click="showDetailsModal = false" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
        </div>

        <div class="space-y-4">
          <div class="flex items-center gap-4 pb-4 border-b">
            <img :src="selectedCrypto.logo" :alt="selectedCrypto.name" class="w-12 h-12 rounded-full" @error="($event.target as any).src = '/assets/default.png'" />
            <div>
              <p class="font-bold text-lg">{{ selectedCrypto.name }}</p>
              <p class="text-gray-600">{{ selectedCrypto.symbol }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div>
              <p class="text-gray-600 text-sm">Prix actuel</p>
              <p class="text-2xl font-bold text-gray-800">{{ formatCurrency(selectedCrypto.price) }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Variation (24h)</p>
              <p :class="['text-lg font-bold', (selectedCrypto.change_24h || 0) >= 0 ? 'text-green-600' : 'text-red-600']">
                {{ (selectedCrypto.change_24h || 0) >= 0 ? '+' : '' }}{{ ((selectedCrypto.change_24h || 0)).toFixed(2) }}%
              </p>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Statut</p>
              <p :class="['text-sm font-bold', selectedCrypto.is_active ? 'text-green-600' : 'text-red-600']">
                {{ selectedCrypto.is_active ? 'Actif' : 'Inactif' }}
              </p>
            </div>
            <div v-if="selectedCrypto.description">
              <p class="text-gray-600 text-sm">Description</p>
              <p class="text-gray-700">{{ selectedCrypto.description }}</p>
            </div>
          </div>

          <button
            @click="showDetailsModal = false"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition mt-6"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import { getAdminCryptos, createAdminCrypto,  deleteAdminCrypto,  type AdminCrypto, type PaginatedResponse } from '@/services/adminApi'

const showAddModal = ref(false)
const showDetailsModal = ref(false)
const editingCrypto = ref<AdminCrypto | null>(null)
const selectedCrypto = ref<AdminCrypto | null>(null)
const isSubmitting = ref(false)
const isLoading = ref(false)
const isRefreshing = ref(false)
const formError = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const searchQuery = ref('')
const currentPage = ref(1)

const formData = reactive({
  name: '',
  symbol: '',
  price: 0,
  description: '',
  is_active: true,
  logo: ''
})

const cryptos = ref<AdminCrypto[]>([])
const pagination = ref<PaginatedResponse<AdminCrypto>['pagination'] | null>(null)

const pageNumbers = computed(() => {
  if (!pagination.value) return []
  const pages = []
  const maxPages = Math.min(5, pagination.value.last_page)
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  let end = Math.min(pagination.value.last_page, start + maxPages - 1)
  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1)
  }
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 2
  }).format(value)
}

const clearMessages = () => {
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 5000)
}

const saveCrypto = async () => {
  formError.value = ''
  isSubmitting.value = true

  try {
    if (editingCrypto.value) {
      await updateAdminCrypto(editingCrypto.value.id, {
        name: formData.name,
        symbol: formData.symbol,
        price: formData.price,
        description: formData.description,
        is_active: formData.is_active,
        logo: formData.logo
      })
      successMessage.value = 'Cryptomonnaie modifiée avec succès!'
    } else {
      await createAdminCrypto({
        name: formData.name,
        symbol: formData.symbol,
        price: formData.price,
        description: formData.description,
        is_active: formData.is_active,
        logo: formData.logo
      })
      successMessage.value = 'Cryptomonnaie ajoutée avec succès!'
    }

    await loadCryptos()
    closeModal()
    clearMessages()
  } catch (error: any) {
    console.error('Error saving crypto:', error)
    const message = error.message || 'Une erreur est survenue lors de la sauvegarde'
    formError.value = message
    errorMessage.value = message
  } finally {
    isSubmitting.value = false
  }
}

const editCrypto = (crypto: AdminCrypto) => {
  editingCrypto.value = crypto
  formData.name = crypto.name
  formData.symbol = crypto.symbol
  formData.price = crypto.price
  formData.description = crypto.description || ''
  formData.is_active = crypto.is_active
  formData.logo = crypto.logo || ''
  formError.value = ''
  showAddModal.value = true
}

const viewDetails = (crypto: AdminCrypto) => {
  selectedCrypto.value = crypto
  showDetailsModal.value = true
}

const deleteCryptoAction = async (cryptoId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette crypto ?')) {
    try {
      await deleteAdminCrypto(cryptoId)
      await loadCryptos()
      successMessage.value = 'Cryptomonnaie supprimée avec succès!'
      clearMessages()
    } catch (error: any) {
      console.error('Error deleting crypto:', error)
      const message = error.message || 'Erreur lors de la suppression'
      errorMessage.value = message
      clearMessages()
    }
  }
}

const refreshCryptos = async () => {
  isRefreshing.value = true
  try {
    await loadCryptos()
    successMessage.value = 'Cryptos rafraîchis avec succès!'
    clearMessages()
  } catch (error: any) {
    console.error('Error refreshing cryptos:', error)
    errorMessage.value = error.message || 'Erreur lors du rafraîchissement'
    clearMessages()
  } finally {
    isRefreshing.value = false
  }
}

const closeModal = () => {
  showAddModal.value = false
  resetForm()
}

const resetForm = () => {
  formData.name = ''
  formData.symbol = ''
  formData.price = 0
  formData.description = ''
  formData.is_active = true
  formData.logo = ''
  editingCrypto.value = null
  formError.value = ''
}

const handleSearch = () => {
  currentPage.value = 1
  loadCryptos()
}

const goToPage = (page: number) => {
  if (page >= 1 && pagination.value && page <= pagination.value.last_page) {
    currentPage.value = page
    loadCryptos()
  }
}

const loadCryptos = async () => {
  isLoading.value = true
  try {
    const response = await getAdminCryptos(
      currentPage.value,
      10,
      searchQuery.value || undefined
    )
    cryptos.value = response.data
    pagination.value = response.pagination
  } catch (error: any) {
    console.error('Error loading cryptos:', error)
    const message = error.message || 'Erreur lors du chargement des cryptomonnaies'
    errorMessage.value = message
    clearMessages()
  } finally {
    isLoading.value = false
  }
}

// Alias for backward compatibility
const deleteCrypto = deleteCryptoAction

onMounted(() => {
  loadCryptos()
})
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
