import api from './api'

export interface Crypto {
  id: string | number
  name: string
  symbol: string
  price: number
  market_cap?: number
  volume_24h?: number
  change_24h?: number
  change_7d?: number
  trend?: string
  description?: string
  logo?: string
}

export interface CryptoHistory {
  date: string
  o: number
  h: number
  l: number
  c: number
}

// ===== Cryptocurrencies APIs =====

export async function getCryptos(): Promise<Crypto[]> {
  try {
    const res = await api.get('/cryptos')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des cryptos' }
  }
}

export async function getCryptoById(id: string | number): Promise<Crypto> {
  try {
    const res = await api.get(`/cryptos/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de la crypto' }
  }
}

export async function getCryptoHistory(id: string | number, days: number = 365): Promise<CryptoHistory[]> {
  try {
    const res = await api.get(`/cryptos/${id}/history/${days}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de l\'historique' }
  }
}

export async function getMarketData(): Promise<{ cryptos: Crypto[] }> {
  try {
    const res = await api.get('/cryptos/market')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des données de marché' }
  }
}

export async function getCryptoCount(): Promise<{ count: number }> {
  try {
    const res = await api.get('/cryptos/count')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors du comptage des cryptos' }
  }
}
