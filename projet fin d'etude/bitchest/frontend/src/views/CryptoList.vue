<template>
  <div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <h1 class="text-2xl font-bold mb-6">Liste des Cryptomonnaies</h1>
      
      <!-- Search and Filter Section -->
      <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Rechercher une crypto..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="flex gap-2">
          <select
            v-model="sortBy"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="market_cap">Market Cap</option>
            <option value="price">Prix</option>
            <option value="price_change_24h">Variation 24h</option>
            <option value="volume_24h">Volume 24h</option>
          </select>
          <button
            @click="toggleSortDirection"
            class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
          >
            {{ sortDirection === 'asc' ? '↑' : '↓' }}
          </button>
        </div>
      </div>

      <!-- Crypto Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="crypto in sortedAndFilteredCryptos"
          :key="crypto.id"
          class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <img
                :src="crypto.logo_url"
                :alt="crypto.name"
                class="w-12 h-12 rounded-full mr-4"
              />
              <div>
                <h3 class="font-bold text-lg">{{ crypto.name }}</h3>
                <p class="text-gray-600">{{ crypto.symbol }}</p>
              </div>
            </div>
            <div
              :class="[
                'px-3 py-1 rounded-full text-sm',
                crypto.price_change_24h > 0
                  ? 'bg-green-100 text-green-800'
                  : 'bg-red-100 text-red-800'
              ]"
            >
              {{ formatVariation(crypto.price_change_24h) }}
            </div>
          </div>

          <div class="space-y-2 mb-4">
            <div class="flex justify-between">
              <span class="text-gray-600">Prix</span>
              <span class="font-medium">{{ formatPrice(crypto.price) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Volume 24h</span>
              <span class="font-medium">{{ formatPrice(crypto.volume_24h) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Market Cap</span>
              <span class="font-medium">{{ formatPrice(crypto.market_cap) }}</span>
            </div>
          </div>

          <div class="flex justify-between space-x-2">
            <router-link
              :to="{ name: 'CryptoDetailPage', params: { id: crypto.id }}"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-center"
            >
              Détails
            </router-link>
            <button
              @click="createAlert(crypto)"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
            >
              Créer une alerte
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

export default defineComponent({
  name: 'CryptoList',

  setup() {
    const router = useRouter()
    const cryptos = ref<any[]>([])
    const searchQuery = ref('')
    const sortBy = ref('market_cap')
    const sortDirection = ref<'asc' | 'desc'>('desc')
    const updateInterval = ref<number | null>(null)

    const sortedAndFilteredCryptos = computed(() => {
      return [...cryptos.value]
        .filter(crypto => 
          crypto.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          crypto.symbol.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        .sort((a, b) => {
          const modifier = sortDirection.value === 'asc' ? 1 : -1
          if (a[sortBy.value] < b[sortBy.value]) return -1 * modifier
          if (a[sortBy.value] > b[sortBy.value]) return 1 * modifier
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

    const toggleSortDirection = () => {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    }

    const loadCryptos = async () => {
      try {
        const response = await api.get('/cryptocurrencies')
        cryptos.value = response.data.data || response.data
      } catch (error) {
        console.error('Erreur lors du chargement des cryptos:', error)
      }
    }

    const createAlert = (crypto: any) => {
      router.push({
        name: 'CryptoDetail',
        params: { id: crypto.id },
        hash: '#alerts'
      })
    }

    const startRealtimeUpdates = () => {
      loadCryptos() // Initial load
      updateInterval.value = window.setInterval(loadCryptos, 30000) // Update every 30 seconds
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
      searchQuery,
      sortBy,
      sortDirection,
      sortedAndFilteredCryptos,
      formatPrice,
      formatVariation,
      toggleSortDirection,
      createAlert
    }
  }
})
</script>