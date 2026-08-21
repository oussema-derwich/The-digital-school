<template>
  <div class="min-h-screen bg-gray-100">
    <!-- En-tête de l'administration -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4">
        <h1 class="text-3xl font-bold text-gray-900">Administration BitChest</h1>
      </div>
    </header>

    <!-- Menu de navigation admin -->
    <nav class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex space-x-8">
          <a v-for="tab in tabs" 
             :key="tab.id"
             :class="[
               currentTab === tab.id 
                 ? 'border-blue-500 text-blue-600' 
                 : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
               'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer'
             ]"
             @click="currentTab = tab.id">
            {{ tab.name }}
          </a>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main class="max-w-7xl mx-auto py-6 px-4">
      <!-- Dashboard -->
      <div v-if="currentTab === 'dashboard'" class="space-y-6">
        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div v-for="stat in dashboardStats" :key="stat.name"
               class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">{{ stat.name }}</dt>
                    <dd class="flex items-baseline">
                      <div class="text-2xl font-semibold text-gray-900">{{ stat.value }}</div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Graphique d'évolution -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-medium text-gray-900">Évolution du marché</h3>
          <div class="mt-4" style="height: 300px;">
            <!-- Intégration du graphique ici -->
          </div>
        </div>
      </div>

      <!-- Gestion des utilisateurs -->
      <div v-if="currentTab === 'users'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Gestion des utilisateurs</h2>
          <button @click="showAddUserModal = true" 
                  class="btn-primary">
            Ajouter un utilisateur
          </button>
        </div>

        <!-- Table des utilisateurs -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="header in userTableHeaders" 
                    :key="header"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ header }}
                </th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                <td class="px-6 py-4">{{ user.email }}</td>
                <td class="px-6 py-4">{{ user.role === 'admin' ? 'Administrateur' : 'Utilisateur' }}</td>
                <td class="px-6 py-4">
                  <span :class="user.is_active ? 'text-green-600' : 'text-red-600'">
                    {{ user.is_active ? 'Actif' : 'Inactif' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-3">
                  <button @click="editUser(user)" 
                          class="text-blue-600 hover:text-blue-800">
                    Modifier
                  </button>
                  <button @click="deleteUser(user.id)" 
                          class="text-red-600 hover:text-red-800">
                    Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Gestion des cryptomonnaies -->
      <div v-if="currentTab === 'cryptos'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Gestion des cryptomonnaies</h2>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="header in cryptoTableHeaders" 
                    :key="header"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ header }}
                </th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="crypto in cryptos" :key="crypto.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ crypto.name }}</td>
                <td class="px-6 py-4">{{ crypto.symbol }}</td>
                <td class="px-6 py-4">{{ formatPrice(crypto.price) }}</td>
                <td class="px-6 py-4">{{ crypto.transactions_count || 0 }}</td>
                <td class="px-6 py-4 text-right">
                  <button @click="editCrypto(crypto)" 
                          class="text-blue-600 hover:text-blue-800">
                    Modifier
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Suivi des transactions -->
      <div v-if="currentTab === 'transactions'" class="space-y-6">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-900">Historique des transactions</h2>
          <div class="flex space-x-4">
            <select v-model="transactionFilters.type" class="form-select">
              <option value="">Tous les types</option>
              <option value="buy">Achats</option>
              <option value="sell">Ventes</option>
            </select>
            <input type="date" v-model="transactionFilters.date" class="form-input">
          </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="header in transactionTableHeaders" 
                    :key="header"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ header }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transaction in filteredTransactions" :key="transaction.id">
                <td class="px-6 py-4">{{ formatDate(transaction.created_at) }}</td>
                <td class="px-6 py-4">{{ transaction.user.name }}</td>
                <td class="px-6 py-4">{{ transaction.crypto.name }}</td>
                <td class="px-6 py-4">{{ transaction.type === 'buy' ? 'Achat' : 'Vente' }}</td>
                <td class="px-6 py-4">{{ transaction.quantity }}</td>
                <td class="px-6 py-4">{{ formatPrice(transaction.amount) }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end">
                    <button
                      v-if="transaction.status !== 'cancelled'"
                      @click="cancelTransaction(transaction.id)"
                      class="text-red-600 hover:text-red-800"
                    >
                      Annuler
                    </button>
                    <span v-else class="text-gray-500">Annulé</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Gestion des alertes -->
      <div v-if="currentTab === 'alerts'" class="space-y-6">
        <h2 class="text-xl font-semibold text-gray-900">Gestion des alertes</h2>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="header in alertTableHeaders" 
                    :key="header"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ header }}
                </th>
                <th class="px-6 py-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="alert in alerts" :key="alert.id">
                <td class="px-6 py-4">{{ alert.user.name }}</td>
                <td class="px-6 py-4">{{ alert.crypto.name }}</td>
                <td class="px-6 py-4">
                  {{ alert.type === 'above' ? 'Au-dessus de' : 'En-dessous de' }}
                  {{ formatPrice(alert.price_threshold) }}
                </td>
                <td class="px-6 py-4">
                  <span :class="alert.is_active ? 'text-green-600' : 'text-red-600'">
                    {{ alert.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <button @click="toggleAlert(alert)" 
                          :class="alert.is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'">
                    {{ alert.is_active ? 'Désactiver' : 'Activer' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modals -->
    <UserModal 
      v-if="showAddUserModal" 
      :user="selectedUser"
      @close="closeUserModal"
      @save="saveUser"
    />

    <CryptoModal
      v-if="showCryptoModal"
      :crypto="selectedCrypto"
      @close="closeCryptoModal"
      @save="saveCrypto"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import UserModal from '@/components/admin/UserModal.vue'
import CryptoModal from '@/components/admin/CryptoModal.vue'

export default defineComponent({
  name: 'AdminView',
  
  components: {
    UserModal,
    CryptoModal
  },

  setup() {
  const currentTab = ref<string>('dashboard')
  const dashboardStats = ref<Array<{ name: string; value: string | number }>>([])
  const users = ref<Array<Record<string, any>>>([])
  const cryptos = ref<Array<Record<string, any>>>([])
  const transactions = ref<Array<Record<string, any>>>([])
  const alerts = ref<Array<Record<string, any>>>([])

    const tabs: Array<{ id: string; name: string }> = [
      { id: 'dashboard', name: 'Tableau de bord' },
      { id: 'users', name: 'Utilisateurs' },
      { id: 'cryptos', name: 'Cryptomonnaies' },
      { id: 'transactions', name: 'Transactions' },
      { id: 'alerts', name: 'Alertes' }
    ]

  const showAddUserModal = ref<boolean>(false)
  const showCryptoModal = ref<boolean>(false)
  const selectedUser = ref<Record<string, any> | null>(null)
  const selectedCrypto = ref<Record<string, any> | null>(null)

    const transactionFilters = ref<{ type: string; date: string }>({
      type: '',
      date: ''
    })

    // Headers pour les tableaux
    const userTableHeaders: string[] = [
      'Nom',
      'Email',
      'Rôle',
      'Statut'
    ]

    const cryptoTableHeaders: string[] = [
      'Nom',
      'Symbole',
      'Prix actuel',
      'Nombre de transactions'
    ]

    const transactionTableHeaders: string[] = [
      'Date',
      'Utilisateur',
      'Cryptomonnaie',
      'Type',
      'Quantité',
      'Montant'
    ]

    // Add actions column for admin operations
    transactionTableHeaders.push('Actions')

    const alertTableHeaders: string[] = [
      'Utilisateur',
      'Cryptomonnaie',
      'Condition',
      'Statut'
    ]

    // Chargement des données
    const loadDashboard = async () => {
      try {
        // Backend exposes stats at /admin/stats (returns { status, data: { ... } })
        const response = await api.get('/admin/stats')
        const data = response.data && response.data.data ? response.data.data : {}

        // Map keys to friendly labels
        const labelMap: Record<string, string> = {
          activeUsers: 'Utilisateurs actifs',
          totalUsers: 'Total utilisateurs',
          newUsersThisWeek: 'Nouveaux cette semaine',
          totalTransactions: 'Transactions totales',
          totalAlerts: 'Alertes totales',
          totalCryptos: 'Cryptomonnaies'
        }

        dashboardStats.value = Object.entries(data).map(([key, value]) => ({
          name: labelMap[key] || key,
          value: value as string | number
        }))
      } catch (error) {
        console.error('Erreur lors du chargement du tableau de bord:', error)
      }
    }

    const loadUsers = async (query: string = '') => {
      try {
        const response = await api.get('/admin/users', { params: { q: query } })
        // backend may return paginated result or array
        users.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erreur lors du chargement des utilisateurs:', error)
      }
    }

    const loadCryptos = async () => {
      try {
        const response = await api.get('/admin/cryptos')
        cryptos.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erreur lors du chargement des cryptomonnaies:', error)
      }
    }

    const loadTransactions = async () => {
      try {
        // Request many items so admin can see all transactions (no pagination in the UI)
        const response = await api.get('/admin/transactions', { params: { per_page: 10000 } })
        transactions.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erreur lors du chargement des transactions:', error)
      }
    }

    // Cancel a transaction (admin action)
    const cancelTransaction = async (transactionId: number) => {
      if (!confirm('Annuler cette transaction ?')) return
      try {
        await api.post(`/admin/transactions/${transactionId}/cancel`)
        await loadTransactions()
      } catch (error) {
        console.error('Erreur lors de l\'annulation de la transaction:', error)
        alert('Impossible d\'annuler la transaction')
      }
    }

    const loadAlerts = async () => {
      try {
        const response = await api.get('/admin/alerts')
        alerts.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erreur lors du chargement des alertes:', error)
      }
    }

    // Filtrage des transactions
    const filteredTransactions = computed(() => {
      return transactions.value.filter(transaction => {
        if (transactionFilters.value.type && transaction.type !== transactionFilters.value.type) {
          return false
        }
        if (transactionFilters.value.date) {
          const transactionDate = transaction.created_at.split('T')[0]
          if (transactionDate !== transactionFilters.value.date) {
            return false
          }
        }
        return true
      })
    })

    // Gestion des modals
    const editUser = (user: any) => {
      selectedUser.value = { ...user }
      showAddUserModal.value = true
    }

    const editCrypto = (crypto: any) => {
      selectedCrypto.value = { ...crypto }
      showCryptoModal.value = true
    }

    const closeUserModal = () => {
      showAddUserModal.value = false
      selectedUser.value = null
    }

    const closeCryptoModal = () => {
      showCryptoModal.value = false
      selectedCrypto.value = null
    }

    // Actions sur les données
    const saveUser = async (userData: any) => {
      try {
        if (selectedUser.value) {
          await api.put(`/admin/users/${selectedUser.value.id}`, userData)
        } else {
          await api.post('/admin/users', userData)
        }
        await loadUsers()
        closeUserModal()
      } catch (error) {
        console.error('Erreur lors de la sauvegarde de l\'utilisateur:', error)
      }
    }

    const saveCrypto = async (cryptoData: any) => {
      try {
        if (selectedCrypto.value) {
          await api.put(`/admin/cryptos/${selectedCrypto.value.id}`, cryptoData)
        }
        await loadCryptos()
        closeCryptoModal()
      } catch (error) {
        console.error('Erreur lors de la sauvegarde de la cryptomonnaie:', error)
      }
    }

    const deleteUser = async (userId: number) => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return

      try {
        await api.delete(`/admin/users/${userId}`)
        await loadUsers()
      } catch (error) {
        console.error('Erreur lors de la suppression de l\'utilisateur:', error)
      }
    }

    const toggleAlert = async (alert: any) => {
      try {
        await api.put(`/admin/alerts/${alert.id}`, {
          is_active: !alert.is_active
        })
        await loadAlerts()
      } catch (error) {
        console.error('Erreur lors de la modification de l\'alerte:', error)
      }
    }

    // Formatage des données
    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    }

    const formatDate = (date: string) => {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      loadDashboard()
      loadUsers()
      loadCryptos()
      loadTransactions()
      loadAlerts()
    })

    return {
      currentTab,
      tabs,
      dashboardStats,
      users,
      cryptos,
      transactions,
      alerts,
      showAddUserModal,
      showCryptoModal,
      selectedUser,
      selectedCrypto,
      transactionFilters,
      filteredTransactions,
      userTableHeaders,
      cryptoTableHeaders,
      transactionTableHeaders,
      alertTableHeaders,
      editUser,
      editCrypto,
      closeUserModal,
      closeCryptoModal,
      saveUser,
      saveCrypto,
      deleteUser,
      toggleAlert,
      formatPrice,
      formatDate
      ,cancelTransaction
    }
  }
})
</script>

<style scoped>
/* Common styles moved to src/styles/tailwind.css */
</style>
