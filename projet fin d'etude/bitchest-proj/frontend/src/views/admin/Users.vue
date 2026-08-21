<template>
  <div class="flex flex-col h-screen bg-gray-50">
    <!-- Header with stats -->
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-bold">Client Management</h1>
          <p class="text-gray-500">Manage platform users and account requests</p>
        </div>
        <button
          @click="showNewClientModal = true"
          class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200"
        >
          <span>+</span>
          <span>New Client</span>
        </button>
      </div>

      <ClientStats :stats="stats" />
    </div>

    <!-- Main content -->
    <div class="flex-1 px-6 pb-6">
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Barre d'onglets -->
        <div class="p-4 border-b border-gray-100">
          <div class="inline-flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <button
              v-for="tab in tabs"
              :key="tab.value"
              @click="currentTab = tab.value"
              :class="{
                'bg-white text-gray-900 shadow': currentTab === tab.value,
                'text-gray-500 hover:text-gray-700': currentTab !== tab.value
              }"
              class="px-4 py-2 rounded-lg transition-colors duration-200 text-sm font-medium"
            >
              {{ tab.label }}
            </button>
          </div>
        </div>

        <!-- Barre de recherche et options -->
        <div class="p-4 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 flex-1">
              <div class="relative flex-1 max-w-md">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search by name or email..."
                  class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <span class="absolute right-3 top-2.5 text-gray-400">🔍</span>
              </div>
              
              <div class="flex items-center space-x-2">
                <button
                  @click="viewMode = 'grid'"
                  :class="{ 'text-blue-500': viewMode === 'grid' }"
                  class="p-2 hover:bg-gray-100 rounded-lg"
                >
                  □
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="{ 'text-blue-500': viewMode === 'list' }"
                  class="p-2 hover:bg-gray-100 rounded-lg"
                >
                  ≡
                </button>
              </div>
            </div>
            <button
              @click="refreshData"
              class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 hover:text-gray-700"
              :disabled="loading"
            >
              <span :class="{ 'animate-spin': loading }">🔄</span>
            </button>
          </div>
        </div>

        <LoadingState :loading="loading" :error="error" @retry="refreshData">
          <!-- Liste des utilisateurs -->
          <div v-if="viewMode === 'grid'" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <UserCard
                v-for="user in paginatedUsers"
                :key="user.id"
                :user="user"
                @view-details="viewUserDetails"
                @suspend-user="confirmSuspendUser"
                @delete-user="confirmDeleteUser"
              />
            </div>
          </div>

          <div v-else>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    v-for="header in tableHeaders"
                    :key="header"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    {{ header }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in paginatedUsers" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-sm">
                        {{ getInitials(user.name) }}
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatCurrency(user.balance) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.createdAt) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button
                      @click="viewUserDetails(user.id)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Details
                    </button>
                    <button
                      @click="confirmSuspendUser(user.id)"
                      class="text-orange-600 hover:text-orange-900"
                    >
                      Suspend
                    </button>
                    <button
                      @click="confirmDeleteUser(user.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <Pagination
            v-if="filteredUsers.length > perPage"
            :page="currentPage"
            :per-page="perPage"
            :total="filteredUsers.length"
            @update:page="currentPage = $event"
            class="mt-4"
          />
        </LoadingState>
      </div>
    </div>

    <!-- Modals -->
    <NewClientModal
      v-if="showNewClientModal"
      @close="showNewClientModal = false"
      @submit="createNewClient"
    />

    <ConfirmationModal
      v-if="showConfirmationModal"
      :type="confirmationModalType"
      :user="selectedUser"
      @confirm="handleConfirmation"
      @close="showConfirmationModal = false"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import api from '@/services/api'

interface User {
  id: string;
  name: string;
  email: string;
  status: 'active' | 'suspended' | 'pending' | 'verified';
  role: 'admin' | 'user';
  kycExpiry: string | null;
  balance: number;
  createdAt: string;
}

interface TabItem {
  label: string;
  value: 'active' | 'pending';
}

interface Stats {
  title: string;
  value: string;
  icon: string;
  bgColor: string;
}

// Extend api with type-safe methods
const userApi = {
  getUsers: () => api.get<{ data: User[] }>('/admin/users'),
  createUser: (userData: Partial<User>) => api.post<{ data: User }>('/admin/users', userData),
  suspendUser: (userId: string) => api.post(`/admin/users/${userId}/suspend`),
  deleteUser: (userId: string) => api.delete(`/admin/users/${userId}`)
}
import ClientStats from '@/components/admin/ClientStats.vue'
import UserCard from '@/components/admin/UserCard.vue'
import NewClientModal from '@/components/admin/NewClientModal.vue'
import ConfirmationModal from '@/components/admin/ConfirmationModal.vue'
import LoadingState from '@/components/ui/LoadingState.vue'
import Pagination from '@/components/ui/Pagination.vue'

