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
            <p class="text-gray-500">Chargement du portefeuille...</p>
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
          <h2 class="text-3xl font-bold text-secondary mb-8">Mon Portefeuille</h2>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Donut Chart -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-card p-6">
              <h3 class="text-lg font-bold text-secondary mb-4">Répartition du Portefeuille</h3>
              <PortfolioDonutChart :data="chartData" />
            </div>

            <!-- Portfolio Stats -->
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-lg shadow-card p-6 text-white">
              <h3 class="text-lg font-bold mb-6 border-b border-white/20 pb-4">Résumé du Portefeuille</h3>
              <div class="space-y-5">
                <!-- Valeur Totale -->
                <div class="bg-white/10 rounded-lg p-4 backdrop-blur">
                  <p class="text-white/70 text-xs font-semibold mb-2">VALEUR TOTALE</p>
                  <p class="text-4xl font-bold">{{ formatCurrency(portfolio.total_value) }}</p>
                  <div :class="['text-sm font-semibold mt-2', portfolio.total_gain >= 0 ? 'text-success-light' : 'text-danger-light']">
                    {{ portfolio.total_gain >= 0 ? '▲' : '▼' }} {{ portfolio.total_gain >= 0 ? '+' : '' }}{{ formatCurrency(portfolio.total_gain) }}
                  </div>
                </div>

                <!-- Gain/Perte Percentage -->
                <div class="bg-white/10 rounded-lg p-4 backdrop-blur">
                  <p class="text-white/70 text-xs font-semibold mb-2">GAIN/PERTE (%)</p>
                  <p :class="['text-3xl font-bold', portfolio.total_gain_percentage >= 0 ? 'text-success' : 'text-danger']">
                    {{ portfolio.total_gain_percentage >= 0 ? '+' : '' }}{{ portfolio.total_gain_percentage.toFixed(2) }}%
                  </p>
                </div>

                <!-- Montant Investi -->
                <div class="bg-white/10 rounded-lg p-4 backdrop-blur">
                  <p class="text-white/70 text-xs font-semibold mb-2">MONTANT INVESTI</p>
                  <p class="text-2xl font-bold">{{ formatCurrency(portfolio.total_invested) }}</p>
                </div>

                <!-- Nombre d'Actifs -->
                <div class="bg-white/10 rounded-lg p-4 backdrop-blur">
                  <p class="text-white/70 text-xs font-semibold mb-2">NOMBRE D'ACTIFS</p>
                  <p class="text-2xl font-bold">{{ assets.length }} cryptomonnaie{{ assets.length > 1 ? 's' : '' }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="flex gap-2 mb-6">
            <button
              v-for="filter in ['Toutes', 'Gagnantes', 'Perdantes']"
              :key="filter"
              @click="selectedFilter = filter"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition',
                selectedFilter === filter
                  ? 'bg-primary text-white'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
              ]"
            >
              {{ filter }}
            </button>
          </div>

          <!-- Assets Table -->
          <div class="bg-white rounded-lg shadow-card p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-bold text-secondary">Actifs ({{ assets.length }})</h3>
              <div class="flex gap-2">
                <button
                  @click="refreshData"
                  class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition"
                >
                  🔄 Actualiser
                </button>
                <button
                  @click="$router.push('/market')"
                  class="px-4 py-2 bg-accent text-white rounded-lg font-medium hover:opacity-90 transition"
                >
                  + Acheter Plus
                </button>
              </div>
            </div>

            <div v-if="filteredAssets.length === 0" class="text-center py-8">
              <p class="text-gray-500">Aucun actif trouvé</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 font-bold text-gray-700">Cryptomonnaie</th>
                    <th class="text-right py-3 px-4 font-bold text-gray-700">Quantité</th>
                    <th class="text-right py-3 px-4 font-bold text-gray-700">Prix d'Achat</th>
                    <th class="text-right py-3 px-4 font-bold text-gray-700">Prix Actuel</th>
                    <th class="text-right py-3 px-4 font-bold text-gray-700">Valeur Actuelle</th>
                    <th class="text-right py-3 px-4 font-bold text-gray-700">Gain/Perte</th>
                    <th class="text-center py-3 px-4 font-bold text-gray-700">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="asset in filteredAssets" :key="asset.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <!-- Cryptomonnaie -->
                    <td class="py-4 px-4 font-semibold text-gray-800">
                      <div class="flex items-center gap-2">
                        <span class="text-lg font-bold text-primary">{{ asset.symbol }}</span>
                        <div>
                          <div class="font-semibold text-gray-800">{{ asset.name }}</div>
                          <div class="text-xs text-gray-500">ID: {{ asset.crypto_id }}</div>
                        </div>
                      </div>
                    </td>
                    
                    <!-- Quantité -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatQuantity(asset.quantity) }}</span>
                      <div class="text-xs text-gray-500">{{ asset.symbol }}</div>
                    </td>
                    
                    <!-- Prix d'Achat Moyen -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatCurrency(asset.avg_buy_price || 0) }}</span>
                      <div class="text-xs text-gray-500">Moyen</div>
                    </td>
                    
                    <!-- Prix Actuel -->
                    <td class="text-right py-4 px-4 text-gray-700">
                      <span class="font-semibold">{{ formatCurrency(asset.current_price) }}</span>
                      <div :class="['text-xs font-semibold', (asset.current_price - (asset.avg_buy_price || 0)) >= 0 ? 'text-success' : 'text-danger']">
                        {{ (asset.current_price - (asset.avg_buy_price || 0)) >= 0 ? '+' : '' }}{{ formatCurrency(asset.current_price - (asset.avg_buy_price || 0)) }}
                      </div>
                    </td>
                    
                    <!-- Valeur Actuelle -->
                    <td class="text-right py-4 px-4 font-bold text-gray-800 bg-blue-50">
                      <span class="text-lg">{{ formatCurrency(asset.value) }}</span>
                      <div class="text-xs text-gray-500">Total</div>
                    </td>
                    
                    <!-- Gain/Perte -->
                    <td :class="[
                      'text-right py-4 px-4 font-bold',
                      asset.gain_loss_percentage >= 0 ? 'text-success' : 'text-danger'
                    ]">
                      <div :class="['text-lg font-bold', asset.gain_loss_percentage >= 0 ? 'text-success' : 'text-danger']">
                        {{ asset.gain_loss_percentage >= 0 ? '+' : '' }}{{ asset.gain_loss_percentage.toFixed(2) }}%
                      </div>
                      <div class="text-sm">
                        {{ asset.profit_loss !== undefined && asset.profit_loss >= 0 ? '+' : '' }}{{ formatCurrency(asset.profit_loss || 0) }}
                      </div>
                    </td>
                    
                    <!-- Actions -->
                    <td class="text-center py-4 px-4 flex gap-2 justify-center">
                      <button
                        @click="viewDetails(asset)"
                        class="px-3 py-1 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition"
                      >
                        Détails
                      </button>
                      <button
                        @click="editAsset(asset)"
                        class="px-3 py-1 bg-accent text-white rounded-lg text-sm font-medium hover:opacity-90 transition"
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
import PortfolioDonutChart from '../components/PortfolioDonutChart.vue'
import { getPortfolioSummary } from '../services/portfolioApi'
import api from '../services/api'
import { useRouter } from 'vue-router'

