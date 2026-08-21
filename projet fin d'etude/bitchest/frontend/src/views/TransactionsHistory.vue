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
        <h2 class="text-3xl font-bold text-secondary mb-8">Historique des Transactions</h2>

        <!-- Filters and Actions -->
        <div class="flex justify-between items-center mb-6 gap-4">
          <div class="flex gap-4 flex-1">
            <select
              v-model="filterCrypto"
              class="px-4 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium focus:border-primary focus:outline-none transition"
            >
              <option value="">Toutes les cryptos</option>
              <option v-for="crypto in availableCryptos" :key="crypto" :value="crypto">{{ crypto }}</option>
            </select>

            <select
              v-model="filterType"
              class="px-4 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium focus:border-primary focus:outline-none transition"
            >
              <option value="">Tous les types</option>
              <option value="Achat">Achat</option>
              <option value="Vente">Vente</option>
            </select>

            <input
              v-model="filterDate"
              type="date"
              class="px-4 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium focus:border-primary focus:outline-none transition"
            />
          </div>

          <div class="flex gap-2">
            <button
              @click="exportPDF"
              class="px-4 py-2 bg-accent text-white rounded-lg font-medium hover:opacity-90 transition"
            >
              📥 PDF
            </button>
            <button
              @click="exportExcel"
              class="px-4 py-2 bg-success text-white rounded-lg font-medium hover:opacity-90 transition"
            >
              📊 Excel
            </button>
            <button
              @click="refreshData"
              class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition"
            >
              🔄 Actualiser
            </button>
          </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow-card p-6 mb-8">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Date</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Type</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Crypto</th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700">Quantité</th>
                  <th class="text-right py-3 px-4 font-bold text-gray-700">Montant</th>
                  <th class="text-left py-3 px-4 font-bold text-gray-700">Statut</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tx in filteredTransactions" :key="tx.id" class="border-b border-gray-100 hover:bg-gray-50 transition">
                  <td class="py-4 px-4 text-gray-700">{{ tx.date }}</td>
                  <td
                    :class="[
                      'py-4 px-4 font-bold',
                      tx.type === 'Achat' ? 'text-success' : 'text-danger'
                    ]"
                  >
                    {{ tx.type }}
                  </td>
                  <td class="py-4 px-4 font-semibold text-gray-800">{{ tx.crypto }}</td>
                  <td class="text-right py-4 px-4 text-gray-700">{{ tx.quantity }}</td>
                  <td class="text-right py-4 px-4 font-semibold text-gray-800">{{ tx.amount }} DT</td>
                  <td class="py-4 px-4">
                    <span
                      :class="[
                        'px-3 py-1 rounded-lg text-sm font-bold',
                        tx.status === 'Confirmé'
                          ? 'bg-success/20 text-success'
                          : tx.status === 'Annulé'
                            ? 'bg-danger/20 text-danger'
                            : 'bg-accent/20 text-accent'
                      ]"
                    >
                      {{ tx.status }}
                    </span>
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

interface Transaction {
  id: number
  date: string
  type: string
  crypto: string
  quantity: string
  amount: string
  status: string
}

export default defineComponent({
  components: { ClientSidebar },
  setup() {
    const router = useRouter()
    const userName = ref('Utilisateur')
    const filterCrypto = ref('')
    const filterType = ref('')
    const filterDate = ref('')
    const availableCryptos = ref<string[]>([])

    const transactions = ref<Transaction[]>([])

    const filteredTransactions = computed(() => {
      return transactions.value.filter((tx) => {
        if (filterCrypto.value && tx.crypto !== filterCrypto.value) return false
        if (filterType.value && tx.type !== filterType.value) return false
        if (filterDate.value) {
          // Format date for comparison
          const txDate = new Date(tx.date).toISOString().split('T')[0]
          if (txDate !== filterDate.value) return false
        }
        return true
      })
    })

    const loadData = async () => {
      try {
        // Load profile
        const profileRes = await api.get('/auth/profile')
        if (profileRes.data) {
          userName.value = profileRes.data.name || 'Utilisateur'
        }

        // Load transactions
        const transRes = await api.get('/transactions')
        if (transRes.data && transRes.data.data) {
          const apiTransactions = transRes.data.data
          transactions.value = apiTransactions.map((tx: any) => ({
            id: tx.id,
            date: tx.date,
            type: tx.transaction.type,
            crypto: tx.crypto.symbol,
            quantity: tx.transaction.quantite.toString(),
            amount: tx.transaction.montant_eur.toFixed(2).replace('.', ','),
            status: 'Confirmé'
          }))
          
          // Extract unique cryptos for filter
          availableCryptos.value = [...new Set(transactions.value.map(t => t.crypto))]
        }
      } catch (e) {
        console.error('Error loading data:', e)
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

    const exportPDF = () => {
      try {
        // Créer un contenu CSV pour le PDF
        let csvContent = 'Date,Type,Crypto,Quantité,Montant,Statut\n'
        filteredTransactions.value.forEach(tx => {
          csvContent += `${tx.date},${tx.type},${tx.crypto},${tx.quantity},${tx.amount},${tx.status}\n`
        })

        // Créer un blob et télécharger
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const link = document.createElement('a')
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', `transactions-${new Date().toISOString().split('T')[0]}.csv`)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      } catch (e) {
        console.error('Erreur lors de l\'export PDF:', e)
        alert('Erreur lors de l\'export PDF')
      }
    }

    const exportExcel = () => {
      try {
        // Créer le contenu Excel (TSV pour compatibilité)
        let tsvContent = 'Date\tType\tCrypto\tQuantité\tMontant\tStatut\n'
        filteredTransactions.value.forEach(tx => {
          tsvContent += `${tx.date}\t${tx.type}\t${tx.crypto}\t${tx.quantity}\t${tx.amount}\t${tx.status}\n`
        })

        // Créer un blob et télécharger
        const blob = new Blob([tsvContent], { type: 'application/vnd.ms-excel;charset=utf-8;' })
        const link = document.createElement('a')
        const url = URL.createObjectURL(blob)
        link.setAttribute('href', url)
        link.setAttribute('download', `transactions-${new Date().toISOString().split('T')[0]}.xls`)
        link.style.visibility = 'hidden'
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      } catch (e) {
        console.error('Erreur lors de l\'export Excel:', e)
        alert('Erreur lors de l\'export Excel')
      }
    }

    onMounted(loadData)

    return {
      userName,
      filterCrypto,
      filterType,
      filterDate,
      availableCryptos,
      transactions,
      filteredTransactions,
      logout,
      refreshData,
      exportPDF,
      exportExcel
    }
  }
})
</script>
