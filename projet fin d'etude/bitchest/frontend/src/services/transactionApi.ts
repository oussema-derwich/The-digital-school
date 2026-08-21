import api from './api'

export interface Transaction {
  id: number
  user_id: number
  crypto_id: number
  crypto_symbol?: string
  crypto_name?: string
  type: 'buy' | 'sell'
  quantity: number
  price: number
  total: number
  fee: number
  status: 'pending' | 'completed' | 'failed'
  created_at?: string
  updated_at?: string
  proof?: string
}

export interface TransactionResponse {
  transactions: Transaction[]
  pagination?: {
    total: number
    per_page: number
    current_page: number
    last_page: number
  }
}

export interface ExportResponse {
  url: string
  filename: string
}

// ===== Transaction APIs =====

export async function getTransactions(page: number = 1, perPage: number = 10): Promise<TransactionResponse> {
  try {
    const res = await api.get('/transactions', {
      params: { page, per_page: perPage }
    })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des transactions' }
  }
}

export async function getTransactionById(id: number): Promise<Transaction> {
  try {
    const res = await api.get(`/transactions/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de la transaction' }
  }
}

export async function getTransactionProof(id: number): Promise<{ proof: string; url: string }> {
  try {
    const res = await api.get(`/transactions/${id}/proof`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de la preuve' }
  }
}

export async function exportTransactionsCSV(): Promise<ExportResponse> {
  try {
    const res = await api.get('/transactions/export/csv')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'export CSV' }
  }
}

export async function exportTransactionsPDF(): Promise<ExportResponse> {
  try {
    const res = await api.get('/transactions/export/pdf')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'export PDF' }
  }
}

export async function downloadTransactionProof(id: number): Promise<Blob> {
  try {
    const res = await api.get(`/transactions/${id}/proof`, {
      responseType: 'blob'
    })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors du téléchargement de la preuve' }
  }
}
