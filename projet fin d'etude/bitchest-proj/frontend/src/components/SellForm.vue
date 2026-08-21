<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">
          Vendre {{ crypto?.name }}
        </h3>

        <form @submit.prevent="submitSale" class="space-y-6">
          <!-- Available Balance -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Solde disponible
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">
                {{ formatQuantity(availableBalance) }} {{ crypto?.symbol }}
              </p>
            </div>
          </div>

          <!-- Quantity Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Quantité à vendre
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                type="number"
                v-model="quantity"
                :max="availableBalance"
                step="0.00000001"
                min="0"
                required
                class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"

              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ crypto?.symbol }}</span>
              </div>
            </div>
          </div>

          <!-- Estimated Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Montant estimé (EUR)
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">{{ formatPrice(estimatedAmount) }}</p>
            </div>
          </div>

          <!-- Fees -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Frais appliqués
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">{{ formatPrice(fees) }}</p>
              <p class="text-sm text-gray-500">({{ feePercentage }}%)</p>
            </div>
          </div>

          <!-- Net Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Montant net reçu
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">{{ formatPrice(netAmount) }}</p>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="text-red-600 text-sm mt-2">
            {{ error }}
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="cancel"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="!isValid || isSubmitting"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              Confirmer la vente
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

export default defineComponent({
  name: 'SellForm',

  props: {
    cryptoId: {
      type: String,
      required: true
    }
  },

  setup(props) {
    const router = useRouter()
    const crypto = ref<any>(null)
    const quantity = ref<number>(0)
    const availableBalance = ref<number>(0)
    const feePercentage = ref(2.5) // Example fee percentage
    const error = ref('')
    const isSubmitting = ref(false)

    const estimatedAmount = computed(() => {
      if (!crypto.value?.price || !quantity.value) return 0
      return quantity.value * crypto.value.price
    })

    const fees = computed(() => {
      return estimatedAmount.value * (feePercentage.value / 100)
    })

    const netAmount = computed(() => {
      return estimatedAmount.value - fees.value
    })

    const isValid = computed(() => {
      return quantity.value > 0 && 
             quantity.value <= availableBalance.value && 
             netAmount.value > 0
    })

    const formatPrice = (price: number) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price || 0)
    }

    const formatQuantity = (quantity: number) => {
      return quantity.toFixed(8)
    }

    const loadData = async () => {
      try {
        const [cryptoResponse, walletResponse] = await Promise.all([
          api.get(`/cryptos/${props.cryptoId}`),
          api.get(`/wallet/${props.cryptoId}`)
        ])
        
        crypto.value = cryptoResponse.data
        availableBalance.value = walletResponse.data.quantity
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error)
      }
    }

    const submitSale = async () => {
      if (!isValid.value) return

      isSubmitting.value = true
      error.value = ''

      try {
        await api.post('/transactions', {
          crypto_id: props.cryptoId,
          quantity: quantity.value,
          type: 'sell'
        })

        router.push({
          name: 'Portfolio',
          query: { success: 'sale' }
        })
      } catch (err: any) {
        error.value = err.response?.data?.message || 'Une erreur est survenue'
        isSubmitting.value = false
      }
    }

    const cancel = () => {
      router.back()
    }

    onMounted(() => {
      loadData()
    })

    return {
      crypto,
      quantity,
      availableBalance,
      estimatedAmount,
      fees,
      netAmount,
      feePercentage,
      error,
      isSubmitting,
      isValid,
      formatPrice,
      formatQuantity,
      submitSale,
      cancel
    }
  }
})
</script>