export default defineComponent({
  name: 'AdminUsers',
  components: {
    ClientStats,
    UserCard,
    NewClientModal,
    ConfirmationModal,
    LoadingState,
    Pagination
  },
  setup() {
    const loading = ref(false)
    const error = ref('')
    const users = ref<User[]>([])
    const stats = ref<Stats[]>([
      { title: 'Total Clients', value: '0', icon: '👥', bgColor: 'bg-blue-50' },
      { title: 'Total Balance', value: '0.00 €', icon: '💰', bgColor: 'bg-green-50' },
      { title: 'Verified', value: '0', icon: '✅', bgColor: 'bg-emerald-50' },
      { title: 'Pending Requests', value: '0', icon: '⏳', bgColor: 'bg-orange-50' }
    ])

    const currentTab = ref<'active' | 'pending'>('active')
    const viewMode = ref<'grid' | 'list'>('grid')
    const searchQuery = ref('')
    const currentPage = ref(1)
    const perPage = ref(12)
    const showNewClientModal = ref(false)
    const showConfirmationModal = ref(false)
    const confirmationModalType = ref<'suspend' | 'delete'>('suspend')
    const selectedUser = ref<User | null>(null)

    const tabs = computed<TabItem[]>(() => [
      { label: 'Clients', value: 'active' },
      { label: 'Account Requests', value: 'pending' }
    ])

    const tableHeaders = ['User', 'Balance', 'Role', 'Registered', 'Actions']

    const filteredUsers = computed(() => {
      let filtered = users.value.filter(user =>
        currentTab.value === 'active' ? user.status === 'active' : user.status === 'pending'
      )

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(
          user =>
            user.name.toLowerCase().includes(query) || user.email.toLowerCase().includes(query)
        )
      }

      return filtered
    })

    const paginatedUsers = computed(() => {
      const start = (currentPage.value - 1) * perPage.value
      const end = start + perPage.value
      return filteredUsers.value.slice(start, end)
    })

    const refreshData = async () => {
      loading.value = true
      error.value = ''
      try {
        const response = await userApi.getUsers()
        users.value = response.data.data
        // Update statistics
        const activeUsers = users.value.filter(u => u.status === 'active')
        const verifiedUsers = users.value.filter(u => u.status === 'verified')
        const pendingUsers = users.value.filter(u => u.status === 'pending')
        const totalBalance = activeUsers.reduce((sum, u) => sum + u.balance, 0)

        stats.value = [
          { title: 'Total Clients', value: users.value.length.toString(), icon: '👥', bgColor: 'bg-blue-50' },
          { title: 'Total Balance', value: formatCurrency(totalBalance), icon: '💰', bgColor: 'bg-green-50' },
          { title: 'Verified', value: verifiedUsers.length.toString(), icon: '✅', bgColor: 'bg-emerald-50' },
          { title: 'Pending Requests', value: pendingUsers.length.toString(), icon: '⏳', bgColor: 'bg-orange-50' }
        ]
      } catch (err) {
        error.value = 'Failed to load users. Please try again.'
        console.error('Error fetching users:', err)
      } finally {
        loading.value = false
      }
    }

    const viewUserDetails = (userId: string) => {
      // Implémenter la navigation vers la page de détails
    }

    const confirmSuspendUser = (userId: string) => {
      const user = users.value.find(u => u.id === userId)
      selectedUser.value = user || null
      confirmationModalType.value = 'suspend'
      showConfirmationModal.value = true
    }

    const confirmDeleteUser = (userId: string) => {
      const user = users.value.find(u => u.id === userId)
      selectedUser.value = user || null
      confirmationModalType.value = 'delete'
      showConfirmationModal.value = true
    }

    const handleConfirmation = async () => {
      if (!selectedUser.value) return

      loading.value = true
      error.value = ''
      try {
        if (confirmationModalType.value === 'suspend') {
          await userApi.suspendUser(selectedUser.value.id)
        } else if (confirmationModalType.value === 'delete') {
          await userApi.deleteUser(selectedUser.value.id)
        }
        
        showConfirmationModal.value = false
        await refreshData()
      } catch (err) {
        error.value = `Failed to ${confirmationModalType.value} user. Please try again.`
        console.error('Error handling user action:', err)
      } finally {
        loading.value = false
      }
    }

    const createNewClient = async (userData: { name: string; email: string; password: string; role: string }) => {
      loading.value = true
      error.value = ''
      try {
        // The modal emits role as a string ('user' or 'admin'), cast to Partial<User> for the API
        await userApi.createUser(userData as unknown as Partial<User>)
        showNewClientModal.value = false
        await refreshData()
      } catch (err) {
        error.value = 'Failed to create new client. Please try again.'
        console.error('Error creating new client:', err)
      } finally {
        loading.value = false
      }
    }

    // Formatters
    const getInitials = (name: string): string => {
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
    }

    const formatCurrency = (amount: number): string => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount)
    }

    const formatDate = (date: string): string => {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      }).format(new Date(date))
    }

    // Load initial data
    onMounted(() => {
      refreshData()
    })

    return {
      loading,
      error,
      stats,
      users,
      currentTab,
      viewMode,
      searchQuery,
      currentPage,
      perPage,
      showNewClientModal,
      showConfirmationModal,
      confirmationModalType,
      selectedUser,
      tabs,
      tableHeaders,
      filteredUsers,
      paginatedUsers,
      refreshData,
      viewUserDetails,
      confirmSuspendUser,
      confirmDeleteUser,
      handleConfirmation,
      createNewClient,
      getInitials,
      formatCurrency,
      formatDate
    }
  }
})
</script>