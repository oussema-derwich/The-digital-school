<template>
  <div class="flex">
    <ClientSidebar />
    <div class="flex-1">
      <!-- Header Navbar -->
      <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white font-bold">BC</div>
          <h1 class="text-xl font-bold text-primary">BitChest</h1>
        </div>
        <div class="flex items-center gap-4">
          <button class="relative text-gray-600 hover:text-primary transition">
            🔔
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>
          <span class="text-sm font-medium text-gray-700">{{ userName }}</span>
          <button
            @click="logout"
            class="px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition"
          >
            Déconnexion
          </button>
        </div>
      </header>

      <!-- Main Content -->
      <main class="p-8 bg-background min-h-screen">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl font-bold text-secondary">Alertes</h2>
          <button
            @click="showNewAlertForm = !showNewAlertForm"
            class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition"
          >
            + Nouvelle Alerte
          </button>
        </div>

        <!-- New Alert Form -->
        <div v-if="showNewAlertForm" class="bg-white rounded-lg shadow-card p-6 mb-8">
          <h3 class="text-lg font-bold text-secondary mb-4">Créer une Nouvelle Alerte</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Crypto</label>
              <select
                v-model="newAlert.crypto_id"
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-medium focus:border-primary focus:outline-none transition"
              >
                <option value="">Sélectionner...</option>
                <option value="Bitcoin">Bitcoin</option>
                <option value="Ethereum">Ethereum</option>
                <option value="Cardano">Cardano</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Seuil (DT)</label>
              <input
                v-model="newAlert.price_threshold"
                type="number"
                placeholder="Ex: 85000"
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-medium focus:border-primary focus:outline-none transition"
              />
            </div>
            <div class="flex items-end gap-2">
              <button
                @click="createAlert"
                class="flex-1 px-4 py-2 bg-success text-white rounded-lg font-medium hover:opacity-90 transition"
              >
                Créer
              </button>
              <button
                @click="showNewAlertForm = false"
                class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg font-medium hover:opacity-90 transition"
              >
                Annuler
              </button>
            </div>
          </div>
        </div>

        <!-- Edit Alert Form -->
        <div v-if="showEditAlertForm" class="bg-white rounded-lg shadow-card p-6 mb-8">
          <h3 class="text-lg font-bold text-secondary mb-4">Modifier l'Alerte</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Crypto</label>
              <select
                v-model="editingAlert.crypto"
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-medium focus:border-primary focus:outline-none transition"
              >
                <option value="">Sélectionner...</option>
                <option value="Bitcoin">Bitcoin</option>
                <option value="Ethereum">Ethereum</option>
                <option value="Cardano">Cardano</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Seuil (DT)</label>
              <input
                v-model="editingAlert.threshold"
                type="number"
                placeholder="Ex: 85000"
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-medium focus:border-primary focus:outline-none transition"
              />
            </div>
            <div class="flex items-end gap-2">
              <button
                @click="saveEditedAlert"
                class="flex-1 px-4 py-2 bg-success text-white rounded-lg font-medium hover:opacity-90 transition"
              >
                Sauvegarder
              </button>
              <button
                @click="showEditAlertForm = false"
                class="flex-1 px-4 py-2 bg-gray-400 text-white rounded-lg font-medium hover:opacity-90 transition"
              >
                Annuler
              </button>
            </div>
          </div>
        </div>

        <!-- Alerts Table -->
        <div class="bg-white rounded-lg shadow-card p-6 mb-8">
          <h3 class="text-lg font-bold text-secondary mb-6">Vos Alertes</h3>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Crypto</th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700">Seuil</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">État</th>
                  <th class="text-center py-3 px-4 font-bold text-gray-700">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="alert in alerts" :key="alert.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
                  <td class="py-4 px-4 font-semibold text-gray-800">{{ alert.crypto }}</td>
                  <td class="text-right py-4 px-4 text-gray-700">{{ alert.threshold }} DT</td>
                  <td class="py-4 px-4">
                    <span
                      :class="[
                        'px-3 py-1 rounded-lg text-sm font-bold',
                        alert.status === 'Activée'
                          ? 'bg-success/20 text-success'
                          : 'bg-danger/20 text-danger'
                      ]"
                    >
                      {{ alert.status }}
                    </span>
                  </td>
                  <td class="text-center py-4 px-4 flex gap-2 justify-center">
                    <button
                      v-if="alert.status === 'Activée'"
                      @click="disableAlert(alert)"
                      class="px-3 py-1 bg-warning text-white rounded-lg text-sm font-medium hover:opacity-90 transition"
                    >
                      Désactiver
                    </button>
                    <button
                      v-else
                      @click="enableAlert(alert)"
                      class="px-3 py-1 bg-success text-white rounded-lg text-sm font-medium hover:opacity-90 transition"
                    >
                      Activer
                    </button>
                    <button
                      @click="editAlert(alert)"
                      class="px-3 py-1 bg-accent text-white rounded-lg text-sm font-medium hover:opacity-90 transition"
                    >
                      Modifier
                    </button>
                    <button
                      @click="deleteAlert(alert)"
                      class="px-3 py-1 bg-danger text-white rounded-lg text-sm font-medium hover:opacity-90 transition"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import ClientSidebar from '../components/ClientSidebar.vue'
import api from '../services/api'
import { useRouter } from 'vue-router'