interface Asset {
  id: number
  crypto_id: number
  name: string
  symbol: string
  quantity: number
  current_price: number
  value: number
  gain_loss_percentage: number
  avg_buy_price?: number
  profit_loss?: number
}

export default defineComponent({
  components: { ClientSidebar, PortfolioDonutChart },
  setup() {
    const router = useRouter()
    const isLoading = ref(true)
    const error = ref<string | null>(null)
    const userName = ref('Utilisateur')
    const selectedFilter = ref('Toutes')
    
    const portfolio = ref({
      total_value: 0,
      total_invested: 0,
      total_gain: 0,
      total_gain_percentage: 0
    })
    
    const assets = ref<Asset[]>([])

    // Utility functions
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

    const formatNumber = (value: number | string): string => {
      if (!value) return '0'
      const num = typeof value === 'string' ? parseFloat(value) : value
      return parseFloat(num.toString()).toFixed(8)
    }

    const formatQuantity = (value: number | string): string => {
      if (!value) return '0'
      const num = typeof value === 'string' ? parseFloat(value) : value
      // Afficher max 8 décimales, sans zéros inutiles
      const formatted = num.toFixed(8)
      return formatted.replace(/\.?0+$/, '')
    }

    const chartData = computed(() => {
      return assets.value.map((asset) => ({
        name: asset.name,
        value: asset.value,
        color: ['#FF6B35', '#004E89', '#F7B32B', '#A23B72', '#00D9FF'][assets.value.indexOf(asset) % 5]
      }))
    })

    const filteredAssets = computed(() => {
      return assets.value.filter((asset) => {
        if (selectedFilter.value === 'Gagnantes') {
          return asset.gain_loss_percentage >= 0
        } else if (selectedFilter.value === 'Perdantes') {
          return asset.gain_loss_percentage < 0
        }
        return true
      })
    })

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

    // Load portfolio data
    const loadPortfolio = async () => {
      try {
        const res = await api.get('/wallet')
        if (res.data?.data) {
          // Si c'est la nouvelle structure avec balance_eur et total_portfolio_value
          if (res.data.data.balance_eur !== undefined && res.data.data.total_portfolio_value !== undefined) {
            const balance_eur = parseFloat(res.data.data.balance_eur)
            const total_crypto_value = parseFloat(res.data.data.total_crypto_value)
            const total_portfolio_value = parseFloat(res.data.data.total_portfolio_value)
            
            portfolio.value = {
              total_value: total_portfolio_value,
              total_invested: balance_eur,
              total_gain: total_crypto_value - balance_eur,
              total_gain_percentage: balance_eur > 0 ? ((total_crypto_value - balance_eur) / balance_eur) * 100 : 0
            }
          } else {
            // Ancienne structure - appel API /portfolio
            const portfolioRes = await getPortfolioSummary()
            if (portfolioRes.portfolio) {
              portfolio.value = {
                total_value: portfolioRes.portfolio.total_value || 0,
                total_invested: portfolioRes.portfolio.total_invested || 0,
                total_gain: portfolioRes.portfolio.total_gain || 0,
                total_gain_percentage: portfolioRes.portfolio.total_gain_percentage || 0
              }
            }
          }
        }
      } catch (err) {
        console.error('Error loading portfolio:', err)
      }
    }

    // Load wallet holdings
    const loadAssets = async () => {
      try {
        const res = await api.get('/wallet')
        if (res.data?.data) {
          // Vérifier si c'est la nouvelle structure du backend
          if (res.data.data.holdings && Array.isArray(res.data.data.holdings)) {
            // Nouvelle structure avec holdings
            assets.value = res.data.data.holdings.map((holding: any) => ({
              id: holding.id,
              crypto_id: holding.cryptocurrency_id,
              name: holding.name,
              symbol: holding.symbol,
              quantity: parseFloat(holding.quantity),
              current_price: parseFloat(holding.current_price),
              value: parseFloat(holding.current_value) || (parseFloat(holding.quantity) * parseFloat(holding.current_price)),
              gain_loss_percentage: parseFloat(holding.profit_loss_percentage) || 0,
              avg_buy_price: parseFloat(holding.avg_buy_price),
              profit_loss: parseFloat(holding.profit_loss) || 0
            }))
          } else if (Array.isArray(res.data.data)) {
            // Ancienne structure (compatibilité)
            assets.value = res.data.data.map((holding: any) => ({
              id: holding.id,
              crypto_id: holding.crypto_id,
              name: holding.crypto?.name || 'Unknown',
              symbol: holding.crypto?.symbol || '???',
              quantity: parseFloat(holding.quantity),
              current_price: holding.crypto?.price || 0,
              value: (parseFloat(holding.quantity) * (holding.crypto?.price || 0)),
              gain_loss_percentage: holding.gain_loss_percentage || 0
            }))
          }
        }
      } catch (err) {
        console.error('Error loading assets:', err)
      }
    }

    // Main load function
    const loadData = async () => {
      try {
        isLoading.value = true
        error.value = null
        await Promise.all([
          loadProfile(),
          loadPortfolio(),
          loadAssets()
        ])
      } catch (err: any) {
        error.value = err?.message || 'Erreur lors du chargement du portefeuille'
        console.error('Load data error:', err)
      } finally {
        isLoading.value = false
      }
    }

    // Retry load
    const retryLoad = () => {
      loadData()
    }

    // Logout function
    const logout = async () => {
      try {
        await api.post('/auth/logout')
      } catch (err) {
        console.error('Logout error:', err)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      }
    }

    // Refresh data
    const refreshData = () => {
      loadData()
    }

    // View details
    const viewDetails = (asset: Asset) => {
      router.push(`/crypto-detail/${asset.crypto_id}`)
    }

    // Edit asset (manage)
    const editAsset = (asset: Asset) => {
      router.push(`/crypto-detail/${asset.crypto_id}`)
    }

    onMounted(loadData)

    return {
      isLoading,
      error,
      userName,
      selectedFilter,
      assets,
      portfolio,
      chartData,
      filteredAssets,
      formatCurrency,
      formatNumber,
      formatQuantity,
      logout,
      retryLoad,
      refreshData,
      viewDetails,
      editAsset
    }
  }
})
</script>
