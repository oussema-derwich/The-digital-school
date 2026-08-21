import api from './api'

export interface Alert {
  id: number
  user_id: number
  crypto_id: number
  crypto_symbol?: string
  crypto_name?: string
  alert_type: 'price_above' | 'price_below' | 'volume_change'
  target_value: number
  current_value?: number
  status: 'active' | 'triggered' | 'inactive'
  created_at?: string
  updated_at?: string
}

export interface CreateAlertPayload {
  crypto_id: string | number
  alert_type: 'price_above' | 'price_below' | 'volume_change'
  target_value: number
}

export interface UpdateAlertPayload {
  alert_type?: string
  target_value?: number
  status?: string
}

export interface AlertResponse {
  alert: Alert
  message?: string
}

// ===== Alert APIs =====

export async function getAlerts(): Promise<Alert[]> {
  try {
    const res = await api.get('/alerts')
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des alertes' }
  }
}

export async function getAlertById(id: number): Promise<Alert> {
  try {
    const res = await api.get(`/alerts/${id}`)
    return res.data.data || res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération de l\'alerte' }
  }
}

export async function createAlert(payload: CreateAlertPayload): Promise<AlertResponse> {
  try {
    const res = await api.post('/alerts', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la création de l\'alerte' }
  }
}

export async function updateAlert(id: number, payload: UpdateAlertPayload): Promise<AlertResponse> {
  try {
    const res = await api.put(`/alerts/${id}`, payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la mise à jour de l\'alerte' }
  }
}

export async function deleteAlert(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/alerts/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de l\'alerte' }
  }
}

export async function resumeAlert(id: number): Promise<AlertResponse> {
  try {
    const res = await api.post(`/alerts/${id}/resume`, {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la réactivation de l\'alerte' }
  }
}
