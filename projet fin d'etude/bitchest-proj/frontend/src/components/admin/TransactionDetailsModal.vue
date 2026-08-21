<template>
  <div 
    class="fixed z-50 inset-0 overflow-y-auto" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
  >
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
        aria-hidden="true"
        @click="$emit('close')"
      ></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div v-if="loading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          </div>

          <div v-else-if="error" class="text-center py-8">
            <div class="text-red-600 mb-2">{{ error }}</div>
            <button 
              @click="fetchTransactionDetails"
              class="text-blue-600 hover:text-blue-800"
            >
              Réessayer
            </button>
          </div>

          <div v-else-if="transactionDetails">
            <!-- Header -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                Détails de la Transaction
              </h3>
              <p class="text-sm text-gray-500 mt-1">
                ID: {{ transactionDetails.id }}
              </p>
            </div>

            <!-- Transaction info -->
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <p class="text-sm font-medium text-gray-500">Type</p>
                <p class="mt-1">
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="transactionDetails.type === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ transactionDetails.type === 'buy' ? 'Achat' : 'Vente' }}
                  </span>
                </p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Date</p>
                <p class="mt-1 text-sm text-gray-900">
                  {{ formatDateTime(transactionDetails.created_at) }}
                </p>
              </div>
            </div>

            <!-- User info -->
            <div class="mb-6">
              <p class="text-sm font-medium text-gray-500 mb-2">Utilisateur</p>
              <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                  {{ getInitials(transactionDetails.user.name) }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ transactionDetails.user.name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ transactionDetails.user.email }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Transaction details -->
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <p class="text-sm font-medium text-gray-500">Cryptomonnaie</p>
                  <p class="mt-1 text-sm text-gray-900">{{ transactionDetails.crypto.name }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Prix unitaire</p>
                  <p class="mt-1 text-sm text-gray-900">{{ formatCurrency(transactionDetails.price) }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Quantité</p>
                  <p class="mt-1 text-sm text-gray-900">{{ transactionDetails.quantity }} {{ transactionDetails.crypto.symbol }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500">Montant total</p>
                  <p class="mt-1 text-sm font-medium text-gray-900">{{ formatCurrency(transactionDetails.amount) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button 
            type="button" 
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            @click="$emit('close')"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, PropType, onMounted } from 'vue'
import api from '@/services/api'

interface TransactionDetails {
  id: number
  type: 'buy' | 'sell'
  user: {
    name: string
    email: string
  }
  crypto: {
    name: string
    symbol: string
  }
  quantity: number
  price: number
  amount: number
  created_at: string
}

export default defineComponent({
  name: 'TransactionDetailsModal',

  props: {
    transactionId: {
      type: [String, Number],
      required: true
    }
  },

  emits: ['close'],

  setup(props) {
    const loading = ref(true)
    const error = ref('')
    const transactionDetails = ref<TransactionDetails | null>(null)

    const fetchTransactionDetails = async () => {
      loading.value = true
      error.value = ''

      try {
        const response = await api.get(`/admin/transactions/${props.transactionId}`)
        transactionDetails.value = response.data
      } catch (err) {
        error.value = 'Erreur lors du chargement des détails de la transaction'
        console.error('Error fetching transaction details:', err)
      } finally {
        loading.value = false
      }
    }

    const getInitials = (name: string): string => {
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
    }

    const formatCurrency = (value: number): string => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(value)
    }

    const formatDateTime = (date: string): string => {
      return new Date(date).toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      fetchTransactionDetails()
    })

    return {
      loading,
      error,
      transactionDetails,
      fetchTransactionDetails,
      getInitials,
      formatCurrency,
      formatDateTime
    }
  }
})
</script>