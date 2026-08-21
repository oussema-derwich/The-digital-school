<template>
  <AdminLayout pageTitle="Gestion des Alertes" pageDescription="Supervision des alertes créées par les utilisateurs">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
        <p class="text-gray-500 text-sm mb-2">🚨 Alertes actives</p>
        <p class="text-3xl font-bold text-orange-600">{{ stats.activeAlerts }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm mb-2">✅ Déclenchées aujourd'hui</p>
        <p class="text-3xl font-bold text-green-600">{{ stats.triggeredToday }}</p>
      </div>
      <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm mb-2">⏸️ Mises en pause</p>
        <p class="text-3xl font-bold text-blue-600">{{ stats.paused }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
          <input
            v-model="filterUser"
            type="text"
            placeholder="Rechercher..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Crypto</label>
          <select v-model="filterCrypto" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Toutes</option>
            <option value="BTC">Bitcoin (BTC)</option>
            <option value="ETH">Ethereum (ETH)</option>
            <option value="ADA">Cardano (ADA)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">État</label>
          <select v-model="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Tous</option>
            <option value="Active">Active</option>
            <option value="Pausée">Pausée</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Alerts Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50 border-b-2 border-gray-200">
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Utilisateur</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Crypto</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Seuil</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Type</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Prix actuel</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">État</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Créée le</th>
              <th class="text-left py-4 px-6 text-gray-700 font-semibold text-sm">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="alert in filteredAlerts" :key="alert.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
              <td class="py-4 px-6 font-medium text-gray-800">{{ (alert.user && (alert.user.name || alert.user.email)) || 'N/A' }}</td>
              <td class="py-4 px-6 font-bold text-gray-800">{{ (alert.crypto && (alert.crypto.symbol || alert.crypto.name)) || 'N/A' }}</td>
              <td class="py-4 px-6 font-bold text-lg text-blue-600">{{ formatCurrency(alert.target_value ?? alert.threshold) }}</td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    (alert.alert_type || alert.type) && String(alert.alert_type || alert.type).includes('↑')
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ alert.alert_type ?? alert.type ?? '' }}
                </span>
              </td>
              <td class="py-4 px-6 text-gray-700">{{ formatCurrency(Number(alert.crypto?.current_price ?? alert.currentPrice ?? 0)) }}</td>
              <td class="py-4 px-6">
                <span
                  :class="[
                    'inline-block px-3 py-1 rounded-full text-xs font-medium',
                    isActive(alert) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ alert.status || (isActive(alert) ? 'Active' : 'Pausée') }}
                </span>
              </td>
              <td class="py-4 px-6 text-gray-600 text-sm">{{ formatDate(alert.created_at || alert.createdAt || alert.createdAt) }}</td>
              <td class="py-4 px-6">
                <div class="flex gap-2">
                  <button
                    @click="editAlert(alert)"
                    class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-medium hover:bg-yellow-200 transition"
                  >
                    ✏️ Modifier
                  </button>
                  <button
                    v-if="isActive(alert)"
                    @click="pauseAlert(alert.id)"
                    class="px-3 py-1 bg-orange-100 text-orange-600 rounded-lg text-xs font-medium hover:bg-orange-200 transition"
                  >
                    ⏸ Pauser
                  </button>
                  <button
                    v-else
                    @click="resumeAlert(alert.id)"
                    class="px-3 py-1 bg-green-100 text-green-600 rounded-lg text-xs font-medium hover:bg-green-200 transition"
                  >
                    ▶️ Reprendre
                  </button>
                  <button
                    @click="deleteAlert(alert.id)"
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

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Modifier l'alerte</h3>

        <form @submit.prevent="updateAlert" class="space-y-4">
          <!-- Seuil -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Seuil (DT)</label>
            <input
              v-model.number="editForm.threshold"
              type="number"
              step="0.01"
              min="0"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type d'alerte</label>
            <select v-model="editForm.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="↑ Au-dessus">Au-dessus du seuil</option>
              <option value="↓ Au-dessous">Au-dessous du seuil</option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex gap-4 mt-6">
            <button
              type="button"
              @click="showEditModal = false"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition"
            >
              Modifier
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from './AdminLayout.vue'
import { ref, computed, reactive, onMounted, watch } from 'vue'
import api from '@/services/api'
import { getAdminAlerts, deleteAdminAlert, resumeAdminAlert, type AdminAlert } from '@/services/adminApi'

const filterUser = ref('')
const filterCrypto = ref('')
const filterStatus = ref('')
const showEditModal = ref(false)
const editingAlert = ref<any>(null)

const editForm = reactive({
  threshold: 0,
  type: '↑ Au-dessus'
})

const isLoading = ref(false)
const currentPage = ref(1)
const perPage = ref(10)
const alerts = ref<AdminAlert[]>([])
const pagination = ref<any | null>(null)

