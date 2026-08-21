import api from './api'

export interface Favorite {
  id: number
  user_id: number
  crypto_id: number
  crypto_symbol?: string
  crypto_name?: string
  created_at?: string
}

export interface FavoriteResponse {
  favorite?: Favorite
  favorites?: Favorite[]
  message?: string
}

// ===== Favorites APIs =====

export async function getFavorites(): Promise<Favorite[]> {
  try {
    const res = await api.get('/favorites')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des favoris' }
  }
}

export async function addFavorite(cryptoId: string | number): Promise<FavoriteResponse> {
  try {
    const res = await api.post(`/favorites/${cryptoId}`, {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'ajout aux favoris' }
  }
}

export async function toggleFavorite(cryptoId: string | number): Promise<FavoriteResponse> {
  try {
    const res = await api.post(`/favorites/${cryptoId}/toggle`, {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors du basculement des favoris' }
  }
}

export async function removeFavorite(cryptoId: string | number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/favorites/${cryptoId}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression des favoris' }
  }
}

export async function isFavorite(cryptoId: string | number): Promise<boolean> {
  try {
    const favorites = await getFavorites()
    return favorites.some(f => f.crypto_id === Number(cryptoId))
  } catch (error) {
    return false
  }
}
