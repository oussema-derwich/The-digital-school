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
        <h2 class="text-3xl font-bold text-secondary mb-8">Liste des Cryptomonnaies</h2>

        <!-- Search Bar -->
        <div class="mb-6">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par nom ou symbole..."
            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg text-base font-medium focus:border-primary focus:outline-none transition"
          />
        </div>

        <!-- Crypto Table -->
        <div class="bg-white rounded-lg shadow-card p-6 mb-8">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-secondary">Cryptos Disponibles</h3>
            <button
              @click="refreshData"
              class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition"
            >
              🔄 Actualiser
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Logo</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Nom</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Symbole</th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700 cursor-pointer hover:text-primary" @click="sortBy('price')">
                    Prix
                  </th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700 cursor-pointer hover:text-primary" @click="sortBy('variation')">
                    Variation
                  </th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700 cursor-pointer hover:text-primary" @click="sortBy('volume')">
                    Volume 24h
                  </th>
                  <th class="text-center py-3 px-4 font-bold text-gray-700">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="crypto in filteredCryptos" :key="crypto.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
                  <td class="py-4 px-4">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                      <img :src="crypto.logo" :alt="crypto.name" class="w-full h-full object-cover" />
                    </div>
                  </td>
                  <td class="py-4 px-4 font-semibold text-gray-800">{{ crypto.name }}</td>
                  <td class="py-4 px-4 text-gray-700">{{ crypto.symbol }}</td>
                  <td class="text-right py-4 px-4 font-semibold text-gray-800">{{ crypto.price }} DT</td>
                  <td
                    :class="[
                      'text-right py-4 px-4 font-bold',
                      parseFloat(crypto.variation) >= 0 ? 'text-success' : 'text-danger'
                    ]"
                  >
                    {{ crypto.variation }}%
                  </td>
                  <td class="text-right py-4 px-4 text-gray-700">{{ crypto.volume }}</td>
                  <td class="text-center py-4 px-4">
                    <button
                      @click="viewDetails(crypto)"
                      class="px-3 py-1 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition"
                    >
                      Détails
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import ClientSidebar from '../components/ClientSidebar.vue'
import api from '../services/api'
import { useRouter } from 'vue-router'

interface Crypto {
  id: number
  name: string
  symbol: string
  logo: string
  price: string
  variation: string
  volume: string
}

export default defineComponent({
  components: { ClientSidebar },
  setup() {
    const router = useRouter()
    const userName = ref('Utilisateur')
    const searchQuery = ref('')
    const sortField = ref('price')
    
    const cryptos = ref<Crypto[]>([
      {
        id: 1,
        name: 'Bitcoin',
        symbol: 'BTC',
        logo: '/assets/bitcoin.png',
        price: '82 250',
        variation: '+3.72',
        volume: '120M'
      },
      {
        id: 2,
        name: 'Ethereum',
        symbol: 'ETH',
        logo: '/assets/ethereum.png',
        price: '6 950',
        variation: '-1.12',
        volume: '85M'
      },
      {
        id: 3,
        name: 'Cardano',
        symbol: 'ADA',
        logo: '/assets/cardano.png',
        price: '1 250',
        variation: '+2.45',
        volume: '45M'
      },
      {
        id: 4,
        name: 'Solana',
        symbol: 'SOL',
        logo: '/assets/stellar.png',
        price: '180',
        variation: '+1.80',
        volume: '32M'
      },
      {
        id: 5,
        name: 'Ripple',
        symbol: 'XRP',
        logo: '/assets/ripple.png',
        price: '2 100',
        variation: '-0.50',
        volume: '28M'
      },
      {
        id: 6,
        name: 'Litecoin',
        symbol: 'LTC',
        logo: '/assets/litecoin.png',
        price: '12 500',
        variation: '+1.25',
        volume: '18M'
      }
    ])

    const filteredCryptos = computed(() => {
      let result = cryptos.value.filter((crypto) => {
        const query = searchQuery.value.toLowerCase()
        return (
          crypto.name.toLowerCase().includes(query) ||
          crypto.symbol.toLowerCase().includes(query)
        )
      })

      // Sort
      result.sort((a, b) => {
        if (sortField.value === 'price') {
          return parseFloat(b.price.replace(/\s/g, '')) - parseFloat(a.price.replace(/\s/g, ''))
        } else if (sortField.value === 'variation') {
          return parseFloat(b.variation) - parseFloat(a.variation)
        } else if (sortField.value === 'volume') {
          const volumeA = parseInt(a.volume.replace('M', ''))
          const volumeB = parseInt(b.volume.replace('M', ''))
          return volumeB - volumeA
        }
        return 0
      })

      return result
    })

    const loadData = async () => {
      try {
        const profileRes = await api.get('/auth/profile')
        if (profileRes.data) {
          userName.value = profileRes.data.name || 'Utilisateur'
        }
      } catch (e) {
        console.error('Error loading profile:', e)
      }
    }

    const logout = async () => {
      try {
        await api.post('/auth/logout')
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        router.push('/login')
      } catch (e) {
        console.error('Logout error:', e)
        router.push('/login')
      }
    }

    const refreshData = () => {
      loadData()
    }

    const sortBy = (field: string) => {
      sortField.value = field
    }

    const viewDetails = (crypto: Crypto) => {
      router.push(`/crypto-detail/${crypto.id}`)
    }

    onMounted(loadData)

    return {
      userName,
      searchQuery,
      cryptos,
      filteredCryptos,
      logout,
      refreshData,
      sortBy,
      viewDetails
    }
  }
})
</script>
