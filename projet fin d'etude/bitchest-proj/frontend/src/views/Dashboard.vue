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
        <!-- Loading State -->
        <div v-if="isLoading" class="flex justify-center items-center min-h-screen">
          <div class="text-center">
            <p class="text-gray-500">Chargement des données...</p>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
          <p class="text-red-600">{{ error }}</p>
          <button
            @click="retryLoad"
            class="mt-2 px-4 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition"
          >
            Réessayer
          </button>
        </div>

        <!-- Content -->
        <template v-else>
          <!-- Stat Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Solde Disponible (EUR) -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-card p-6 border-l-4 border-blue-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-xs font-semibold mb-2">💳 SOLDE EUR</p>
                  <p class="text-3xl font-bold text-blue-600">{{ formatCurrency(portfolio.balance_eur) }}</p>
                  <p class="text-xs text-gray-500 mt-2">Fonds disponibles</p>
                </div>
              </div>
            </div>

            <!-- Valeur Crypto -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-card p-6 border-l-4 border-purple-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-xs font-semibold mb-2">💎 VALEUR CRYPTO</p>
                  <p class="text-3xl font-bold text-purple-600">{{ formatCurrency(portfolio.total_crypto_value) }}</p>
                  <p class="text-xs text-gray-500 mt-2">Valeur actuelle</p>
                </div>
              </div>
            </div>

            <!-- Portfolio Total Value -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-card p-6 border-l-4 border-green-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-xs font-semibold mb-2">📊 PORTEFEUILLE TOTAL</p>
                  <p class="text-3xl font-bold text-green-600">{{ formatCurrency(portfolio.total_portfolio_value) }}</p>
                  <p class="text-xs text-gray-500 mt-2">EUR + Crypto</p>
                </div>
              </div>
            </div>

            <!-- Cryptos Held -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow-card p-6 border-l-4 border-orange-500">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-gray-600 text-xs font-semibold mb-2">📈 ACTIFS DÉTENUS</p>
                  <p class="text-3xl font-bold text-orange-600">{{ holdings.length }}</p>
                  <p class="text-xs text-gray-500 mt-2">Cryptomonnaies</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Market Chart Section -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Chart -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-card p-6">
              <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div>
                  <h3 class="text-lg font-bold text-secondary">Évolution du Portefeuille</h3>
                  <p class="text-xs text-gray-500 mt-1">Analyse de votre portefeuille sur la période sélectionnée</p>
                </div>
                <div class="flex gap-2">
                  <button
                    v-for="period in [7, 30, 90]"
                    :key="period"
                    @click="selectedPeriod = period"
                    :class="[
                      'px-4 py-2 rounded-lg text-sm font-semibold transition',
                      selectedPeriod === period
                        ? 'bg-primary text-white shadow-md'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]"
                  >
                    {{ period }}j
                  </button>
                </div>
              </div>
              <MarketChart :period="selectedPeriod" :data="chartData" :labels="chartLabels" :height="320" />
              
              <!-- Chart Analysis -->
              <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t">
                <div class="text-center">
                  <p class="text-xs text-gray-500 mb-2">VALEUR MAX</p>
                  <p class="text-lg font-bold text-success">{{ formatCurrency(chartAnalysis.maxValue) }}</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500 mb-2">VALEUR MIN</p>
                  <p class="text-lg font-bold text-danger">{{ formatCurrency(chartAnalysis.minValue) }}</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500 mb-2">MOYENNE</p>
                  <p class="text-lg font-bold text-primary">{{ formatCurrency(chartAnalysis.avgValue) }}</p>
                </div>
              </div>
            </div>

            <!-- Portfolio Analysis Stats -->
            <div class="bg-white rounded-lg shadow-card p-6">
              <h3 class="text-lg font-bold text-secondary mb-6 border-b pb-4">📊 Analyse</h3>
              <div class="space-y-5">
                <!-- Gain Total -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                  <p class="text-xs text-gray-600 font-semibold mb-2">💹 GAIN TOTAL</p>
                  <p :class="['text-2xl font-bold', portfolio.total_gain >= 0 ? 'text-success' : 'text-danger']">
                    {{ portfolio.total_gain >= 0 ? '+' : '' }}{{ formatCurrency(portfolio.total_gain) }}
                  </p>
                  <div class="text-xs text-gray-500 mt-2">
                    {{ portfolio.total_gain_percentage >= 0 ? '+' : '' }}{{ portfolio.total_gain_percentage.toFixed(2) }}%
                  </div>
                </div>

                <!-- Holdings Analysis -->
                <div class="bg-blue-50 rounded-lg p-4">
                  <p class="text-xs text-gray-600 font-semibold mb-2">🔍 RÉPARTITION</p>
                  <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                      <span class="text-gray-600">Crypto / EUR</span>
                      <span class="font-bold text-gray-800">{{ portfolioRatio }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-gradient-to-r from-purple-500 to-blue-500 h-2 rounded-full transition-all"
                        :style="{ width: portfolioRatio + '%' }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Top Holding -->
                <div class="bg-orange-50 rounded-lg p-4">
                  <p class="text-xs text-gray-600 font-semibold mb-2">⭐ TOP ACTIF</p>
                  <p class="text-lg font-bold text-gray-800">{{ topHolding.symbol || 'N/A' }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ formatCurrency(topHolding.value) }}</p>
                </div>

                <!-- Actifs Performance -->
                <div class="bg-indigo-50 rounded-lg p-4">
                  <p class="text-xs text-gray-600 font-semibold mb-3">📈 PERFORMANCE</p>
                  <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                      <span>Gagnants</span>
                      <span :class="['font-bold', performanceStats.winners > 0 ? 'text-success' : 'text-gray-400']">
                        {{ performanceStats.winners }}/{{ holdings.length }}
                      </span>
                    </div>
                    <div class="flex justify-between text-xs">
                      <span>Perdants</span>
                      <span :class="['font-bold', performanceStats.losers > 0 ? 'text-danger' : 'text-gray-400']">
                        {{ performanceStats.losers }}/{{ holdings.length }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Holdings Table -->
          <div class="bg-white rounded-lg shadow-card p-6 mb-8">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
              <h3 class="text-lg font-bold text-secondary">Vos Positions Actuelles</h3>
              <button
                @click="$router.push('/market')"
                class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition shadow-md"
              >
                + Acheter des Cryptos
              </button>
            </div>

            <div v-if="holdings.length === 0" class="text-center py-12">
              <p class="text-gray-500 text-lg mb-4">Aucune position actuellement</p>
              <router-link to="/market" class="text-primary font-bold hover:underline">Commencez à acheter →</router-link>
            </div>

            <!-- Holdings Table -->
            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="text-left py-4 px-4 font-bold text-gray-700">Cryptomonnaie</th>
                    <th class="text-right py-4 px-4 font-bold text-gray-700">Quantité</th>
                    <th class="text-right py-4 px-4 font-bold text-gray-700">Prix d'Achat</th>
                    <th class="text-right py-4 px-4 font-bold text-gray-700">Prix Actuel</th>
                    <th class="text-right py-4 px-4 font-bold text-gray-700">Valeur Actuelle</th>
                    <th class="text-right py-4 px-4 font-bold text-gray-700">Gain/Perte</th>
                    <th class="text-center py-4 px-4 font-bold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="holding in holdings" :key="holding.id" class="border-b border-gray-100 hover:bg-blue-50 transition">
                    <!-- Crypto Name -->
                    <td class="py-4 px-4 font-semibold text-gray-800">
                      <div class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-xs font-bold">
                          {{ holding.symbol.substring(0, 1) }}
                        </span>
                        <div>
                          <p class="font-bold text-gray-800">{{ holding.symbol }}</p>
                          <p class="text-xs text-gray-500">{{ holding.name }}</p>
                        </div>
                      </div>
                    </td>
                    
                    <!-- Quantité -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatQuantity(holding.quantity) }}</span>
                    </td>
                    
                    <!-- Prix d'Achat Moyen -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatCurrency(holding.avg_buy_price) }}</span>
                    </td>
                    
                    <!-- Prix Actuel -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatCurrency(holding.current_price) }}</span>
                      <div :class="['text-xs font-semibold mt-1', (holding.current_price - holding.avg_buy_price) >= 0 ? 'text-success' : 'text-danger']">
                        {{ (holding.current_price - holding.avg_buy_price) >= 0 ? '▲' : '▼' }} 
                        {{ formatCurrency(Math.abs(holding.current_price - holding.avg_buy_price)) }}
                      </div>
                    </td>
                    
                    <!-- Valeur Actuelle -->
                    <td class="text-right py-4 px-4 font-bold text-gray-800 bg-blue-50">
                      <span class="text-lg">{{ formatCurrency(holding.current_value) }}</span>
                    </td>
                    
                    <!-- Gain/Perte -->
                    <td :class="['text-right py-4 px-4 font-bold', holding.profit_loss_percentage >= 0 ? 'text-success' : 'text-danger']">
                      <div :class="['text-lg font-bold', holding.profit_loss_percentage >= 0 ? 'text-success' : 'text-danger']">
                        {{ holding.profit_loss_percentage >= 0 ? '▲ +' : '▼ ' }}{{ holding.profit_loss_percentage.toFixed(2) }}%
                      </div>
                      <div class="text-sm">
                        {{ holding.profit_loss >= 0 ? '+' : '' }}{{ formatCurrency(holding.profit_loss) }}
                      </div>
                    </td>
                    
                    <!-- Actions -->
                    <td class="text-center py-4 px-4">
                      <button
                        @click="viewCryptoDetail(holding.cryptocurrency_id)"
                        class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary-dark transition"
                      >
                        Gérer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </template>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import ClientSidebar from '../components/ClientSidebar.vue'
