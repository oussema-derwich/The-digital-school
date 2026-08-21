<template>
  <div class="p-6 bg-[#F9FAFB] min-h-[80vh]">
    <!-- En-tête avec résumé -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h1 class="text-2xl font-semibold mb-6">Mon portefeuille</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-4">
          <h3 class="text-sm font-medium text-gray-600">Valeur totale</h3>
          <p class="text-2xl font-bold text-blue-600">{{ formatPrice(portfolioValue) }}</p>
          <p :class="[
            'text-sm mt-1',
            portfolioValueChange >= 0 ? 'text-green-600' : 'text-red-600'
          ]">
            {{ formatVariation(portfolioValueChange) }} (24h)
          </p>
        </div>

        <div :class="[
          'rounded-lg p-4',
          totalProfitLoss >= 0 ? 'bg-green-50' : 'bg-red-50'
        ]">
          <h3 class="text-sm font-medium text-gray-600">Gains/Pertes totaux</h3>
          <p :class="[
            'text-2xl font-bold',
            totalProfitLoss >= 0 ? 'text-green-600' : 'text-red-600'
          ]">
            {{ formatPrice(totalProfitLoss) }}
          </p>
          <p class="text-sm mt-1 text-gray-600">
            {{ formatVariation(profitLossPercentage) }}
          </p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
          <h3 class="text-sm font-medium text-gray-600">Cryptos détenues</h3>
          <p class="text-2xl font-bold text-gray-700">{{ assets.length }}</p>
          <p class="text-sm mt-1 text-gray-600">
            sur {{ totalCryptos }} disponibles
          </p>
        </div>
      </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Historique de valeur -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="font-semibold">Évolution de la valeur</h2>
          <div class="flex gap-2">
            <button 
              v-for="period in ['7J', '30J', '90J']"
              :key="period"
              @click="changeHistoryPeriod(period)"
              :class="[
                'px-3 py-1 rounded-md text-sm',
                selectedPeriod === period
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:bg-gray-100'
              ]"
            >
              {{ period }}
            </button>
          </div>
        </div>
        <PortfolioValueChart :data="historyData" />
      </div>

      <!-- Répartition -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="font-semibold mb-4">Répartition du portefeuille</h2>
        <PortfolioDonutChart :data="donutChartData" />
      </div>
    </div>

    <!-- Liste des actifs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-wrap justify-between items-center">
          <h2 class="font-semibold">Mes positions</h2>
          <div class="flex items-center gap-4">
            <div class="relative">
              <input 
                type="text"
                v-model="search"
                placeholder="Rechercher..."
                class="pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                🔍
              </span>
            </div>
            <button
              @click="refreshData"
              class="p-2 text-gray-600 hover:bg-gray-50 rounded-full"
            >
              🔄
            </button>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crypto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix moyen</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix actuel</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur totale</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gains/Pertes</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr 
              v-for="asset in filteredAssets" 
              :key="asset.crypto_id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img 
                    :src="asset.crypto.logo_url" 
                    :alt="asset.crypto.name"
                    class="w-8 h-8 rounded-full mr-3"
                  >
                  <div>
                    <div class="font-medium text-gray-900">{{ asset.crypto.name }}</div>
                    <div class="text-sm text-gray-500">{{ asset.crypto.symbol }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatQuantity(asset.quantity) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(asset.average_buy_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ formatPrice(asset.crypto.price) }}
                </div>
                <div :class="[
                  'text-xs',
                  asset.crypto.price_change_24h >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ formatVariation(asset.crypto.price_change_24h) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                {{ formatPrice(asset.current_value) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div :class="[
                  'text-sm font-medium',
                  asset.profit_loss >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ formatPrice(asset.profit_loss) }}
                </div>
                <div :class="[
                  'text-xs',
                  asset.profit_loss_percentage >= 0 ? 'text-green-600' : 'text-red-600'
                ]">
                  {{ formatVariation(asset.profit_loss_percentage) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <router-link
                    :to="{ name: 'CryptoDetail', params: { id: asset.crypto_id }}"
                    class="text-blue-600 hover:text-blue-900"
                  >
                    Détails
                  </router-link>
                  <button
                    @click="handleQuickSell(asset)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Vendre
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal de vente rapide -->
  <TransitionRoot appear :show="showQuickSellModal" as="template">
    <Dialog as="div" @close="closeQuickSellModal" class="relative z-10">
      <TransitionChild
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-md rounded-2xl bg-white p-6">
              <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                Vendre {{ selectedAsset?.crypto.name }}
              </DialogTitle>
              
              <div class="mt-4">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Prix actuel
                    </label>
                    <p class="mt-1 font-semibold">
                      {{ formatPrice(selectedAsset?.crypto.price) }}
                    </p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Quantité disponible
                    </label>
                    <p class="mt-1 text-sm text-gray-600">
                      {{ formatQuantity(selectedAsset?.quantity || 0) }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Quantité à vendre
                    </label>
                    <input
                      type="number"
                      v-model="quickSellForm.quantity"
                      :max="selectedAsset?.quantity"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      step="0.000001"
                      min="0"
                    >
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Montant total
                    </label>
                    <p class="mt-1 font-semibold">
                      {{ formatPrice(quickSellForm.quantity * (selectedAsset?.crypto.price || 0)) }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Profit/Perte estimé
                    </label>
                    <p :class="[
                      'mt-1 font-semibold',
                      estimatedProfitLoss >= 0 ? 'text-green-600' : 'text-red-600'
                    ]">
                      {{ formatPrice(estimatedProfitLoss) }}
                      ({{ formatVariation(estimatedProfitLossPercentage) }})
                    </p>
                  </div>
                </div>
              </div>

              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="closeQuickSellModal"
                  class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg"
                >
                  Annuler
                </button>
                <button
                  @click="submitQuickSell"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                  :disabled="!canSell"
                >
                  Confirmer la vente
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted, onUnmounted } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import PortfolioValueChart from '@/components/PortfolioValueChart.vue'
import PortfolioDonutChart from '@/components/PortfolioDonutChart.vue'
import api from '@/services/api'

export default defineComponent({
  name: 'WalletView',
  
  components: {
    PortfolioValueChart,
    PortfolioDonutChart,
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot
  },

  setup() {
    // États du portfolio
    const assets = ref<any[]>([])
    const historyData = ref<any[]>([])
    const totalCryptos = ref(0)
    const search = ref('')
    const selectedPeriod = ref('30J')
    const updateInterval = ref<number | null>(null)
    
    // États du modal de vente
    const showQuickSellModal = ref(false)
    const selectedAsset = ref<any>(null)
    const quickSellForm = ref({
      quantity: 0
    })

    // Données calculées
    const portfolioValue = computed(() => 
      assets.value.reduce((sum, asset) => sum + asset.current_value, 0)
    )

    const portfolioValueChange = computed(() => 
      assets.value.reduce((sum, asset) => 
        sum + (asset.crypto.price_change_24h * asset.current_value / 100), 0
      )
    )

    const totalProfitLoss = computed(() =>
      assets.value.reduce((sum, asset) => sum + asset.profit_loss, 0)
    )

    const profitLossPercentage = computed(() => {
      const totalCost = assets.value.reduce((sum, asset) => 
        sum + (asset.average_buy_price * asset.quantity), 0
      )
      return totalCost > 0 ? (totalProfitLoss.value / totalCost) * 100 : 0
    })

    const filteredAssets = computed(() => {
      const query = search.value.toLowerCase()
      return assets.value.filter(asset => 
        asset.crypto.name.toLowerCase().includes(query) ||
        asset.crypto.symbol.toLowerCase().includes(query)
      )
    })

    const donutChartData = computed(() => {
      const colors = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
        '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6'
      ]
      return assets.value.map((asset, index) => ({
        name: asset.crypto.symbol,
        value: asset.current_value,
        color: colors[index % colors.length]
      }))
    })

    // Fonctions utilitaires
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

    // Actions
    const refreshData = async () => {
      try {
        const [assetsResponse, historyResponse, cryptosResponse] = await Promise.all([
          api.get('/portfolio'),
          api.get(`/portfolio/history?period=${selectedPeriod.value}`),
          api.get('/cryptos/count')
        ])
        
        assets.value = assetsResponse.data.data.holdings || []
        historyData.value = historyResponse.data.data.history || []
        totalCryptos.value = cryptosResponse.data.data.count || 0
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error)
      }
    }

    const changeHistoryPeriod = async (period: string) => {
      selectedPeriod.value = period
      try {
        const response = await api.get(`/portfolio/history?period=${period}`)
        historyData.value = response.data.data.history || []
      } catch (error) {
        console.error('Erreur lors du chargement de l\'historique:', error)
      }
    }

    const handleQuickSell = (asset: any) => {
      selectedAsset.value = asset
      quickSellForm.value.quantity = 0
      showQuickSellModal.value = true
    }

    const closeQuickSellModal = () => {
      showQuickSellModal.value = false
      selectedAsset.value = null
      quickSellForm.value.quantity = 0
    }

    const canSell = computed(() => {
      return selectedAsset.value &&
             quickSellForm.value.quantity > 0 &&
             quickSellForm.value.quantity <= selectedAsset.value.quantity
    })

    const estimatedProfitLoss = computed(() => {
      if (!selectedAsset.value || !quickSellForm.value.quantity) return 0
      
      const sellValue = quickSellForm.value.quantity * selectedAsset.value.crypto.price
      const buyValue = quickSellForm.value.quantity * selectedAsset.value.average_buy_price
      return sellValue - buyValue
    })

    const estimatedProfitLossPercentage = computed(() => {
      if (!selectedAsset.value || !quickSellForm.value.quantity) return 0
      
      const buyValue = quickSellForm.value.quantity * selectedAsset.value.average_buy_price
      return buyValue > 0 ? (estimatedProfitLoss.value / buyValue) * 100 : 0
    })

    const submitQuickSell = async () => {
      if (!selectedAsset.value || !canSell.value) return
      
      try {
        await api.post('/transactions', {
          type: 'sell',
          cryptocurrency_id: selectedAsset.value.crypto_id,
          quantity: quickSellForm.value.quantity
        })
        closeQuickSellModal()
        await refreshData()
      } catch (error) {
        console.error('Erreur lors de la vente:', error)
      }
    }

    // Cycle de vie du composant
    onMounted(() => {
      refreshData()
      // Actualisation toutes les 30 secondes
      updateInterval.value = window.setInterval(refreshData, 30000)
    })

    onUnmounted(() => {
      if (updateInterval.value) {
        clearInterval(updateInterval.value)
      }
    })

    return {
      assets,
      filteredAssets,
      historyData,
      donutChartData,
      search,
      selectedPeriod,
      totalCryptos,
      portfolioValue,
      portfolioValueChange,
      totalProfitLoss,
      profitLossPercentage,
      showQuickSellModal,
      selectedAsset,
      quickSellForm,
      canSell,
      estimatedProfitLoss,
      estimatedProfitLossPercentage,
      formatPrice,
      formatQuantity,
      formatVariation,
      refreshData,
      changeHistoryPeriod,
      handleQuickSell,
      closeQuickSellModal,
      submitQuickSell
    }
  }
})
</script>