interface Alert {
  id: number
  crypto: string
  threshold: string
  status: string
  crypto_id?: number | string
  price_threshold?: number
  is_active?: boolean
  type?: string
}

export default defineComponent({
  components: { ClientSidebar },
  setup() {
    const router = useRouter()
    const userName = ref('Utilisateur')
    const showNewAlertForm = ref(false)
    const showEditAlertForm = ref(false)
    const cryptocurrencies = ref<any[]>([])
    const newAlert = ref({ crypto_id: '', price_threshold: '', type: 'above' })
    const editingAlert = ref<Alert>({ id: 0, crypto: '', threshold: '', status: '' })
    const editingAlertId = ref<number | null>(null)
    const alerts = ref<Alert[]>([])

    const loadData = async () => {
      try {
        // Load profile
        const profileRes = await api.get('/auth/profile')
        const profile = profileRes.data?.data || profileRes.data
        if (profile) {
          userName.value = profile.name || 'Utilisateur'
        }

        // Load cryptocurrencies
        const cryptoRes = await api.get('/cryptocurrencies')
        if (cryptoRes.data) {
          cryptocurrencies.value = Array.isArray(cryptoRes.data) ? cryptoRes.data : cryptoRes.data.data || []
        }

        // Load alerts
        const alertsRes = await api.get('/alerts')
        if (alertsRes.data) {
          const alertsData = Array.isArray(alertsRes.data) ? alertsRes.data : alertsRes.data.data || []
          alerts.value = alertsData.map((alert: any) => {
            const threshold = parseFloat(alert.price_threshold)
            return {
              id: alert.id,
              crypto: alert.crypto?.symbol || 'Unknown',
              threshold: !isNaN(threshold) ? threshold.toFixed(2) : '0.00',
              status: alert.is_active ? 'Activée' : 'Désactivée',
              crypto_id: alert.crypto_id,
              price_threshold: !isNaN(threshold) ? threshold : 0,
              is_active: alert.is_active,
              type: alert.type
            }
          })
        }
      } catch (e) {
        console.error('Error loading data:', e)
      }
    }

    const logout = async () => {
      try {
        await api.post('/auth/logout')
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } catch (e) {
        console.error('Logout error:', e)
        router.push('/login')
      }
    }

    const createAlert = async () => {
      if (newAlert.value.crypto_id && newAlert.value.price_threshold) {
        try {
          const response = await api.post('/alerts', {
            cryptocurrency_id: newAlert.value.crypto_id,
            price_threshold: parseFloat(newAlert.value.price_threshold),
            type: newAlert.value.type
          })
          
          if (response.data && response.data.alert) {
            const alert = response.data.alert
            alerts.value.push({
              id: alert.id,
              crypto: cryptocurrencies.value.find(c => c.id === alert.cryptocurrency_id)?.symbol || '',
              threshold: alert.price_threshold.toFixed(2),
              status: 'Activée',
              crypto_id: alert.cryptocurrency_id,
              price_threshold: alert.price_threshold,
              is_active: alert.is_active,
              type: alert.type
            })
            newAlert.value = { crypto_id: '', price_threshold: '', type: 'above' }
            showNewAlertForm.value = false
          }
        } catch (e) {
          console.error('Error creating alert:', e)
          alert('Erreur lors de la création de l\'alerte')
        }
      }
    }

    const disableAlert = async (alert: Alert) => {
      try {
        await api.put(`/alerts/${alert.id}`, { is_active: false })
        alert.status = 'Désactivée'
      } catch (e) {
        console.error('Error disabling alert:', e)
      }
    }

    const enableAlert = async (alert: Alert) => {
      try {
        await api.put(`/alerts/${alert.id}`, { is_active: true })
        alert.status = 'Activée'
      } catch (e) {
        console.error('Error enabling alert:', e)
      }
    }

    const editAlert = (alert: Alert) => {
      editingAlert.value = { ...alert }
      editingAlertId.value = alert.id
      showEditAlertForm.value = true
    }

    const saveEditedAlert = async () => {
      if (editingAlert.value.crypto && editingAlert.value.threshold && editingAlertId.value) {
        try {
          await api.put(`/alerts/${editingAlertId.value}`, {
            price_threshold: parseFloat(editingAlert.value.threshold)
          })
          
          const index = alerts.value.findIndex((a) => a.id === editingAlertId.value)
          if (index > -1) {
            alerts.value[index] = { ...editingAlert.value }
          }
          showEditAlertForm.value = false
          editingAlert.value = { id: 0, crypto: '', threshold: '', status: '' }
          editingAlertId.value = null
        } catch (e) {
          console.error('Error saving alert:', e)
        }
      }
    }

    const deleteAlert = async (alert: Alert) => {
      try {
        await api.delete(`/alerts/${alert.id}`)
        const index = alerts.value.findIndex((a) => a.id === alert.id)
        if (index > -1) {
          alerts.value.splice(index, 1)
        }
      } catch (e) {
        console.error('Error deleting alert:', e)
      }
    }

    onMounted(loadData)

    return {
      userName,
      showNewAlertForm,
      showEditAlertForm,
      newAlert,
      editingAlert,
      cryptocurrencies,
      alerts,
      logout,
      createAlert,
      disableAlert,
      enableAlert,
      editAlert,
      saveEditedAlert,
      deleteAlert
    }
  }
})
</script>
