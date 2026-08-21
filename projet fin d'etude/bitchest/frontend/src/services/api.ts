import axios from 'axios'
import type { AxiosInstance, AxiosError } from 'axios'

// Determine base URL based on environment
const baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const api: AxiosInstance = axios.create({
  baseURL,
  headers: {
    'Content-Type': 'application/json'
  },
  timeout: 30000 // 30 seconds timeout
})

// Request interceptor - Attach token if present
api.interceptors.request.use(
  (config: any) => {
    const token = localStorage.getItem('token')
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`
    }
    // Add timestamp for cache busting if needed
    config.headers['X-Requested-With'] = 'XMLHttpRequest'
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - Handle errors globally
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error: AxiosError) => {
    // Handle authentication errors (401)
    if (error.response?.status === 401) {
      // Clear auth from localStorage
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('notifications_unread')

      // Redirect to login unless already there
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }

    // Handle server errors (5xx)
    if (error.response?.status && error.response.status >= 500) {
      const payload = error.response?.data || { message: error.message }
      try {
        localStorage.setItem('lastServerError', JSON.stringify(payload))
      } catch (e) {
        // Ignore storage errors
      }
      try {
        (window as any).__lastServerError = payload
      } catch (e) {
        // Ignore window errors
      }
      console.error('Server 5xx error:', payload)
    }

    // Handle forbidden errors (403)
    if (error.response?.status === 403) {
      console.warn('Access forbidden:', error.response?.data)
    }

    // Handle network errors
    if (!error.response) {
      console.error('Network error:', error.message)
    }

    return Promise.reject(error)
  }
)

export default api

