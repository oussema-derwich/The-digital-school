import api from './api'

export interface AdminUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'user'
  is_active: boolean
  balance?: number
  kyc_status?: string
  kycExpiry?: string | null
  phone?: string
  created_at: string
  updated_at: string
}

export interface AdminCrypto {
  id: number
  name: string
  symbol: string
  price: number
  market_cap?: number
  volume_24h?: number
  change_24h?: number
  change_7d?: number
  logo?: string
  description?: string
  is_active: boolean
  created_at?: string
  updated_at?: string
}

export interface AdminTransaction {
  id: number
  type: 'buy' | 'sell'
  user_id: number
  user?: { name: string; email: string }
  crypto_id: number
  crypto?: { name: string; symbol: string }
  quantity: number
  price: number
  total: number
  fee?: number
  status: 'pending' | 'completed' | 'failed'
  created_at: string
  updated_at: string
}

export interface AdminAlert {
  id: number
  user_id: number
  crypto_id: number
  alert_type: 'price_above' | 'price_below' | 'volume_change'
  target_value: number
  status: 'active' | 'triggered' | 'inactive'
  user?: { name: string; email: string }
  crypto?: { name: string; symbol: string }
  created_at: string
  updated_at: string
}

export interface AdminStats {
  total_users: number
  active_users: number
  new_users_today: number
  new_users_this_week: number
  total_transactions: number
  total_transaction_volume: number
  active_alerts: number
  alerts_triggered_today: number
  total_market_value: number
  platform_fees_collected: number
}

export interface AdminSettings {
  platform_name: string
  maintenance_mode: boolean
  max_daily_transaction: number
  default_fee_percentage: number
}

export interface PaginatedResponse<T> {
  data: T[]
  pagination: {
    total: number
    per_page: number
    current_page: number
    last_page: number
    from: number
    to: number
  }
}

export interface Activity {
  id: number
  user_id?: number
  user_name: string
  action: string
  description: string
  type: string
  created_at: string
}

// ===== Admin Dashboard Stats =====

export async function getAdminStats(): Promise<AdminStats> {
  try {
    const res = await api.get('/admin/stats')
    const payload = res.data.data || res.data || {}

    // Map backend camelCase keys to frontend snake_case AdminStats shape
    return {
      total_users: payload.totalUsers ?? payload.total_users ?? 0,
      active_users: payload.activeUsers ?? payload.active_users ?? 0,
      new_users_today: payload.newUsersToday ?? payload.new_users_today ?? 0,
      new_users_this_week: payload.newUsersThisWeek ?? payload.new_users_this_week ?? 0,
      total_transactions: payload.totalTransactions ?? payload.total_transactions ?? 0,
      total_transaction_volume: payload.totalTransactionVolume ?? payload.total_transaction_volume ?? 0,
      active_alerts: payload.totalAlerts ?? payload.active_alerts ?? 0,
      alerts_triggered_today: payload.alertsTriggeredToday ?? payload.alerts_triggered_today ?? 0,
      total_market_value: payload.totalMarketValue ?? payload.total_market_value ?? 0,
      platform_fees_collected: payload.platformFeesCollected ?? payload.platform_fees_collected ?? 0
    }
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des statistiques' }
  }
}

export async function getRecentActivities(limit: number = 10): Promise<Activity[]> {
  try {
    const res = await api.get('/admin/activities', { params: { limit } })
    const items = res.data.data || res.data || []

    // Backend currently returns recent transactions; map them to Activity shape expected by frontend
    return (items as any[]).map((tx: any) => {
      const walletCrypto = tx.walletCrypto || tx.wallet_crypto || null
      const user = (walletCrypto && walletCrypto.wallet && walletCrypto.wallet.user) || tx.user || tx.user_info || null
      const crypto = (walletCrypto && walletCrypto.cryptocurrency) || tx.crypto || null

      return {
        id: tx.id,
        user_id: user?.id ?? null,
        user_name: user?.name ?? user?.email ?? 'N/A',
        action: tx.type ? (tx.type === 'buy' ? 'Achat' : 'Vente') : (tx.action || 'action'),
        description: `Transaction ${tx.id} - ${crypto?.symbol ?? crypto?.name ?? ''} - ${tx.total ?? tx.total_price ?? ''}`,
        type: tx.type ?? 'transaction',
        created_at: tx.created_at
      }
    })
  } catch (error: any) {
    console.error('Error fetching activities:', error)
    return []
  }
}

