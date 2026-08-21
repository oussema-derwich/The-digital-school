<template>
  <AdminLayout pageTitle="Dashboard" pageDescription="Vue d'ensemble des statistiques de la plateforme">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="spinner mb-4"></div>
        <p class="text-gray-500">Chargement des statistiques...</p>
      </div>
    </div>

    <template v-else>
      <!-- Stats Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Users Card -->
        <div class="²                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   ²-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm mb-1">👤 Utilisateurs actifs</p>
              <p class="text-3xl font-bold text-blue-600">{{ stats.active_users }}</p>
              <p class="text-xs text-green-600 mt-2">↑ +{{ stats.new_users_this_week }} cette semaine</p>
            </div>    
            <div class="text-4xl opacity-20">👥</div>
          </div>
        </div>

        <!-- Transaction Volume -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-emerald-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm mb-1">💰 Volume des transactions</p>
              <p class="text-3xl font-bold text-emerald-600">{{ formatCurrency(stats.total_transaction_volume) }}</p>
              <p class="text-xs text-green-600 mt-2">{{ stats.total_transactions }} transactions</p>
            </div>
            <div class="text-4xl opacity-20">💳</div>
          </div>
        </div>

        <!-- Active Alerts -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm mb-1">⚠️ Alertes actives</p>
              <p class="text-3xl font-bold text-orange-600">{{ stats.active_alerts }}</p>
              <p class="text-xs text-orange-600 mt-2">{{ stats.alerts_triggered_today }} déclenchées aujourd'hui</p>
            </div>
            <div class="text-4xl opacity-20">🚨</div>
          </div>
        </div>

        <!-- Market Value -->
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm mb-1">💹 Valeur du marché global</p>
              <p class="text-3xl font-bold text-purple-600">{{ formatCurrency(stats.total_market_value) }}</p>
              <p class="text-xs text-green-600 mt-2">{{ stats.total_users }} utilisateurs total</p>
            </div>
            <div class="text-4xl opacity-20">📈</div>
          </div>
        </div>
      </div>

      <!-- Charts and Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Transaction Volume Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-lg font-bold text-gray-800">Évolution du volume des transactions</h3>
              <p class="text-sm text-gray-500">{{ chartPeriod }} derniers jours</p>
            </div>
            <div class="flex gap-2">
              <button
                @click="chartPeriod = 7; loadChartData()"
                :class="[
                  'px-3 py-1 rounded-lg text-sm font-medium transition',
                  chartPeriod === 7 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                7j
              </button>
              <button
                @click="chartPeriod = 30; loadChartData()"
                :class="[
                  'px-3 py-1 rounded-lg text-sm font-medium transition',
                  chartPeriod === 30 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                30j
              </button>
            </div>
          </div>
          <div v-if="chartData.length > 0" class="h-64 bg-white p-3 rounded shadow-sm">
            <canvas ref="chartCanvas" class="w-full h-full" aria-label="Transaction volume chart" role="img"></canvas>
          </div>
          <div v-else class="h-64 flex items-center justify-center text-gray-400">
            Données de graphique indisponibles
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-4">
          <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Actions rapides</h3>
            <button 
              @click="refreshStats"
              class="w-full py-3 text-white rounded-lg font-medium transition mb-3 flex items-center justify-center gap-2 hover:bg-blue-700" 
              style="background-color: #3B82F6"
              :disabled="isRefreshing"
            >
              <span v-if="!isRefreshing">🔄</span>
              <span v-else class="animate-spin">⏳</span>
              <span>{{ isRefreshing ? 'Actualisation...' : 'Actualiser statistiques' }}</span>
            </button>
            <button class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition mb-3 flex items-center justify-center gap-2">
              <span>📄</span>
              <span>Exporter rapport</span>
            </button>
            <button class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition flex items-center justify-center gap-2">
              <span>⚙️</span>
              <span>Paramètres</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Recent Activities Table -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Dernières activités</h3>
        <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-500">
          Aucune activité récente
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b-2 border-gray-200">
                <th class="text-left py-3 px-4 text-gray-700 font-semibold">Date</th>
                <th class="text-left py-3 px-4 text-gray-700 font-semibold">Utilisateur</th>
                <th class="text-left py-3 px-4 text-gray-700 font-semibold">Action</th>
                <th class="text-left py-3 px-4 text-gray-700 font-semibold">Détail</th>
                <th class="text-left py-3 px-4 text-gray-700 font-semibold">Type</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="activity in recentActivities" :key="activity.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="py-3 px-4 text-gray-600 text-sm">{{ formatDate(activity.created_at) }}</td>
                <td class="py-3 px-4">
                  <span class="font-medium text-gray-800">{{ activity.user_name }}</span>
                </td>
                <td class="py-3 px-4">
                  <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    {{ activity.action }}
                  </span>
                </td>
                <td class="py-3 px-4 text-gray-600 text-sm">{{ activity.description }}</td>
                <td class="py-3 px-4">
                  <span
                    :class="[
                      'inline-block px-3 py-1 rounded-full text-xs font-medium',
                      activity.type === 'transaction'
                        ? 'bg-green-100 text-green-800'
                        : activity.type === 'user'
                        ? 'bg-blue-100 text-blue-800'
                        : activity.type === 'crypto'
                        ? 'bg-purple-100 text-purple-800'
                        : 'bg-yellow-100 text-yellow-800'
                    ]"
                  >
                    {{ activity.type }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted, watch, nextTick } from 'vue'
