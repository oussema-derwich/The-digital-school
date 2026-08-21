import { ref, computed } from 'vue'
import { getCryptos, getCryptoById, getCryptoHistory } from '@/services/cryptoApi'
import { getWallet, buyCrypto, sellCrypto } from '@/services/walletApi'
import { getTransactions } from '@/services/transactionApi'
import { getAlerts, createAlert, deleteAlert } from '@/services/alertApi'
import { getFavorites, toggleFavorite } from '@/services/favoriteApi'
import { formatApiError } from '@/services/errorHandler'

export interface CryptoState {
  cryptos: any[]
  wallets: any[]
  transactions: any[]
  alerts: any[]
  favorites: any[]
  loading: boolean
  error: string | null
}

export function useCryptoApp() {
  const state = ref<CryptoState>({
    cryptos: [],
    wallets: [],
    transactions: [],
    alerts: [],
    favorites: [],
    loading: false,
    error: null
  })

  // Load all crypto data
  const loadCryptos = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.cryptos = await getCryptos()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load user wallets
  const loadWallets = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.wallets = await getWallet()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load transactions
  const loadTransactions = async (page = 1) => {
    state.value.loading = true
    state.value.error = null
    try {
      const response = await getTransactions(page, 20)
      state.value.transactions = response.transactions || []
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load alerts
  const loadAlerts = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.alerts = await getAlerts()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Load favorites
  const loadFavorites = async () => {
    state.value.loading = true
    state.value.error = null
    try {
      state.value.favorites = await getFavorites()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
    } finally {
      state.value.loading = false
    }
  }

  // Buy crypto
  const handleBuyCrypto = async (cryptoId: number, amount: number) => {
    state.value.loading = true
    state.value.error = null
    try {
      const result = await buyCrypto({ crypto_id: cryptoId, amount })
      await loadWallets()
      await loadTransactions()
      return result
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Sell crypto
  const handleSellCrypto = async (cryptoId: number, quantity: number) => {
    state.value.loading = true
    state.value.error = null
    try {
      const result = await sellCrypto({ crypto_id: cryptoId, quantity })
      await loadWallets()
      await loadTransactions()
      return result
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Create alert
  const handleCreateAlert = async (cryptoId: number, alertType: string, targetValue: number) => {
    state.value.loading = true
    state.value.error = null
    try {
      const result = await createAlert({ crypto_id: cryptoId, alert_type: alertType as any, target_value: targetValue })
      await loadAlerts()
      return result
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Delete alert
  const handleDeleteAlert = async (alertId: number) => {
    state.value.loading = true
    state.value.error = null
    try {
      await deleteAlert(alertId)
      await loadAlerts()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    } finally {
      state.value.loading = false
    }
  }

  // Toggle favorite
  const handleToggleFavorite = async (cryptoId: number) => {
    state.value.error = null
    try {
      await toggleFavorite(cryptoId)
      await loadFavorites()
    } catch (error: any) {
      const apiError = formatApiError(error)
      state.value.error = apiError.message
      throw error
    }
  }

  // Check if crypto is favorite
  const isFavorite = computed(() => {
    return (cryptoId: number) => state.value.favorites.some(f => f.crypto_id === cryptoId)
  })

  // Get crypto by id
  const getCrypto = computed(() => {
    return (id: number) => state.value.cryptos.find(c => c.id === id)
  })

  // Get wallet by crypto id
  const getWalletByCrypto = computed(() => {
    return (cryptoId: number) => state.value.wallets.find(w => w.crypto_id === cryptoId)
  })

  return {
    state: computed(() => state.value),
    loadCryptos,
    loadWallets,
    loadTransactions,
    loadAlerts,
    loadFavorites,
    handleBuyCrypto,
    handleSellCrypto,
    handleCreateAlert,
    handleDeleteAlert,
    handleToggleFavorite,
    isFavorite,
    getCrypto,
    getWalletByCrypto
  }
}