import MarketChart from '../components/MarketChart.vue'
import api from '../services/api'
import { useRouter } from 'vue-router'

export default defineComponent({
  components: { ClientSidebar, MarketChart },
  setup() {
    const router = useRouter()
    const isLoading = ref(true)
    const error = ref<string | null>(null)
    const userName = ref('Utilisateur')
    const selectedPeriod = ref(7)
    const chartData = ref<any[]>([])
    const chartLabels = ref<string[]>([])
    const holdings = ref<any[]>([])
    
    const portfolio = ref({
      balance_eur: 0,
      total_crypto_value: 0,
      total_portfolio_value: 0,
      total_gain: 0,
      total_gain_percentage: 0
    })

    // Utility function to format currency
    const formatCurrency = (value: number | string): string => {
      if (!value) return '0 DT'
      const num = typeof value === 'string' ? parseFloat(value) : value
      return new Intl.NumberFormat('fr-TN', {
        style: 'currency',
        currency: 'TND',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(num)
    }

    // Format quantity with max 8 decimals
    const formatQuantity = (value: number | string): string => {
      if (!value) return '0'
      const num = typeof value === 'string' ? parseFloat(value) : value
      const formatted = num.toFixed(8)
      return formatted.replace(/\.?0+$/, '')
    }

    // Load user profile
    const loadProfile = async () => {
      try {
        const res = await api.get('/auth/profile')
        if (res.data?.data) {
          userName.value = res.data.data.name || 'Utilisateur'
        }
      } catch (err) {
        console.error('Error loading profile:', err)
      }
    }

    // Load wallet data
    const loadPortfolio = async () => {
      try {
        const res = await api.get('/wallet')
        if (res.data?.data) {
          const data = res.data.data
          
          // Mapping exact du backend
          portfolio.value = {
            balance_eur: parseFloat(data.balance_eur) || 0,
            total_crypto_value: parseFloat(data.total_crypto_value) || 0,
            total_portfolio_value: parseFloat(data.total_portfolio_value) || 0,
            total_gain: (parseFloat(data.total_crypto_value) || 0) - (parseFloat(data.balance_eur) || 0),
            total_gain_percentage: calculateGainPercentage(
              parseFloat(data.total_crypto_value) || 0,
              parseFloat(data.balance_eur) || 0
            )
          }

          // Extract holdings
          if (data.holdings && Array.isArray(data.holdings)) {
            holdings.value = data.holdings.map((holding: any) => ({
              id: holding.id,
              cryptocurrency_id: holding.cryptocurrency_id,
              symbol: holding.symbol,
              name: holding.name,
              quantity: parseFloat(holding.quantity),
              avg_buy_price: parseFloat(holding.avg_buy_price),
              current_price: parseFloat(holding.current_price),
              current_value: parseFloat(holding.current_value),
              profit_loss: parseFloat(holding.profit_loss),
              profit_loss_percentage: parseFloat(holding.profit_loss_percentage)
            }))
          }
        }
      } catch (err) {
        console.error('Error loading portfolio:', err)
      }
    }

    // Calculate gain percentage
    const calculateGainPercentage = (total_crypto: number, balance_eur: number): number => {
      if (balance_eur === 0) return 0
      return ((total_crypto - balance_eur) / balance_eur) * 100
    }

    // Load portfolio history for chart
    const loadChartData = async () => {
      try {
        const res = await api.get(`/portfolio/history?period=${selectedPeriod.value}J`)
        if (res.data?.data?.history && Array.isArray(res.data.data.history)) {
          const history = res.data.data.history
          
          // Extract prices and dates
          chartData.value = history.map((item: any) => ({
            value: parseFloat(item.value) || 0,
            date: item.date
          }))
          
          chartLabels.value = history.map((item: any) => {
            const date = new Date(item.date)
            return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' })
          })
        }
      } catch (err) {
        console.error('Error loading chart data:', err)
      }
    }

    // Chart analysis computed property
    const chartAnalysis = computed(() => {
      if (chartData.value.length === 0) {
        return {
          maxValue: portfolio.value.total_portfolio_value,
          minValue: portfolio.value.total_portfolio_value,
          avgValue: portfolio.value.total_portfolio_value
        }
      }

      const values = chartData.value.map(d => d.value || 0)
      const maxValue = Math.max(...values)
      const minValue = Math.min(...values)
      const avgValue = values.reduce((a, b) => a + b, 0) / values.length

      return { maxValue, minValue, avgValue }
    })

    // Portfolio ratio (Crypto vs EUR)
    const portfolioRatio = computed(() => {
      if (portfolio.value.total_portfolio_value === 0) return 0
      const ratio = (portfolio.value.total_crypto_value / portfolio.value.total_portfolio_value) * 100
      return Math.round(ratio)
    })

    // Top holding
    const topHolding = computed(() => {
      if (holdings.value.length === 0) {
        return { symbol: 'N/A', value: 0 }
      }
      const top = holdings.value.reduce((a, b) => a.current_value > b.current_value ? a : b)
      return top
    })

    // Performance stats
    const performanceStats = computed(() => {
      let winners = 0
      let losers = 0
      
      holdings.value.forEach(holding => {
        if (holding.profit_loss_percentage >= 0) {
          winners++
        } else {
          losers++
        }
      })

      return { winners, losers }
    })

    // Main load function
    const loadData = async () => {
      try {
        isLoading.value = true
        error.value = null
        await Promise.all([
          loadProfile(),
          loadPortfolio()
        ])
        await loadChartData()
      } catch (err: any) {
        error.value = err?.message || 'Erreur lors du chargement des données'
        console.error('Load data error:', err)
      } finally {
        isLoading.value = false
      }
    }

    // Retry load
    const retryLoad = () => {
      loadData()
    }

    // Logout function - uses new optimized auth store
    const logout = async () => {
      try {
        const { performLogout } = await import('../composables/useAuth')
        const auth = performLogout
        // Get from store
        const { useAuthStore } = await import('../services/useAuthStore')
        const store = useAuthStore()
        await store.performLogout()
      } catch (err) {
        console.error('Logout error:', err)
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      } finally {
        router.push('/login').catch(() => {})
      }
    }

    // Navigate to crypto detail
    const viewCryptoDetail = (cryptoId: number) => {
      router.push(`/crypto-detail/${cryptoId}`)
    }

    onMounted(loadData)

    return {
      isLoading,
      error,
      userName,
      selectedPeriod,
      chartData,
      chartLabels,
      holdings,
      portfolio,
      chartAnalysis,
      portfolioRatio,
      topHolding,
      performanceStats,
      formatCurrency,
      formatQuantity,
      logout,
      retryLoad,
      viewCryptoDetail,
      loadData
    }
  }
})
</script>