export async function getTransactionChart(period: number = 7): Promise<any[]> {
  try {
    const res = await api.get('/admin/charts/transactions', { params: { period } })
    const payload = res.data.data || res.data || {}

    // Backend returns grouped data by type: { buy: [{date, count, total}, ...], sell: [...] }
    // Convert to array of { date, value } summing totals across types per date
    const map: Record<string, number> = {}

    if (typeof payload === 'object' && !Array.isArray(payload)) {
      Object.values(payload).forEach((arr: any) => {
        (arr || []).forEach((row: any) => {
          const date = row.date || row.created_at || row[Object.keys(row)[0]]
          const total = Number(row.total ?? row.total_price ?? row.total_transaction ?? 0) || 0
          map[date] = (map[date] || 0) + total
        })
      })
    }

    const result = Object.keys(map).sort().map(date => ({ date, value: map[date] }))
    return result
  } catch (error: any) {
    console.error('Error fetching chart data:', error)
    return []
  }
}

// ===== Admin Users APIs =====

export async function getAdminUsers(
  page: number = 1,
  perPage: number = 10,
  searchQuery?: string,
  status?: string
): Promise<PaginatedResponse<AdminUser>> {
  try {
    const params: any = { page, per_page: perPage }
    if (searchQuery) params.search = searchQuery
    if (status) params.status = status
    const res = await api.get('/admin/users', { params })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des utilisateurs' }
  }
}

export async function getAdminUserById(id: number): Promise<AdminUser> {
  try {
    const res = await api.get(`/admin/users/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de l\'utilisateur' }
  }
}

export async function createAdminUser(payload: {
  name: string
  email: string
  password: string
  role: string
}): Promise<{ user: AdminUser; message?: string }> {
  try {
    const res = await api.post('/admin/users', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la création de l\'utilisateur' }
  }
}

export async function updateAdminUser(id: number, payload: any): Promise<{ user: AdminUser; message?: string }> {
  try {
    const res = await api.put(`/admin/users/${id}`, payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la mise à jour de l\'utilisateur' }
  }
}

export async function deleteAdminUser(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/admin/users/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de l\'utilisateur' }
  }
}



// ===== Admin Cryptos APIs =====

export async function getAdminCryptos(
  page: number = 1,
  perPage: number = 10,
  searchQuery?: string
): Promise<PaginatedResponse<AdminCrypto>> {
  try {
    const params: any = { page, per_page: perPage }
    if (searchQuery) params.search = searchQuery
    const res = await api.get('/admin/cryptos', { params })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des cryptos' }
  }
}

export async function getAdminCryptoById(id: number): Promise<AdminCrypto> {
  try {
    const res = await api.get(`/admin/cryptos/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de la crypto' }
  }
}

export async function createAdminCrypto(payload: any): Promise<{ crypto: AdminCrypto; message?: string }> {
  try {
    const res = await api.post('/admin/cryptos', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la création de la crypto' }
  }
}

export async function deleteAdminCrypto(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/admin/cryptos/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de la crypto' }
  }
}

// (registration requests management functions are defined later as
// getAdminRegistrationRequests / approveRegistrationRequest / rejectRegistrationRequest)

// ===== Admin Transactions APIs =====

export async function getAdminTransactions(
  page: number = 1,
  perPage: number = 10,
  filterType?: string,
  filterCrypto?: string,
  filterUser?: string,
  filterStatus?: string
): Promise<PaginatedResponse<AdminTransaction>> {
  try {
    const params: any = { page, per_page: perPage }
    if (filterType) params.type = filterType
    // Backend expects user_id and cryptocurrency_id
    if (filterCrypto) params.cryptocurrency_id = filterCrypto
    if (filterUser) params.user_id = filterUser
    if (filterStatus) params.status = filterStatus

    const res = await api.get('/admin/transactions', { params })
    const payload = res.data || {}

    // Map transactions returned by backend to AdminTransaction shape expected by frontend
    const data = (payload.data || payload || []).map((tx: any) => {
      const walletCrypto = tx.walletCrypto || tx.wallet_crypto || null
      const user = (walletCrypto && walletCrypto.wallet && walletCrypto.wallet.user) || tx.user || null
      const crypto = (walletCrypto && walletCrypto.cryptocurrency) || tx.crypto || null

      return {
        id: tx.id,
        type: tx.type,
        user_id: user?.id ?? tx.user_id ?? null,
        user: user ? { name: user.name, email: user.email } : undefined,
        crypto_id: crypto?.id ?? tx.crypto_id ?? tx.cryptocurrency_id ?? null,
        crypto: crypto ? { name: crypto.name, symbol: crypto.symbol, image: crypto.image ?? crypto.logo_url ?? crypto.logo } : undefined,
        quantity: Number(tx.quantity ?? tx.amount ?? 0),
        price: Number(tx.unit_price ?? tx.price ?? tx.price_per_unit ?? 0),
        total: Number(tx.total_price ?? tx.total ?? 0),
        fee: Number(tx.fee ?? 0),
        status: tx.status ?? 'pending',
        created_at: tx.created_at,
        updated_at: tx.updated_at
      }
    })

    const pagination = payload.pagination || payload.pagination || payload || null
    return { data, pagination }
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des transactions' }
  }
}

export async function getAdminTransactionById(id: number): Promise<AdminTransaction> {
  try {
    const res = await api.get(`/admin/transactions/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de la transaction' }
  }
}



// ===== Admin Alerts APIs =====

export async function getAdminAlerts(
  page: number = 1,
  perPage: number = 10,
  filterStatus?: string,
  filterUser?: string,
  filterCrypto?: string
): Promise<PaginatedResponse<AdminAlert>> {
  try {
    const params: any = { page, per_page: perPage }
    if (filterStatus) params.status = filterStatus
    if (filterUser) params.user_id = filterUser
    if (filterCrypto) params.cryptocurrency_id = filterCrypto
    const res = await api.get('/admin/alerts', { params })

    const payload = res.data || {}

    // Normalize items to AdminAlert shape
    const items = (payload.data || payload || []) as any[]
    const data = items.map(it => {
      // Try to extract user and crypto from several possible shapes
      const user = it.user || (it.wallet && it.wallet.user) || (it.user_info) || null
      const crypto = it.crypto || it.cryptocurrency || (it.wallet_crypto && it.wallet_crypto.cryptocurrency) || null

      return {
        id: it.id,
        user_id: it.user_id ?? user?.id ?? null,
        user: user ? { name: user.name, email: user.email } : undefined,
        crypto_id: it.crypto_id ?? it.cryptocurrency_id ?? crypto?.id ?? null,
        crypto: crypto ? { name: crypto.name, symbol: crypto.symbol } : undefined,
        alert_type: it.alert_type ?? it.type ?? it.direction ?? null,
        target_value: Number(it.target_value ?? it.threshold ?? it.value ?? 0),
        status: it.status ?? it.state ?? 'active',
        created_at: it.created_at,
        updated_at: it.updated_at
      }
    })

    const pagination = payload.pagination || payload.pagination || null
    return { data, pagination }
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des alertes' }
  }
}

export async function getAdminAlertById(id: number): Promise<AdminAlert> {
  try {
    const res = await api.get(`/admin/alerts/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de l\'alerte' }
  }
}

export async function deleteAdminAlert(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/admin/alerts/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de l\'alerte' }
  }
}

