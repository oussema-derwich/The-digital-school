<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
      <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold">Gestion des alertes</h2>
        <div>
          <button @click="exportAlerts" class="btn btn-primary">Exporter</button>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input v-model="search" @input="fetchAlerts" placeholder="Recherche par crypto ou utilisateur" class="form-input" />
          <select v-model="statusFilter" @change="fetchAlerts" class="form-input">
            <option value="">Tous</option>
            <option value="active">Actives</option>
            <option value="inactive">Inactives</option>
          </select>
          <select v-model="conditionFilter" @change="fetchAlerts" class="form-input">
            <option value="">Toutes</option>
            <option value="above">Au dessus</option>
            <option value="below">En dessous</option>
          </select>
          <button @click="resetFilters" class="btn btn-secondary">Réinitialiser</button>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crypto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="alert in alerts" :key="alert.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ alert.user.name }}<div class="text-xs text-gray-500">{{ alert.user.email }}</div></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alert.crypto.name }} ({{ alert.crypto.symbol }})</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alert.condition }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alert.threshold }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <label class="inline-flex items-center">
                  <input type="checkbox" v-model="alert.is_active" @change="toggleAlert(alert)" class="mr-2" />
                  <span class="text-sm">{{ alert.is_active ? 'Active' : 'Inactive' }}</span>
                </label>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="editAlert(alert)" class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</button>
                <button @click="confirmDelete(alert)" class="text-red-600 hover:text-red-900">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

export default {
  setup() {
    const alerts = ref([])
    const search = ref('')
    const statusFilter = ref('')
    const conditionFilter = ref('')

    const fetchAlerts = async () => {
      try {
        const response = await api.get('/admin/alerts', { params: { q: search.value, status: statusFilter.value, condition: conditionFilter.value }})
        alerts.value = response.data
      } catch (err) { console.error(err) }
    }

    const toggleAlert = async (alert) => {
      try {
        await api.post(`/admin/alerts/${alert.id}/toggle`)
      } catch (err) { console.error(err) }
    }

    const editAlert = (alert) => {
      // open modal (not implemented here) - placeholder
      alert('Edit alert: ' + alert.id)
    }

    const confirmDelete = async (alert) => {
      if (confirm('Confirmer suppression ?')) {
        try {
          await api.delete(`/admin/alerts/${alert.id}`)
          alerts.value = alerts.value.filter(a => a.id !== alert.id)
        } catch (err) { console.error(err) }
      }
    }

    const exportAlerts = async () => {
      try {
        const response = await api.get('/admin/alerts/export', { responseType: 'blob' })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `alerts-${new Date().toISOString()}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (err) { console.error(err) }
    }

    const resetFilters = () => { search.value = ''; statusFilter.value = ''; conditionFilter.value = ''; fetchAlerts() }

    onMounted(() => { fetchAlerts() })

    return { alerts, search, statusFilter, conditionFilter, fetchAlerts, toggleAlert, editAlert, confirmDelete, exportAlerts }
  }
}
</script>
