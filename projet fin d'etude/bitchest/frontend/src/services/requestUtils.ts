import api from './api'
import type { AxiosRequestConfig } from 'axios'

/**
 * Health check - Test API connectivity
 */
export async function checkHealth(): Promise<{ status: string; database: string }> {
  try {
    const res = await api.get('/health')
    return res.data
  } catch (error: any) {
    throw error.response?.data || { message: 'API health check failed' }
  }
}

/**
 * Generic GET request
 */
export async function get<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
  try {
    const res = await api.get<T>(url, config)
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Generic POST request
 */
export async function post<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
  try {
    const res = await api.post<T>(url, data, config)
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Generic PUT request
 */
export async function put<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
  try {
    const res = await api.put<T>(url, data, config)
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Generic PATCH request
 */
export async function patch<T = any>(url: string, data?: any, config?: AxiosRequestConfig): Promise<T> {
  try {
    const res = await api.patch<T>(url, data, config)
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Generic DELETE request
 */
export async function deleteRequest<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
  try {
    const res = await api.delete<T>(url, config)
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Upload file
 */
export async function uploadFile(url: string, file: File, additionalData?: Record<string, any>): Promise<any> {
  try {
    const formData = new FormData()
    formData.append('file', file)

    if (additionalData) {
      Object.entries(additionalData).forEach(([key, value]) => {
        formData.append(key, String(value))
      })
    }

    const res = await api.post(url, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return res.data
  } catch (error) {
    throw error
  }
}

/**
 * Download file
 */
export async function downloadFile(url: string, filename: string): Promise<void> {
  try {
    const res = await api.get(url, {
      responseType: 'blob'
    })

    const blob = new Blob([res.data], { type: res.data.type })
    const link = document.createElement('a')
    link.href = window.URL.createObjectURL(blob)
    link.download = filename
    link.click()
    window.URL.revokeObjectURL(link.href)
  } catch (error) {
    throw error
  }
}

/**
 * Batch API calls
 */
export async function batch<T = any>(requests: Array<() => Promise<any>>): Promise<T[]> {
  try {
    return await Promise.all(requests.map(req => req()))
  } catch (error) {
    throw error
  }
}