import Chart from 'chart.js/auto'
import type { ChartOptions } from 'chart.js'
import AdminLayout from './AdminLayout.vue'
import { getAdminStats, getRecentActivities, getTransactionChart, type AdminStats, type Activity } from '@/services/adminApi'

const isLoading = ref(true)
const isRefreshing = ref(false)
const chartPeriod = ref(7)

const stats = ref<AdminStats>({
  total_users: 0,
  active_users: 0,
  new_users_today: 0,
  new_users_this_week: 0,
  total_transactions: 0,
  total_transaction_volume: 0,
  active_alerts: 0,
  alerts_triggered_today: 0,
  total_market_value: 0,
  platform_fees_collected: 0
})

const recentActivities = ref<Activity[]>([])
const chartData = ref<any[]>([])

const chartMax = computed(() => {
  if (!chartData.value || chartData.value.length === 0) return 1
  const max = Math.max(...chartData.value.map((d: any) => Number(d.value) || 0))
  return max > 0 ? max : 1
})

const chartCanvas = ref<HTMLCanvasElement | null>(null)
let chartInstance: any = null

const createOrUpdateChart = () => {
  try {
    if (!chartCanvas.value) return

    const labels = chartData.value.map(d => d.date)
    const values = chartData.value.map(d => Number(d.value) || 0)

    // Debug: log labels and values to help diagnose empty charts
    console.debug('Chart labels:', labels)
    console.debug('Chart values:', values)

    const data = {
      labels,
      datasets: [
        {
          label: 'Volume des transactions',
          data: values,
          fill: true,
          backgroundColor: 'rgba(37,99,235,0.12)',
          borderColor: 'rgba(37,99,235,1)',
          tension: 0.35,
          pointRadius: 3,
          pointBackgroundColor: 'rgba(37,99,235,1)'
        }
      ]
    }

    const options: ChartOptions<'line'> = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index' as const, intersect: false }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#6b7280' }
        },
        y: {
          grid: { color: 'rgba(15,23,42,0.06)' },
          ticks: { color: '#6b7280' },
          suggestedMin: 0
        }
      }
    }

    if (chartInstance) {
      chartInstance.data = data
      chartInstance.options = options
      chartInstance.update()
    } else {
      const ctx = chartCanvas.value.getContext('2d')
      if (!ctx) return
      chartInstance = new Chart(ctx, { type: 'line', data, options })
    }
  } catch (e) {
    console.error('Chart error:', e)
  }
}

