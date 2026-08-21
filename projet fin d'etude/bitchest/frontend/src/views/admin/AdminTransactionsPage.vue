<template>
  <AdminLayout pageTitle="Suivi des Transactions" pageDescription="Assurance et traçabilité de toutes les transactions">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
          <input
            v-model="filterUser"
            @input="handleFilter"
            type="text"
            placeholder="Rechercher..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
          <select 
            v-model="filterType"
            @change="handleFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tous</option>
            <option value="buy">Achat</option>
            <option value="sell">Vente</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
          <select 
            v-model="filterStatus"
            @change="handleFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="completed">Confirmé</option>
            <option value="failed">Échoué</option>
          </select>
        </div>
        <div class="flex items-end">
          <button 
            @click="handleFilter"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
          >
            🔄 Rafraîchir
          </button>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div v-if="isLoading" class="p-6 text-center text-gray-500">
        ⏳ Chargement des transactions...
      </div>
      <div v-else-if="transactions.length === 0" class="p-6 text-center text-gray-500">
        Aucune transaction trouvée
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50 border-b-2 border-gray-200">
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Date</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Utilisateur</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Type</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Crypto</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Quantité</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Prix unitaire</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Montant total</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Statut</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in transactions" :key="tx.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
              <td class="py-4 px-6 text-gray-600 text-sm">{{ formatDate(tx.created_at) }}</td>
              <td class="py-4 px-6 font-medium text-gray-800">{{ tx.user?.name || 'N/A' }}</td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    tx.type === 'buy'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ tx.type === 'buy' ? 'Achat' : 'Vente' }}
                </span>
              </td>
              <td class="py-4 px-6 font-semibold text-gray-800">{{ tx.crypto?.symbol || 'N/A' }}</td>
              <td class="py-4 px-6 text-gray-700">{{ tx.quantity }}</td>
              <td class="py-4 px-6 text-gray-700">{{ formatCurrency(tx.price) }}</td>
              <td class="py-4 px-6 font-bold text-lg text-gray-800">{{ formatCurrency(tx.total) }}</td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    tx.status === 'completed'
                      ? 'bg-green-100 text-green-800'
                      : tx.status === 'pending'
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ formatStatus(tx.status) }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="flex gap-2 flex-wrap">
                  <button
                    @click="viewTransactionDetails(tx)"
                    class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-200 transition"
                  >
                    🔍 Détails
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
        Affichage {{ pagination.from }} à {{ pagination.to }} sur {{ pagination.total }} transactions
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

    <!-- Details Modal -->
    <div v-if="showDetailsModal && selectedTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-gray-800">Détails de la transaction</h3>
          <button @click="showDetailsModal = false" class="text-2xl text-gray-500">✕</button>
        </div>

        <div class="space-y-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Date</p>
            <p class="font-medium text-gray-800">{{ formatDate(selectedTransaction.created_at) }}</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Utilisateur</p>
            <p class="font-medium text-gray-800">{{ selectedTransaction.user?.name || 'N/A' }}</p>
            <p class="text-xs text-gray-600">{{ selectedTransaction.user?.email || '' }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Type</p>
              <p class="font-medium text-gray-800">{{ selectedTransaction.type === 'buy' ? 'Achat' : 'Vente' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Crypto</p>
              <p class="font-medium text-gray-800">{{ selectedTransaction.crypto?.symbol || 'N/A' }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Quantité</p>
              <p class="font-bold text-gray-800">{{ selectedTransaction.quantity }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Prix unitaire</p>
              <p class="font-bold text-gray-800">{{ formatCurrency(selectedTransaction.price) }}</p>
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
            <p class="text-xs text-blue-600 mb-1">Montant total</p>
            <p class="text-2xl font-bold text-blue-600">{{ formatCurrency(selectedTransaction.total) }}</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Statut</p>
            <p class="font-medium text-gray-800">{{ formatStatus(selectedTransaction.status) }}</p>
          </div>

          <div v-if="selectedTransaction.fee" class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Frais</p>
            <p class="font-medium text-gray-800">{{ formatCurrency(selectedTransaction.fee) }}</p>
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
import { ref, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import { getAdminTransactions, type AdminTransaction, type PaginatedResponse } from '@/services/adminApi'

const showDetailsModal = ref(false)
const selectedTransaction = ref<AdminTransaction | null>(null)
const isLoading = ref(false)
const filterUser = ref('')
const filterType = ref('')
const filterStatus = ref('')
const currentPage = ref(1)

const transactions = ref<AdminTransaction[]>([])
const pagination = ref<PaginatedResponse<AdminTransaction>['pagination'] | null>(null)

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

const formatCurrency = (value: number | undefined): string => {
  if (value === undefined) return '0,00 TND'
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND'
  }).format(value)
}

const formatDate = (dateString: string): string => {
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateString
  }
}

const formatStatus = (status: string | undefined): string => {
  const statusMap: Record<string, string> = {
    'pending': 'En attente',
    'completed': 'Confirmé',
    'failed': 'Échoué'
  }
  return statusMap[status || ''] || status || 'Inconnu'
}

const viewTransactionDetails = (tx: AdminTransaction) => {
  selectedTransaction.value = tx
  showDetailsModal.value = true
}

const handleFilter = () => {
  currentPage.value = 1
  loadTransactions()
}

const goToPage = (page: number) => {
  if (page >= 1 && pagination.value && page <= pagination.value.last_page) {
    currentPage.value = page
    loadTransactions()
  }
}

const loadTransactions = async () => {
  isLoading.value = true
  try {
    const response = await getAdminTransactions(
      currentPage.value,
      10,
      filterType.value || undefined,
      undefined, // filterCrypto
      filterUser.value || undefined,
      filterStatus.value || undefined
    )
    transactions.value = response.data
    pagination.value = response.pagination
  } catch (error) {
    console.error('Error loading transactions:', error)
    alert('Erreur lors du chargement des transactions')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadTransactions()
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
