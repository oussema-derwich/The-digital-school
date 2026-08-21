<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
      <!-- En-tête -->
      <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Gestion des cryptomonnaies
          </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <button
            @click="showAddModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Ajouter une cryptomonnaie
          </button>
        </div>
      </div>

      <!-- Liste des cryptomonnaies -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="crypto in cryptos" :key="crypto.id" class="px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <img
                  :src="crypto.logo"
                  :alt="crypto.name"
                  class="h-10 w-10 rounded-full"
                />
                <div class="ml-4">
                  <div class="flex items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ crypto.name }}</h3>
                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                      {{ crypto.symbol }}
                    </span>
                  </div>
                  <div class="mt-1 text-sm text-gray-500">
                    Prix actuel: {{ formatCurrency(crypto.current_price) }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-4">
                <div class="text-sm">
                  <span
                    :class="[
                      crypto.price_change_24h >= 0
                        ? 'text-green-600'
                        : 'text-red-600',
                      'font-medium'
                    ]"
                  >
                    {{ formatPercentage(crypto.price_change_24h) }}
                  </span>
                  <span class="text-gray-500">24h</span>
                </div>
                <div>
                  <span
                    :class="[
                      crypto.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800',
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                    ]"
                  >
                    {{ crypto.is_active ? 'Actif' : 'Inactif' }}
                  </span>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editCrypto(crypto)"
                    class="inline-flex items-center p-1 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <PencilIcon class="h-5 w-5" />
                  </button>
                  <button
                    @click="toggleCryptoStatus(crypto)"
                    class="inline-flex items-center p-1 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    <SwitchVerticalIcon class="h-5 w-5" />
                  </button>
                  <button
                    @click="confirmDelete(crypto)"
                    class="inline-flex items-center p-1 border border-transparent rounded-md text-white bg-red-600 hover:bg-red-700"
                  >
                    <TrashIcon class="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal d'ajout/édition -->
    <div v-if="showAddModal || showEditModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ showAddModal ? 'Ajouter une cryptomonnaie' : 'Modifier la cryptomonnaie' }}
            </h3>
            <form @submit.prevent="handleSubmit" class="mt-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Nom
                  </label>
                  <input
                    type="text"
                    v-model="formData.name"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Symbole
                  </label>
                  <input
                    type="text"
                    v-model="formData.symbol"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Prix de référence
                  </label>
                  <input
                    type="number"
                    v-model="formData.current_price"
                    required
                    min="0"
                    step="0.000001"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Logo
                  </label>
                  <input
                    type="file"
                    @change="handleLogoUpload"
                    accept="image/png,image/jpeg,image/svg+xml"
                    class="mt-1 block w-full"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Description
                  </label>
                  <textarea
                    v-model="formData.description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  ></textarea>
                </div>
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="formData.is_active"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-900">
                    Actif
                  </label>
                </div>
              </div>
              <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button
                  type="submit"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
                >
                  {{ showAddModal ? 'Ajouter' : 'Enregistrer' }}
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                >
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon, ArrowsUpDownIcon } from '@heroicons/vue/24/outline'
import api from '@/services/api'

export default {
  components: {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ArrowsUpDownIcon
  },

  setup() {
    const cryptos = ref([])
    const showAddModal = ref(false)
    const showEditModal = ref(false)
    const formData = ref({
      name: '',
      symbol: '',
      current_price: 0,
      description: '',
      is_active: true
    })

    const fetchCryptos = async () => {
      try {
        const response = await api.get('/admin/cryptos')
        cryptos.value = response.data
      } catch (error) {
        console.error('Erreur lors du chargement des cryptomonnaies:', error)
      }
    }

    const handleLogoUpload = (event) => {
      const file = event.target.files[0]
      formData.value.logo = file
    }

    const editCrypto = (crypto) => {
      formData.value = { ...crypto }
      showEditModal.value = true
    }

    const handleSubmit = async () => {
      try {
        const data = new FormData()
        for (const key in formData.value) {
          data.append(key, formData.value[key])
        }

        if (showAddModal.value) {
          await api.post('/admin/cryptos', data)
        } else {
          await api.put(`/admin/cryptos/${formData.value.id}`, data)
        }

        await fetchCryptos()
        closeModal()
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement:', error)
      }
    }

    const toggleCryptoStatus = async (crypto) => {
      try {
        await api.post(`/admin/cryptos/${crypto.id}/toggle-status`)
        crypto.is_active = !crypto.is_active
      } catch (error) {
        console.error('Erreur lors du changement de statut:', error)
      }
    }

    const confirmDelete = async (crypto) => {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette cryptomonnaie ? Cette action est irréversible.')) {
        try {
          await api.delete(`/admin/cryptos/${crypto.id}`)
          cryptos.value = cryptos.value.filter(c => c.id !== crypto.id)
        } catch (error) {
          console.error('Erreur lors de la suppression:', error)
        }
      }
    }

    const closeModal = () => {
      showAddModal.value = false
      showEditModal.value = false
      formData.value = {
        name: '',
        symbol: '',
        current_price: 0,
        description: '',
        is_active: true
      }
    }

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(value)
    }

    const formatPercentage = (value) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'percent',
        minimumFractionDigits: 2
      }).format(value / 100)
    }

    onMounted(() => {
      fetchCryptos()
    })

    return {
      cryptos,
      showAddModal,
      showEditModal,
      formData,
      handleLogoUpload,
      editCrypto,
      handleSubmit,
      toggleCryptoStatus,
      confirmDelete,
      closeModal,
      formatCurrency,
      formatPercentage
    }
  }
}
</script>