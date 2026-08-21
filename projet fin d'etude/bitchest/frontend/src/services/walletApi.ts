import api from './api'

export interface Wallet {
  id: number
  user_id: number
  crypto_id: number
  crypto_symbol?: string
  crypto_name?: string
  quantity: number
  average_price: number
  current_value?: number
  created_at?: string
  updated_at?: string
}

export interface BuyPayload {
  crypto_id: string | number
  amount: number
  currency?: string
}

export interface SellPayload {
  crypto_id: string | number
  quantity: number
}

export interface WalletResponse {
  wallet: Wallet
  message?: string
}

// ===== Wallet APIs =====

export async function getWallet(): Promise<Wallet[]> {
  try {
    const res = await api.get('/wallet')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération du portefeuille' }
  }
}

export async function buyCrypto(payload: BuyPayload): Promise<WalletResponse> {
  try {
    const res = await api.post('/buy', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'achat' }
  }
}

export async function sellCrypto(payload: SellPayload): Promise<WalletResponse> {
  try {
    const res = await api.post('/sell', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la vente' }
  }
}

export async function getWalletByCryptoId(cryptoId: string | number): Promise<Wallet> {
  try {
    const wallets = await getWallet()
    const wallet = wallets.find(w => w.crypto_id === Number(cryptoId))
    if (!wallet) throw new Error('Portefeuille non trouvé pour cette crypto')
    return wallet
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération du portefeuille' }
  }
}