const stats = ref({
  activeAlerts: 0,
  triggeredToday: 0,
  paused: 0
})

const pageNumbers = computed(() => {
  if (!pagination.value) return []
  const pages = []
  const maxPages = Math.min(5, pagination.value.last_page || pagination.value.lastPage || 5)
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  let end = Math.min(pagination.value.last_page || pagination.value.lastPage || 1, start + maxPages - 1)
  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1)
  }
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

const filteredAlerts = computed(() => {
  // server-side filters preferred; client filter as extra
  return alerts.value.filter(alert => {
    const matchesUser = !filterUser.value || (alert.user && (alert.user.name || alert.user.email || '').toLowerCase().includes(filterUser.value.toLowerCase()))
    const matchesCrypto = !filterCrypto.value || (alert.crypto && (alert.crypto.symbol || alert.crypto.name) === filterCrypto.value || (alert.crypto && (alert.crypto.symbol || '').toLowerCase() === filterCrypto.value.toLowerCase()))
    const matchesStatus = !filterStatus.value || (String(alert.status).toLowerCase() === filterStatus.value.toLowerCase())
    return matchesUser && matchesCrypto && matchesStatus
  })
})

const formatCurrency = (value: number | string | undefined): string => {
  const n = Number(value ?? 0)
  const safe = Number.isFinite(n) ? n : 0
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 2
  }).format(safe)
}

const formatDate = (dateString: string | undefined): string => {
  if (!dateString) return ''
  try {
    const d = new Date(dateString)
    return new Intl.DateTimeFormat('fr-FR', {
      year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'
    }).format(d)
  } catch {
    return String(dateString)
  }
}

const isActive = (alert: any): boolean => {
  const s = String(alert?.status || '').toLowerCase()
  return s === 'active' || s === '1' || s === 'true'
}

// react to filter changes
watch([filterUser, filterCrypto, filterStatus], () => {
  currentPage.value = 1
  loadAlerts()
})

const editAlert = (alert: any) => {
  editingAlert.value = alert
  editForm.threshold = alert.target_value ?? alert.threshold ?? 0
  editForm.type = alert.alert_type ?? alert.type ?? '↑ Au-dessus'
  showEditModal.value = true
}

const updateAlert = async () => {
  try {
    await api.put(`/admin/alerts/${editingAlert.value.id}`, { threshold: editForm.threshold, type: editForm.type })
    const idx = alerts.value.findIndex(a => a.id === editingAlert.value.id)
    if (idx >= 0) {
      alerts.value[idx] = {
        ...alerts.value[idx],
        target_value: editForm.threshold,
        alert_type: editForm.type
      }
    }
    showEditModal.value = false
  } catch (e) {
    console.error('Error updating alert:', e)
  }
}

const pauseAlert = async (alertId: number) => {
  try {
    await api.put(`/admin/alerts/${alertId}`, { status: 'paused' })
    const alert = alerts.value.find(a => a.id === alertId)
    if (alert) alert.status = 'Pausée'
  } catch (e) {
    console.error('Error pausing alert:', e)
  }
}

const resumeAlert = async (alertId: number) => {
  try {
    // use adminApi helper if available
    try { await resumeAdminAlert(alertId) } catch (_) { await api.put(`/admin/alerts/${alertId}`, { status: 'active' }) }
    const alert = alerts.value.find(a => a.id === alertId)
    if (alert) alert.status = 'Active'
  } catch (e) {
    console.error('Error resuming alert:', e)
  }
}

const deleteAlert = async (alertId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) return
  try {
    await deleteAdminAlert(alertId).catch(() => api.delete(`/admin/alerts/${alertId}`))
    alerts.value = alerts.value.filter(a => a.id !== alertId)
  } catch (e) {
    console.error('Error deleting alert:', e)
  }
}

const goToPage = (page: number) => {
  if (page >= 1 && pagination.value && page <= (pagination.value.last_page || pagination.value.lastPage || 1)) {
    currentPage.value = page
    loadAlerts()
  }
}

const loadAlerts = async () => {
  isLoading.value = true
  try {
    const res = await getAdminAlerts(currentPage.value, perPage.value, filterStatus.value || undefined, filterUser.value || undefined, filterCrypto.value || undefined)
    alerts.value = res.data || []
    pagination.value = res.pagination || null

    // compute stats
    stats.value.activeAlerts = alerts.value.filter(a => String(a.status).toLowerCase() === 'active' || String(a.status).toLowerCase() === '1').length
    stats.value.paused = alerts.value.filter(a => String(a.status).toLowerCase().includes('pause')).length
    // triggeredToday: count by created_at on today
    const todayKey = new Date().toISOString().split('T')[0]
    stats.value.triggeredToday = alerts.value.filter(a => (a.created_at || '').split('T')[0] === todayKey).length
  } catch (e) {
    console.error('Error loading alerts:', e)
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  await loadAlerts()
})
</script>

<style scoped>
</style>
