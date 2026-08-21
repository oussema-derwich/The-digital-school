import api from './api'

export interface RegistrationRequest {
  id: number
  name: string
  email: string
  phone?: string
  reason?: string
  status: 'pending' | 'approved' | 'rejected'
  created_at?: string
  updated_at?: string
}

export interface RegistrationResponse {
  request: RegistrationRequest
  message?: string
}

// ===== Registration Request APIs =====

export async function createRegistrationRequest(payload: {
  name: string
  email: string
  role?: string
}): Promise<RegistrationResponse> {
  try {
    const res = await api.post('/registration/request', payload)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la création de la demande' }
  }
}

export async function getRegistrationRequestStatus(id: string | number): Promise<RegistrationResponse> {
  try {
    const res = await api.get(`/registration/status/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération du statut' }
  }
}
