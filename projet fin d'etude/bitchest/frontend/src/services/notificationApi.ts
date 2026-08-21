import api from './api'

export interface Notification {
  id: number
  user_id: number
  type: string
  title: string
  message: string
  data?: any
  read_at?: string | null
  created_at?: string
}

export interface NotificationResponse {
  notifications: Notification[]
  pagination?: {
    total: number
    per_page: number
    current_page: number
    last_page: number
  }
}

// ===== Notification APIs =====

export async function getNotifications(page: number = 1, perPage: number = 10): Promise<NotificationResponse> {
  try {
    const res = await api.get('/notifications', {
      params: { page, per_page: perPage }
    })
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la récupération des notifications' }
  }
}

export async function markNotificationAsRead(id: number): Promise<{ message: string }> {
  try {
    const res = await api.put(`/notifications/${id}/read`, {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la marque de la notification' }
  }
}

export async function markAllNotificationsAsRead(): Promise<{ message: string }> {
  try {
    const res = await api.put('/notifications/read-all', {})
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la marque de toutes les notifications' }
  }
}

export async function deleteNotification(id: number): Promise<{ message: string }> {
  try {
    const res = await api.delete(`/notifications/${id}`)
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'Erreur lors de la suppression de la notification' }
  }
}

export async function getUnreadCount(): Promise<number> {
  try {
    const res = await api.get('/notifications', {
      params: { unread_only: true }
    })
    const notifications = res.data.data || res.data.notifications || []
    return notifications.filter((n: Notification) => !n.read_at).length
  } catch (error) {
    return 0
  }
}
