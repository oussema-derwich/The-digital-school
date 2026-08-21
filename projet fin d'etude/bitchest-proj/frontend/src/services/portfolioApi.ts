import api from './api'

export interface Portfolio {
  total_value: number
  total_invested: number
  total_gain: number
  total_gain_percentage: number
  currency: string
  updated_at: string
}

export interface PortfolioHistoryItem {
  date: string
  value: number
  invested: number
  gain: number
}

export interface PortfolioResponse {
  portfolio: Portfolio
  assets?: Array<{
    name: string
    value: number
    percentage: number
    color: string
  }>
}

// ===== Portfolio APIs =====

export async function getPortfolioSummary(): Promise<PortfolioResponse> {
  try {
    const res = await api.get('/portfolio')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération du portefeuille' }
  }
}

export async function getPortfolioHistory(): Promise<PortfolioHistoryItem[]> {
  try {
    const res = await api.get('/portfolio/history')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de l\'historique' }
  }
}

export async function getPortfolioAssets(): Promise<Array<{ name: string; value: number; percentage: number; color: string }>> {
  try {
    const res = await api.get('/portfolio')
    return res.data.assets || []
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des actifs' }
  }
}
