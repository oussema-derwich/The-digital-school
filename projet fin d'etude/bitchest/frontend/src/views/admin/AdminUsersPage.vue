<template>
  <AdminLayout pageTitle="Gestion des Utilisateurs" pageDescription="Gérez les comptes utilisateurs de la plateforme">
    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Nom, email ou rôle..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
          <select
            v-model="selectedStatus"
            @change="handleFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tous les statuts</option>
            <option value="active">Actif</option>
            <option value="inactive">Inactif</option>
          </select>
        </div>
        <div class="flex items-end">
          <button 
            @click="handleFilter"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2"
            :disabled="isLoading"
          >
            <span v-if="!isLoading">🔍</span>
            <span v-else class="animate-spin">⏳</span>
            <span>{{ isLoading ? 'Chargement...' : 'Filtrer' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div v-if="isLoading" class="p-6 text-center text-gray-500">
        ⏳ Chargement des utilisateurs...
      </div>
      <div v-else-if="users.length === 0" class="p-6 text-center text-gray-500">
        Aucun utilisateur trouvé
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50 border-b-2 border-gray-200">
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Nom</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Email</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Date d'inscription</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Rôle</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Statut</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <span class="font-medium text-gray-800">{{ user.name }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-gray-600">{{ user.email }}</td>
              <td class="py-4 px-6 text-gray-600 text-sm">{{ formatDate(user.created_at) }}</td>
              <td class="py-4 px-6">
                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                  {{ user.role === 'admin' ? 'Admin' : 'Client' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    user.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ user.is_active ? 'Actif' : 'Inactif' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="flex gap-2 flex-wrap">
                  <button
                    @click="selectUser(user); showDetailsPanel = true"
                    class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-200 transition"
                  >
                    👁 Voir
                  </button>
                  <button
                    @click="editUser(user)"
                    class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-medium hover:bg-yellow-200 transition"
                  >
                    ✏️ Modifier
                  </button>
                  <button
                    @click="deleteUser(user.id)"
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
        Affichage {{ pagination.from }} à {{ pagination.to }} sur {{ pagination.total }} utilisateurs
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

    <!-- Details Panel (Sidebar) -->
    <div
      v-if="showDetailsPanel"
      class="fixed right-0 top-0 h-screen w-96 bg-white shadow-xl z-50 overflow-y-auto"
    >
      <div class="p-6 border-b border-gray-200 sticky top-0 bg-white">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-bold text-gray-800">Détails Utilisateur</h3>
          <button
            @click="showDetailsPanel = false"
            class="text-gray-500 hover:text-gray-700 text-2xl"
          >
            ✕
          </button>
        </div>
      </div>

      <div v-if="selectedUser" class="p-6 space-y-6">
        <!-- User Avatar and Name -->
        <div class="text-center">
          <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-4xl font-bold mx-auto mb-4">
            {{ selectedUser.name.charAt(0).toUpperCase() }}
          </div>
          <h4 class="text-xl font-bold text-gray-800">{{ selectedUser.name }}</h4>
          <p class="text-gray-500 text-sm">{{ selectedUser.email }}</p>
        </div>

        <!-- User Info -->
        <div class="space-y-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Date d'inscription</p>
            <p class="font-medium text-gray-800">{{ formatDate(selectedUser.created_at) }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Solde</p>
              <p class="font-bold text-lg text-blue-600">{{ formatCurrency(selectedUser.balance) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs text-gray-500 mb-1">Rôle</p>
              <p class="font-bold text-lg text-emerald-600">{{ selectedUser.role === 'admin' ? 'Admin' : 'Client' }}</p>
            </div>
          </div>

          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-600 font-medium mb-2">Rôle: {{ selectedUser.role }}</p>
            <p class="text-xs text-blue-600">Statut: {{ selectedUser.status }}</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="space-y-2">
          <button
            @click="viewHistory(selectedUser.id)"
            class="w-full py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2"
          >
            <span>📜</span>
            <span>Voir l'historique</span>
          </button>
          <button
            @click="editUser(selectedUser)"
            class="w-full py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition"
          >
            Modifier les informations
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Overlay -->
    <div
      v-if="showDetailsPanel"
      @click="showDetailsPanel = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import { useRouter } from 'vue-router'
import { getAdminUsers, deleteAdminUser, type AdminUser, type PaginatedResponse } from '@/services/adminApi'

const router = useRouter()
const searchQuery = ref('')
const selectedStatus = ref('')
const showDetailsPanel = ref(false)
const selectedUser = ref<AdminUser | null>(null)
const isLoading = ref(false)
const currentPage = ref(1)

const users = ref<AdminUser[]>([])
const pagination = ref<PaginatedResponse<AdminUser>['pagination'] | null>(null)

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

const selectUser = (user: AdminUser) => {
  selectedUser.value = user
}

const editUser = (user: AdminUser) => {
  console.log('Edit user:', user)
  // Open edit modal here
}

const deleteUserAction = async (userId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
    try {
      await deleteAdminUser(userId)
      await loadUsers()
      alert('Utilisateur supprimé avec succès')
    } catch (e) {
      alert('Erreur lors de la suppression')
      console.error('Error deleting user:', e)
    }
  }
}

const viewHistory = (userId: number) => {
  router.push(`/admin/transactions?userId=${userId}`)
  showDetailsPanel.value = false
}

const formatDate = (dateString: string): string => {
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR')
  } catch {
    return dateString
  }
}

const formatCurrency = (value: number | undefined): string => {
  if (value === undefined) return '0,00 TND'
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND'
  }).format(value)
}

const handleSearch = () => {
  currentPage.value = 1
  loadUsers()
}

const handleFilter = () => {
  currentPage.value = 1
  loadUsers()
}

const goToPage = (page: number) => {
  if (page >= 1 && pagination.value && page <= pagination.value.last_page) {
    currentPage.value = page
    loadUsers()
  }
}

const loadUsers = async () => {
  isLoading.value = true
  try {
    const response = await getAdminUsers(
      currentPage.value,
      10,
      searchQuery.value || undefined,
      selectedStatus.value === 'active' ? 'active' : selectedStatus.value === 'inactive' ? 'inactive' : undefined
    )
    users.value = response.data
    pagination.value = response.pagination
  } catch (e) {
    console.error('Error loading users:', e)
    alert('Erreur lors du chargement des utilisateurs')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadUsers()
})

// Alias for backward compatibility
const deleteUser = deleteUserAction
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
