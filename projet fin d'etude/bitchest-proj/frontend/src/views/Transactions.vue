<template>
  <div class="p-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-semibold">Historique des transactions</h1>
          <div class="flex gap-4">
            <button 
              @click="exportTransactions('csv')"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              Exporter en CSV
            </button>
            <button 
              @click="exportTransactions('pdf')"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Exporter en PDF
            </button>
          </div>
        </div>

        <!-- Filtres -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select 
              v-model="filters.type"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Tous</option>
              <option value="buy">Achat</option>
              <option value="sell">Vente</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Crypto</label>
            <select 
              v-model="filters.crypto"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Toutes</option>
              <option v-for="c in cryptos" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
            <select 
              v-model="filters.period"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="all">Toutes les périodes</option>
              <option value="today">Aujourd'hui</option>
              <option value="week">Cette semaine</option>
              <option value="month">Ce mois</option>
              <option value="year">Cette année</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Crypto
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Quantité
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prix unitaire
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                </th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Preuve
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="t in filteredTransactions" :key="t.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(t.date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    t.type === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]">
                    {{ t.type === 'buy' ? 'Achat' : 'Vente' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ t.crypto }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatNumber(t.quantity) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(t.price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ formatPrice(t.quantity * t.price) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <button 
                    @click="downloadProof(t)"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    📄 Télécharger
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import api from '@/services/api'

export default defineComponent({
  setup() {
    const transactions = ref<any[]>([])
    const cryptos = ref<string[]>([])
    const filters = ref({
      type: '',
      crypto: '',
      period: 'all'
    })

    const load = async () => {
      try {
        const res = await api.get('/transactions')
        // API returns {status, data: [...], statistiques: {...}}
        transactions.value = res.data.data || res.data
        // Extract unique crypto symbols
        cryptos.value = [...new Set(transactions.value.map(t => t.crypto?.symbol || t.crypto))]
      } catch (e) {
        console.error('Error loading transactions:', e)
        // Demo data
        transactions.value = [
          { id: 1, date: '2025-01-01', type: 'buy', crypto: { symbol: 'BTC' }, transaction: { quantite: 0.01, prix_unitaire: 48000, montant_eur: 480 } },
          { id: 2, date: '2025-01-02', type: 'sell', crypto: { symbol: 'ETH' }, transaction: { quantite: 0.1, prix_unitaire: 3200, montant_eur: 320 } }
        ]
        cryptos.value = ['BTC', 'ETH']
      }
    }

    const filteredTransactions = computed(() => {
      return transactions.value.filter(t => {
        const matchesType = !filters.value.type || t.type === filters.value.type
        const matchesCrypto = !filters.value.crypto || t.crypto === filters.value.crypto
        const matchesPeriod = filterByPeriod(t.date, filters.value.period)
        return matchesType && matchesCrypto && matchesPeriod
      })
    })

    const filterByPeriod = (date: string, period: string) => {
      const txDate = new Date(date)
      const now = new Date()
      
      switch (period) {
        case 'today':
          return txDate.toDateString() === now.toDateString()
        case 'week':
          const weekAgo = new Date(now.setDate(now.getDate() - 7))
          return txDate >= weekAgo
        case 'month':
          return txDate.getMonth() === now.getMonth() && 
                 txDate.getFullYear() === now.getFullYear()
        case 'year':
          return txDate.getFullYear() === now.getFullYear()
        default:
          return true
      }
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

    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    }

    const formatNumber = (num: number) => {
      return new Intl.NumberFormat('fr-FR', {
        maximumFractionDigits: 8
      }).format(num)
    }

    const exportTransactions = async (format: 'csv' | 'pdf') => {
      try {
        const response = await api.get(`/transactions/export/${format}`, {
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `transactions.${format}`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        console.error(`Error exporting transactions as ${format}:`, error)
      }
    }

    const downloadProof = async (transaction: any) => {
      try {
        const response = await api.get(`/transactions/${transaction.id}/proof`, {
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `transaction-${transaction.id}-proof.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        console.error('Error downloading transaction proof:', error)
      }
    }

    onMounted(load)

    return {
      transactions,
      cryptos,
      filters,
      filteredTransactions,
      formatDate,
      formatPrice,
      formatNumber,
      exportTransactions,
      downloadProof
    }
  }
})
</script>
