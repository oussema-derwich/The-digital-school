<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
      <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Suivi des transactions
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Vue d'ensemble des activités financières de la plateforme
          </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
          <button
            @click="refreshData"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Actualiser
          </button>
          <button
            @click="exportTransactions"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exporter PDF/Excel
          </button>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-medium text-gray-500">Volume 24h</h3>
            <span class="text-2xl">💹</span>
          </div>
          <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.volume24h) }}</p>
          <p class="mt-2 flex items-center text-sm" :class="stats.volumeChange >= 0 ? 'text-green-600' : 'text-red-600'">
            <span class="mr-1">{{ stats.volumeChange >= 0 ? '↑' : '↓' }}</span>
            {{ Math.abs(stats.volumeChange) }}% vs hier
          </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-medium text-gray-500">Commissions</h3>
            <span class="text-2xl">💰</span>
          </div>
          <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(stats.revenue) }}</p>
          <p class="mt-2 flex items-center text-sm" :class="stats.revenueChange >= 0 ? 'text-green-600' : 'text-red-600'">
            <span class="mr-1">{{ stats.revenueChange >= 0 ? '↑' : '↓' }}</span>
            {{ Math.abs(stats.revenueChange) }}% ce mois
          </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-medium text-gray-500">Nombre de transactions</h3>
            <span class="text-2xl">🔄</span>
          </div>
          <p class="text-2xl font-semibold text-gray-900">{{ stats.totalTransactions }}</p>
          <p class="mt-2 flex items-center text-sm" :class="stats.transactionsChange >= 0 ? 'text-green-600' : 'text-red-600'">
            <span class="mr-1">{{ stats.transactionsChange >= 0 ? '↑' : '↓' }}</span>
            {{ Math.abs(stats.transactionsChange) }}% aujourd'hui
          </p>
        </div>
      </div>

      <!-- Graphiques -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Top Cryptos par Volume</h3>
          <div class="h-64">
            <BarChart :data="chartData.topCryptos" />
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution du Volume</h3>
          <div class="h-64">
            <DonutChart :data="chartData.volumeDistribution" />
          </div>
        </div>
      </div>

      <!-- Filtres -->
      <div class="bg-white shadow rounded-lg mb-6 p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          type="date"
          v-model="filters.startDate"
          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Date début"
        />
        <input
          type="date"
          v-model="filters.endDate"
          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Date fin"
        />
        <select
          v-model="filters.cryptoId"
          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
          <option value="">Toutes les cryptos</option>
          <option v-for="crypto in cryptos" :key="crypto.id" :value="crypto.id">
            {{ crypto.name }}
          </option>
        </select>
        <input
          type="text"
          v-model="filters.userQuery"
          class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Utilisateur (nom ou email)"
        />
        <button
          @click="fetchTransactions"
          class="col-span-1 md:col-span-4 mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
        >
          Filtrer
        </button>
      </div>

      <!-- Tableau des transactions -->
      <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & heure</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cryptomonnaie</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant final</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="tx in transactions" :key="tx.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDateTime(tx.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ tx.user.name }}<br><span class="text-xs text-gray-500">{{ tx.user.email }}</span></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="tx.type === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ tx.type === 'buy' ? 'Achat' : 'Vente' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tx.crypto.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ tx.quantity }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(tx.price) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ formatCurrency(tx.amount) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import BarChart from '@/components/admin/BarChart.vue'
import DonutChart from '@/components/admin/DonutChart.vue'

interface Transaction {
  id: number
  user: {
    name: string
    email: string
  }
  type: 'buy' | 'sell'
  crypto: {
    name: string
    symbol: string
  }
  quantity: number
  price: number
  amount: number
  created_at: string
}

interface Crypto {
  id: number
  name: string
  symbol: string
  current_price: number
  price_change_24h: number
}

interface Stats {
  volume24h: number
  volumeChange: number
  revenue: number
  revenueChange: number
  totalTransactions: number
  transactionsChange: number
}

interface ChartData {
  topCryptos: Array<{label: string, value: number}>
  volumeDistribution: Array<{label: string, value: number}>
}

export default {
  components: {
    BarChart,
    DonutChart
  },

  setup() {
    const transactions = ref<Transaction[]>([])
    const cryptos = ref<Crypto[]>([])
    const stats = ref<Stats>({
      volume24h: 0,
      volumeChange: 0,
      revenue: 0,
      revenueChange: 0,
      totalTransactions: 0,
      transactionsChange: 0
    })
    const chartData = ref<ChartData>({
      topCryptos: [],
      volumeDistribution: []
    })
    const filters = ref({
      startDate: '',
      endDate: '',
      cryptoId: '',
      userQuery: ''
    })

    const fetchTransactions = async () => {
      try {
        const response = await api.get('/admin/transactions', { params: filters.value })
        transactions.value = response.data.data || response.data || []
      } catch (error) {
        console.error('Erreur lors du chargement des transactions:', error)
      }
    }

    const fetchStats = async () => {
      try {
        const response = await api.get('/admin/transactions/stats')
        stats.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error)
      }
    }

    const fetchChartData = async () => {
      try {
        const [topResponse, distResponse] = await Promise.all([
          api.get('/admin/transactions/top-cryptos'),
          api.get('/admin/transactions/volume-distribution')
        ])
        chartData.value = {
          topCryptos: topResponse.data,
          volumeDistribution: distResponse.data
        }
      } catch (error) {
        console.error('Erreur lors du chargement des données des graphiques:', error)
        // Données par défaut si l'API échoue
        chartData.value = {
          topCryptos: [
            { label: 'Bitcoin', value: 1200 },
            { label: 'Ethereum', value: 890 },
            { label: 'Ripple', value: 450 }
          ],
          volumeDistribution: [
            { name: 'Bitcoin', value: 45, color: 'rgba(59, 130, 246, 0.5)' },
            { name: 'Ethereum', value: 30, color: 'rgba(147, 51, 234, 0.5)' },
            { name: 'Autres', value: 25, color: 'rgba(236, 72, 153, 0.5)' }
          ]
        }
      }
    }

    const fetchCryptos = async () => {
      try {
        const response = await api.get('/admin/cryptos')
        cryptos.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des cryptos:', error)
      }
    }

    const exportTransactions = async () => {
      try {
        const response = await api.get('/admin/transactions/export', {
          params: filters.value,
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `transactions-${new Date().toISOString()}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        console.error('Erreur lors de l\'export:', error)
      }
    }

    const refreshData = async () => {
      await Promise.all([
        fetchTransactions(),
        fetchStats(),
        fetchChartData()
      ])
    }

    const formatCurrency = (value: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(value)
    }

    const formatDateTime = (date: string) => {
      return new Date(date).toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      fetchCryptos()
      refreshData()
    })

    return {
      transactions,
      cryptos,
      filters,
      stats,
      chartData,
      fetchTransactions,
      exportTransactions,
      refreshData,
      formatCurrency,
      formatDateTime
    }
  }
}
</script>