watch(chartData, async () => {
  await nextTick()
  createOrUpdateChart()
})
onUnmounted(() => { if (chartInstance) chartInstance.destroy() })

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND'
  }).format(value)
}

const formatDate = (date: string): string => {
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const loadStats = async () => {
  try {
    // Load stats from API
    const fetched = await getAdminStats()
    stats.value = fetched

    // If backend doesn't provide transaction volume or total transactions, fetch transactions and compute as fallback
    if (!stats.value.total_transaction_volume || stats.value.total_transaction_volume === 0) {
      try {
        const txRes = await import('@/services/adminApi').then(m => m.getAdminTransactions(1, 1000))
        const txs = txRes.data || txRes || []
        // txRes here may be PaginatedResponse shape
        const items = Array.isArray(txs) ? txs : (txRes.data || [])
        const totalVolume = items.reduce((sum: number, tx: any) => {
          const v = Number(tx.total_price ?? tx.total ?? tx.total_price ?? 0) || 0
          return sum + v
        }, 0)
        stats.value.total_transaction_volume = totalVolume
        // total transactions fallback
        stats.value.total_transactions = (txRes.pagination && txRes.pagination.total) || items.length || stats.value.total_transactions
      } catch (e) {
        // ignore fallback errors
        console.warn('Could not compute transaction volume fallback', e)
      }
    }

    // If total_users missing, try to infer from recent activities
    if (!stats.value.total_users || stats.value.total_users === 0) {
      if (recentActivities.value && recentActivities.value.length > 0) {
        const uniqueUsers = new Set(recentActivities.value.map(a => a.user_id).filter(Boolean))
        stats.value.total_users = uniqueUsers.size
      }
    }
  } catch (error) {
    console.error('Error loading stats:', error)
  }
}

const loadActivities = async () => {
  try {
    recentActivities.value = await getRecentActivities(10)
  } catch (error) {
    console.error('Error loading activities:', error)
  }
}

const loadChartData = async () => {
  try {
    // Get raw chart data (array of {date, value}) and normalize into continuous last N days
    const raw = await getTransactionChart(chartPeriod.value)
    console.debug('Raw chart response from API:', raw)
    const map: Record<string, number> = {}
    raw.forEach((r: any) => {
      // Normalize date key to YYYY-MM-DD
      const d = (r.date || '').split('T')[0] || r.date
      map[d] = Number(r.value ?? r.total ?? r.count ?? 0) || 0
    })

    // Build continuous array for last `chartPeriod` days (most recent last)
    const days = Number(chartPeriod.value) || 7
    const arr: any[] = []
    for (let i = days - 1; i >= 0; i--) {
      const dt = new Date()
      dt.setDate(dt.getDate() - i)
      const yyyy = dt.getFullYear()
      const mm = String(dt.getMonth() + 1).padStart(2, '0')
      const dd = String(dt.getDate()).padStart(2, '0')
      const key = `${yyyy}-${mm}-${dd}`
      arr.push({ date: `${dd}/${mm}`, value: map[key] ?? 0 })
    }

    console.debug('Normalized date->value map for chart:', map)
    console.debug('Final chart data array:', arr)

    chartData.value = arr
  } catch (error) {
    console.error('Error loading chart data:', error)
  }
}

const refreshStats = async () => {
  isRefreshing.value = true
  try {
    await Promise.all([loadStats(), loadActivities(), loadChartData()])
  } catch (error) {
    console.error('Error refreshing:', error)
  } finally {
    isRefreshing.value = false
  }
}

onMounted(async () => {
  try {
    await Promise.all([loadStats(), loadActivities(), loadChartData()])
  } catch (error) {
    console.error('Error on mount:', error)
  } finally {
    isLoading.value = false
  }
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
 