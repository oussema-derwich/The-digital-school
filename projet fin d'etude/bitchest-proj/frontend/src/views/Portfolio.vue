<template>
  <div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <!-- Portfolio Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Valeur Totale</h3>
          <p class="text-3xl font-bold text-blue-600">{{ formatPrice(totalValue) }}</p>
        </div>
        
        <div :class="[
          'rounded-lg p-6',
          totalGainLoss > 0 ? 'bg-green-50' : 'bg-red-50'
        ]">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Gain/Perte Total</h3>
          <p :class="[
            'text-3xl font-bold',
            totalGainLoss > 0 ? 'text-green-600' : 'text-red-600'
          ]">
            {{ formatPrice(totalGainLoss) }}
          </p>
        </div>

        <div class="bg-gray-50 rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-600 mb-2">Nombre d'actifs</h3>
          <p class="text-3xl font-bold text-gray-700">{{ assets.length }}</p>
        </div>
      </div>

      <!-- Assets List -->
      <div class="space-y-6">
        <div v-for="asset in assets" :key="asset.crypto_id" 
          class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <!-- Crypto Info -->
            <div class="flex items-center">
              <img :src="asset.crypto.logo_url" :alt="asset.crypto.name" 
                class="w-12 h-12 rounded-full mr-4">
              <div>
                <h3 class="font-bold text-lg">{{ asset.crypto.name }}</h3>
                <p class="text-gray-600">{{ asset.crypto.symbol }}</p>
              </div>
            </div>

            <!-- Current Value -->
            <div class="text-right">
              <p class="text-lg font-bold">{{ formatPrice(asset.current_value) }}</p>
              <p :class="[
                'text-sm',
                asset.gain_loss > 0 ? 'text-green-600' : 'text-red-600'
              ]">
                {{ formatVariation(asset.gain_loss_percentage) }}
              </p>
            </div>
          </div>

          <!-- Asset Details -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div>
              <p class="text-sm text-gray-600">Quantité</p>
              <p class="font-medium">{{ formatQuantity(asset.quantity) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Prix moyen d'achat</p>
              <p class="font-medium">{{ formatPrice(asset.average_buy_price) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Prix actuel</p>
              <p class="font-medium">{{ formatPrice(asset.crypto.price) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Gain/Perte</p>
              <p :class="[
                'font-medium',
                asset.gain_loss > 0 ? 'text-green-600' : 'text-red-600'
              ]">
                {{ formatPrice(asset.gain_loss) }}
              </p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4 mt-4">
            <router-link 
              :to="{ name: 'CryptoDetail', params: { id: asset.crypto_id }}"
              class="px-4 py-2 text-blue-600 hover:text-blue-800"
            >
              Voir détails
            </router-link>
            <router-link 
              :to="{ name: 'Sell', params: { cryptoId: asset.crypto_id }}"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Vendre
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

export default defineComponent({
  name: 'Portfolio',

  setup() {
    const assets = ref<any[]>([])
    const updateInterval = ref<number | null>(null)

    const totalValue = computed(() => 
      assets.value.reduce((sum, asset) => sum + asset.current_value, 0)
    )

    const totalGainLoss = computed(() =>
      assets.value.reduce((sum, asset) => sum + asset.gain_loss, 0)
    )

    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price || 0)
    }

    const formatQuantity = (quantity: number) => {
      return quantity.toFixed(8)
    }

    const formatVariation = (variation: number) => {
      const prefix = variation > 0 ? '+' : ''
      return `${prefix}${variation?.toFixed(2)}%`
    }

    const loadPortfolio = async () => {
      try {
        const response = await api.get('/portfolio')
        assets.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement du portefeuille:', error)
      }
    }

    const startRealtimeUpdates = () => {
      loadPortfolio() // Initial load
      updateInterval.value = window.setInterval(loadPortfolio, 30000) // Update every 30 seconds
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
      assets,
      totalValue,
      totalGainLoss,
      formatPrice,
      formatQuantity,
      formatVariation
    }
  }
})
</script>