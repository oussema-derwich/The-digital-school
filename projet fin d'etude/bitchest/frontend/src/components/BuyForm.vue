<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">
          Acheter {{ crypto?.name }}
        </h3>

        <form @submit.prevent="submitPurchase" class="space-y-6">
          <!-- Amount Input -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Montant à investir (EUR)
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                type="number"
                v-model="amount"
                step="0.01"
                min="0"
                required
                class="block w-full pr-12 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">EUR</span>
              </div>
            </div>
          </div>

          <!-- Estimated Quantity -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Quantité estimée
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">
                {{ formatQuantity(estimatedQuantity) }} {{ crypto?.symbol }}
              </p>
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

          <!-- Total -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Total à payer
            </label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md">
              <p class="text-lg font-medium">{{ formatPrice(total) }}</p>
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
              Confirmer l'achat
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
  name: 'BuyForm',

  props: {
    cryptoId: {
      type: String,
      required: true
    }
  },

  setup(props) {
    const router = useRouter()
    const crypto = ref<any>(null)
    const amount = ref<number>(0)
    const feePercentage = ref(2.5) // Example fee percentage
    const error = ref('')
    const isSubmitting = ref(false)

    const estimatedQuantity = computed(() => {
      if (!crypto.value?.price || !amount.value) return 0
      return (amount.value * (1 - feePercentage.value / 100)) / crypto.value.price
    })

    const fees = computed(() => {
      return amount.value * (feePercentage.value / 100)
    })

    const total = computed(() => {
      return amount.value
    })

    const isValid = computed(() => {
      return amount.value > 0 && estimatedQuantity.value > 0
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

    const loadCryptoData = async () => {
      try {
        const response = await api.get(`/cryptos/${props.cryptoId}`)
        crypto.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error)
      }
    }

    const submitPurchase = async () => {
      if (!isValid.value) return

      isSubmitting.value = true
      error.value = ''

      try {
        await api.post('/transactions', {
          crypto_id: props.cryptoId,
          amount: amount.value,
          type: 'buy'
        })

        router.push({
          name: 'Portfolio',
          query: { success: 'purchase' }
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
      loadCryptoData()
    })

    return {
      crypto,
      amount,
      estimatedQuantity,
      fees,
      total,
      feePercentage,
      error,
      isSubmitting,
      isValid,
      formatPrice,
      formatQuantity,
      submitPurchase,
      cancel
    }
  }
})
</script>