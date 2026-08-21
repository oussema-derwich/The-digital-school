<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-semibold mb-6">Gestion des Alertes</h1>

    <!-- Formulaire de création d'alerte -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-lg font-medium mb-4">Créer une nouvelle alerte</h2>
      <form @submit.prevent="createAlert" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cryptomonnaie</label>
          <select v-model="newAlert.crypto_id" class="w-full rounded-md border-gray-300 shadow-sm">
            <option v-for="crypto in cryptos" :key="crypto.id" :value="crypto.id">
              {{ crypto.name }} ({{ crypto.symbol }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type d'alerte</label>
          <select v-model="newAlert.type" class="w-full rounded-md border-gray-300 shadow-sm">
            <option value="above">Prix au-dessus de</option>
            <option value="below">Prix en-dessous de</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Prix seuil (€)</label>
          <input type="number" v-model="newAlert.price_threshold" step="0.01" min="0"
                 class="w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <button type="submit" class="btn-primary w-full">
          Créer l'alerte
        </button>
      </form>
    </div>

    <!-- Liste des alertes existantes -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-lg font-medium mb-4">Mes alertes</h2>
      
      <div v-if="alerts.length === 0" class="text-center text-gray-500 py-4">
        Aucune alerte définie
      </div>

      <div v-else class="space-y-4">
        <div v-for="alert in alerts" :key="alert.id"
             class="border rounded-lg p-4 flex items-center justify-between">
          <div>
            <div class="font-medium">{{ alert.crypto.name }}</div>
            <div class="text-sm text-gray-500">
              {{ alert.type === 'above' ? 'Au-dessus de' : 'En-dessous de' }}
              {{ alert.price_threshold }} €
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <button @click="toggleAlert(alert)" 
                    :class="alert.is_active ? 'text-green-600' : 'text-gray-400'"
                    class="p-2 hover:bg-gray-100 rounded-full">
              <span v-if="alert.is_active">🔔</span>
              <span v-else>🔕</span>
            </button>
            
            <button @click="deleteAlert(alert.id)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-full">
              🗑️
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import api from '@/services/api'

export default defineComponent({
  name: 'AlertsView',
  
  setup() {
    const alerts = ref<any[]>([])
    const cryptos = ref<any[]>([])
    const newAlert = ref({
      crypto_id: '',
      type: 'above',
      price_threshold: 0
    })

    const loadAlerts = async () => {
      try {
        const response = await api.get('/alerts')
        alerts.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des alertes:', error)
      }
    }

    const loadCryptos = async () => {
      try {
        const response = await api.get('/cryptos')
        cryptos.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des cryptomonnaies:', error)
      }
    }

    const createAlert = async () => {
      try {
        await api.post('/alerts', newAlert.value)
        await loadAlerts()
        newAlert.value = {
          crypto_id: '',
          type: 'above',
          price_threshold: 0
        }
      } catch (error) {
        console.error('Erreur lors de la création de l\'alerte:', error)
      }
    }

    const toggleAlert = async (alert: any) => {
      try {
        await api.put(`/alerts/${alert.id}`, {
          is_active: !alert.is_active
        })
        await loadAlerts()
      } catch (error) {
        console.error('Erreur lors de la modification de l\'alerte:', error)
      }
    }

    const deleteAlert = async (alertId: number) => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) return
      
      try {
        await api.delete(`/alerts/${alertId}`)
        await loadAlerts()
      } catch (error) {
        console.error('Erreur lors de la suppression de l\'alerte:', error)
      }
    }

    onMounted(() => {
      loadAlerts()
      loadCryptos()
    })

    return {
      alerts,
      cryptos,
      newAlert,
      createAlert,
      toggleAlert,
      deleteAlert
    }
  }
})
</script>

<style scoped>
/* Common styles moved to src/styles/tailwind.css */
</style>