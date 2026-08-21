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
          <!-- Back Button -->
          <button
            @click="$router.back()"
            class="mb-6 px-4 py-2 bg-gray-400 text-white rounded-lg font-medium hover:opacity-90 transition"
          >
            ← Retour
          </button>

          <!-- Crypto Header -->
          <div class="bg-white rounded-lg shadow-card p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
              <div>
                <div class="flex items-center gap-3 mb-4">
                  <CryptoLogo :name="crypto.symbol" size="lg" />
                  <div>
                    <h2 class="text-3xl font-bold text-secondary">{{ crypto.name }}</h2>
                    <p class="text-gray-500">{{ crypto.symbol }}</p>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <p class="text-4xl font-bold text-primary mb-2">{{ formatCurrency(crypto.price) }}</p>
                <p
                  :class="[
                    'text-lg font-bold',
                    priceChange >= 0 ? 'text-success' : 'text-danger'
                  ]"
                >
                  {{ priceChange >= 0 ? '+' : '' }}{{ priceChange.toFixed(2) }}%
                </p>
              </div>
            </div>

            <!-- Timeframe Buttons -->
            <div class="flex gap-2 mb-6">
              <button
                v-for="period in [7, 30, 90]"
                :key="period"
                @click="selectedPeriod = period"
                :class="[
                  'px-4 py-2 rounded-lg font-medium transition',
                  selectedPeriod === period
                    ? 'bg-primary text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                ]"
              >
                {{ period }}j
              </button>
            </div>

            <!-- Chart -->
            <MarketChart :period="selectedPeriod" :data="chartData" :labels="chartLabels" :height="300" />
          </div>

          <!-- Market Info -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-card p-6">
              <p class="text-gray-500 text-sm mb-2">Volume 24h</p>
              <p class="text-2xl font-bold text-primary">{{ formatCurrency(crypto.volume_24h) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-card p-6">
              <p class="text-gray-500 text-sm mb-2">Market Cap</p>
              <p class="text-2xl font-bold text-accent">{{ formatCurrency(crypto.market_cap) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-card p-6">
              <p class="text-gray-500 text-sm mb-2">Offre en Circulation</p>
              <p class="text-2xl font-bold text-purple-600">{{ formatNumber(crypto.circulating_supply) }}</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="bg-white rounded-lg shadow-card p-6">
            <div class="flex gap-4">
              <button
                @click="buyCrypto"
                class="flex-1 px-6 py-3 bg-success text-white rounded-lg font-bold hover:opacity-90 transition"
              >
                ✓ Acheter
              </button>
              <button
                @click="sellCrypto"
                class="flex-1 px-6 py-3 bg-danger text-white rounded-lg font-bold hover:opacity-90 transition"
              >
                ✕ Vendre
              </button>
              <button
                @click="createAlert"
                class="flex-1 px-6 py-3 bg-accent text-white rounded-lg font-bold hover:opacity-90 transition"
              >
                🔔 Alerte
              </button>
            </div>
          </div>
        </template>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, watch } from 'vue'
import ClientSidebar from '../components/ClientSidebar.vue'
import CryptoLogo from '../components/CryptoLogo.vue'
import MarketChart from '../components/MarketChart.vue'
import { getCryptoById, getCryptoHistory, type Crypto } from '../services/cryptoApi'
import api from '../services/api'
import { useRouter, useRoute } from 'vue-router'

export default defineComponent({
  components: { ClientSidebar, CryptoLogo, MarketChart },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const isLoading = ref(true)
    const error = ref<string | null>(null)
    const userName = ref('Utilisateur')
    const selectedPeriod = ref(7)
    const chartData = ref<any[]>([])
    const chartLabels = ref<string[]>([])
    const priceChange = ref(0)
    const cryptoId = ref<string | number>(route.params.id || 1)
    const crypto = ref<Crypto>({
      id: 0,
      name: '',
      symbol: '',
      price: 0,
      market_cap: 0,
      volume_24h: 0,
      change_24h: 0,
      change_7d: 0
    })

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
      return new Intl.NumberFormat('fr-TN', {
        notation: 'compact',
        compactDisplay: 'short',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
      }).format(num)
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

    // Load crypto details
    const loadCryptoDetails = async () => {
      try {
        const cryptoData = await getCryptoById(cryptoId.value)
        crypto.value = cryptoData
        priceChange.value = cryptoData.change_24h || 0
      } catch (err: any) {
        error.value = err?.message || 'Erreur lors du chargement des détails de la crypto'
        console.error('Error loading crypto details:', err)
      }
    }

    // Load chart data
    const loadChartData = async () => {
      try {
        const historyData = await getCryptoHistory(cryptoId.value, selectedPeriod.value)
        if (Array.isArray(historyData)) {
          chartData.value = historyData
          chartLabels.value = historyData.map((item: any) => item.date || '')
        }
      } catch (err) {
        console.error('Error loading chart data:', err)
      }
    }

    // Main load function
    const loadData = async () => {
      try {
        isLoading.value = true
        error.value = null
        await Promise.all([
          loadProfile(),
          loadCryptoDetails()
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

    // Watch selectedPeriod changes
    watch(selectedPeriod, () => {
      loadChartData()
    })

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

    // Buy crypto
    const buyCrypto = () => {
      router.push(`/buy?crypto_id=${crypto.value.id}`)
    }

    // Sell crypto
    const sellCrypto = () => {
      router.push(`/sell?crypto_id=${crypto.value.id}`)
    }

    // Create alert
    const createAlert = () => {
      router.push(`/alerts?crypto_id=${crypto.value.id}`)
    }

    onMounted(loadData)

    return {
      isLoading,
      error,
      userName,
      selectedPeriod,
      chartData,
      chartLabels,
      crypto,
      priceChange,
      formatCurrency,
      formatNumber,
      logout,
      retryLoad,
      buyCrypto,
      sellCrypto,
      createAlert
    }
  }
})
</script>
