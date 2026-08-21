<template>
  <div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Marché en direct</h2>
      <div class="space-x-2">
        <router-link 
          to="/wallet" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Accéder au portefeuille
        </router-link>
      </div>
    </div>

    <!-- Market Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-sm text-gray-500">Volume total 24h</div>
        <div class="text-xl font-bold">{{ formatPrice(totalVolume) }}</div>
      </div>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-sm text-gray-500">Capitalisation totale</div>
        <div class="text-xl font-bold">{{ formatPrice(totalMarketCap) }}</div>
      </div>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-sm text-gray-500">Cryptos suivies</div>
        <div class="text-xl font-bold">{{ cryptos.length }}</div>
      </div>
    </div>

    <!-- Crypto List -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crypto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variation 24h</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volume 24h</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Market Cap</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="crypto in sortedCryptos" :key="crypto.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img :src="crypto.logo_url" :alt="crypto.name" class="w-8 h-8 rounded-full mr-3">
                <div>
                  <div class="font-medium text-gray-900">{{ crypto.name }}</div>
                  <div class="text-sm text-gray-500">{{ crypto.symbol }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ formatPrice(crypto.price) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'px-2 py-1 text-sm rounded-full',
                crypto.price_change_24h > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ formatVariation(crypto.price_change_24h) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatPrice(crypto.volume_24h) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatPrice(crypto.market_cap) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
              <router-link 
                :to="{ name: 'CryptoDetail', params: { id: crypto.id }}" 
                class="text-blue-600 hover:text-blue-900"
              >
                Détails
              </router-link>
              <button
                @click="createAlert(crypto)"
                class="text-blue-600 hover:text-blue-900"
              >
                Créer une alerte
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

export default defineComponent({
  name: 'MarketOverview',

  setup() {
    const router = useRouter()
    const cryptos = ref<any[]>([])
    const sortField = ref('market_cap')
    const sortDirection = ref<'asc' | 'desc'>('desc')
    const updateInterval = ref<number | null>(null)

    const totalVolume = computed(() => 
      cryptos.value.reduce((sum, crypto) => sum + (crypto.volume_24h || 0), 0)
    )

    const totalMarketCap = computed(() => 
      cryptos.value.reduce((sum, crypto) => sum + (crypto.market_cap || 0), 0)
    )

    const sortedCryptos = computed(() => {
      return [...cryptos.value].sort((a, b) => {
        const modifier = sortDirection.value === 'asc' ? 1 : -1
        if (a[sortField.value] < b[sortField.value]) return -1 * modifier
        if (a[sortField.value] > b[sortField.value]) return 1 * modifier
        return 0
      })
    })

    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price || 0)
    }

    const formatVariation = (variation: number) => {
      const prefix = variation > 0 ? '+' : ''
      return `${prefix}${variation?.toFixed(2)}%`
    }

    const loadMarketData = async () => {
      try {
        const response = await api.get('/cryptos/market')
        cryptos.value = response.data.data || response.data
      } catch (error) {
        console.error('Erreur lors du chargement des données du marché:', error)
      }
    }

    const startRealtimeUpdates = () => {
      loadMarketData() // Initial load
      updateInterval.value = window.setInterval(loadMarketData, 30000) // Update every 30 seconds
    }

    const createAlert = (crypto: any) => {
      router.push({
        name: 'CryptoDetail',
        params: { id: crypto.id },
        hash: '#alerts'
      })
    }

    onMounted(() => {
      startRealtimeUpdates()
    })

    onUnmounted(() => {
      if (updateInterval.value) {
        clearInterval(updateInterval.value)
      }
    })

    return {
      cryptos,
      sortedCryptos,
      totalVolume,
      totalMarketCap,
      formatPrice,
      formatVariation,
      createAlert
    }
  }
})
</script>