export async function resumeAdminAlert(id: number): Promise<{ message: string }> {
  try {
    const res = await api.put(`/admin/alerts/${id}/resume`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la reprise de l\'alerte' }
  }
}

// ===== Admin Settings =====

export async function getAdminSettings(): Promise<AdminSettings> {
  try {
    const res = await api.get('/admin/settings')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des paramètres' }
  }
}

export async function updateAdminSettings(payload: Partial<AdminSettings>): Promise<{ settings: AdminSettings; message?: string }> {
  try {
    const res = await api.put('/admin/settings', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la mise à jour des paramètres' }
  }
}

// ===== Registration Requests Management =====

export async function getAdminRegistrationRequests(
  page: number = 1,
  perPage: number = 10,
  filterStatus?: string
): Promise<PaginatedResponse<any>> {
  try {
    const params: any = { page, per_page: perPage }
    if (filterStatus) params.status = filterStatus
    const res = await api.get('/admin/registration-requests', { params })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des demandes' }
  }
}

export async function approveRegistrationRequest(id: number): Promise<{ message: string }> {
  try {
    const res = await api.post(`/admin/registration-requests/${id}/approve`, {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de l\'approbation' }
  }
}

export async function rejectRegistrationRequest(id: number, reason?: string): Promise<{ message: string }> {
  try {
    const res = await api.post(`/admin/registration-requests/${id}/reject`, { reason })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors du rejet' }
  }
}

export async function getRegistrationRequestDetails(id: number): Promise<{ status: string; data: any }> {
  try {
    const res = await api.get(`/admin/registration-requests/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des détails' }
  }
}

export async function updateRegistrationRequest(id: number, data: { name?: string; email?: string; role?: string }): Promise<{ status: string; data: any }> {
  try {
    const res = await api.put(`/admin/registration-requests/${id}`, data)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la mise à jour' }
  }
}

export async function deleteRegistrationRequest(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/admin/registration-requests/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression' }
  }
}