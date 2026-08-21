<template>
  <div class="p-6 bg-[#F9FAFB] min-h-[80vh]">
    <!-- En-tête de la page -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold mb-2">Marché des cryptomonnaies</h1>
      <p class="text-gray-600">Suivez en temps réel l'évolution des cryptomonnaies</p>
    </div>

    <!-- Barre de filtres et recherche -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
      <div class="flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px]">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Rechercher une crypto..." 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
        </div>
        <div class="flex gap-2">
          <select 
            v-model="sortBy"
            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="market_cap">Capitalisation</option>
            <option value="price">Prix</option>
            <option value="volume_24h">Volume 24h</option>
            <option value="price_change_24h">Variation 24h</option>
          </select>
          <button 
            @click="toggleSortDirection"
            class="px-4 py-2 border rounded-lg hover:bg-gray-50"
          >
            {{ sortDirection === 'asc' ? '↑' : '↓' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Vue d'ensemble du marché -->
    <MarketOverview 
      ref="marketOverview"
      :searchQuery="searchQuery"
      :sortBy="sortBy"
      :sortDirection="sortDirection"
    />

    <!-- Section des cryptos favorites -->
    <div v-if="favoritesCryptos.length > 0" class="mt-8">
      <h2 class="text-xl font-semibold mb-4">Vos cryptos favorites</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="crypto in favoritesCryptos" 
          :key="crypto.id"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
        >
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <img 
                :src="crypto.logo_url" 
                :alt="crypto.name"
                class="w-10 h-10 rounded-full mr-3"
              >
              <div>
                <h3 class="font-semibold">{{ crypto.name }}</h3>
                <p class="text-sm text-gray-500">{{ crypto.symbol }}</p>
              </div>
            </div>
            <button 
              @click="toggleFavorite(crypto)"
              class="text-yellow-500 hover:text-yellow-600"
            >
              ⭐
            </button>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Prix</p>
              <p class="font-semibold">{{ formatPrice(crypto.price) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Variation 24h</p>
              <p :class="[
                'font-semibold',
                crypto.price_change_24h > 0 ? 'text-green-600' : 'text-red-600'
              ]">
                {{ formatVariation(crypto.price_change_24h) }}
              </p>
            </div>
          </div>

          <div class="mt-4 flex justify-end gap-2">
            <router-link
              :to="{ name: 'CryptoDetail', params: { id: crypto.id }}"
              class="px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
            >
              Détails
            </router-link>
            <button
              @click="handleQuickBuy(crypto)"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Acheter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal d'achat rapide -->
  <TransitionRoot appear :show="showQuickBuyModal" as="template">
    <Dialog as="div" @close="closeQuickBuyModal" class="relative z-10">
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
                Acheter {{ selectedCrypto?.name }}
              </DialogTitle>
              
              <div class="mt-4">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Prix actuel
                    </label>
                    <p class="mt-1 font-semibold">
                      {{ formatPrice(selectedCrypto?.price) }}
                    </p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Quantité à acheter
                    </label>
                    <input
                      type="number"
                      v-model="quickBuyForm.quantity"
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
                      {{ formatPrice(quickBuyForm.quantity * (selectedCrypto?.price || 0)) }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="closeQuickBuyModal"
                  class="px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-lg"
                >
                  Annuler
                </button>
                <button
                  @click="submitQuickBuy"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  :disabled="quickBuyForm.quantity <= 0"
                >
                  Confirmer l'achat
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
import { defineComponent, ref, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import MarketOverview from '@/components/MarketOverview.vue'
import api from '@/services/api'

export default defineComponent({
  name: 'MarketView',
  
  components: {
    MarketOverview,
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot
  },

  setup() {
    const marketOverview = ref<InstanceType<typeof MarketOverview> | null>(null)
    const searchQuery = ref('')
    const sortBy = ref('market_cap')
    const sortDirection = ref<'asc' | 'desc'>('desc')
    const favoritesCryptos = ref<any[]>([])

    // États pour le modal d'achat rapide
    const showQuickBuyModal = ref(false)
    const selectedCrypto = ref<any>(null)
    const quickBuyForm = ref({
      quantity: 0
    })

    const toggleSortDirection = () => {
      sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    }

    const loadFavorites = async () => {
      try {
        const response = await api.get('/favorites')
        favoritesCryptos.value = response.data.data || response.data
      } catch (error) {
        console.error('Erreur lors du chargement des favoris:', error)
      }
    }

    const toggleFavorite = async (crypto: any) => {
      try {
        await api.post(`/favorites/${crypto.id}/toggle`)
        await loadFavorites() // Recharger la liste des favoris
      } catch (error) {
        console.error('Erreur lors de la modification des favoris:', error)
      }
    }

    const handleQuickBuy = (crypto: any) => {
      selectedCrypto.value = crypto
      quickBuyForm.value.quantity = 0
      showQuickBuyModal.value = true
    }

    const closeQuickBuyModal = () => {
      showQuickBuyModal.value = false
      selectedCrypto.value = null
      quickBuyForm.value.quantity = 0
    }

    const submitQuickBuy = async () => {
      if (!selectedCrypto.value || quickBuyForm.value.quantity <= 0) return
      
      try {
        await api.post('/transactions', {
          type: 'buy',
          crypto_id: selectedCrypto.value.id,
          quantity: quickBuyForm.value.quantity
        })
        closeQuickBuyModal()
        // Notifier l'utilisateur du succès
      } catch (error) {
        console.error('Erreur lors de l\'achat:', error)
        // Notifier l'utilisateur de l'erreur
      }
    }

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

    // Chargement initial des favoris
    loadFavorites()

    return {
      marketOverview,
      searchQuery,
      sortBy,
      sortDirection,
      favoritesCryptos,
      showQuickBuyModal,
      selectedCrypto,
      quickBuyForm,
      toggleSortDirection,
      toggleFavorite,
      handleQuickBuy,
      closeQuickBuyModal,
      submitQuickBuy,
      formatPrice,
      formatVariation
    }
  }
})
</script>