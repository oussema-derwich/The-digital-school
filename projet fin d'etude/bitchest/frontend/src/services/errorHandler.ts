export interface ApiError {
  message: string
  errors?: Record<string, string[]>
  status?: number
}

export interface ApiResponse<T = any> {
  success: boolean
  data?: T
  message?: string
  errors?: Record<string, string[]>
}

/**
 * Format API error for display
 */
export function formatApiError(error: any): ApiError {
  if (error.response?.data) {
    const data = error.response.data
    return {
      message: data.message || 'Une erreur s\'est produite',
      errors: data.errors || {},
      status: error.response.status
    }
  }

  if (error.message) {
    return {
      message: error.message
    }
  }

  return {
    message: 'Une erreur inconnue s\'est produite'
  }
}

/**
 * Get error messages from API response
 */
export function getErrorMessages(error: any): string[] {
  const apiError = formatApiError(error)
  const messages: string[] = [apiError.message]

  if (apiError.errors) {
    Object.values(apiError.errors).forEach((msgs) => {
      if (Array.isArray(msgs)) {
        messages.push(...msgs)
      }
    })
  }

  return messages
}

/**
 * Check if error is authentication error
 */
export function isAuthError(error: any): boolean {
  return error.response?.status === 401 || error.response?.status === 403
}

/**
 * Check if error is validation error
 */
export function isValidationError(error: any): boolean {
  return error.response?.status === 422
}

/**
 * Check if error is server error
 */
export function isServerError(error: any): boolean {
  const status = error.response?.status
  return status ? status >= 500 : false
}

/**
 * Check if error is network error
 */
export function isNetworkError(error: any): boolean {
  return !error.response
}

/**
 * Get field error from validation error
 */
export function getFieldError(error: any, field: string): string {
  const apiError = formatApiError(error)
  if (apiError.errors && apiError.errors[field]) {
    const errors = apiError.errors[field]
    return Array.isArray(errors) ? errors[0] : errors
  }
  return ''
}

/**
 * Retry API call with exponential backoff
 */
export async function retryApiCall<T>(
  fn: () => Promise<T>,
  maxRetries: number = 3,
  delayMs: number = 1000
): Promise<T> {
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await fn()
    } catch (error) {
      if (i === maxRetries - 1) throw error

      // Don't retry on 4xx errors (except 429)
      const status = (error as any).response?.status
      if (status && status >= 400 && status < 500 && status !== 429) {
        throw error
      }

      // Wait before retry
      await new Promise(resolve => setTimeout(resolve, delayMs * Math.pow(2, i)))
    }
  }
  throw new Error('Max retries exceeded')
}

/**
 * Abort API call after timeout
 */
export function withTimeout<T>(
  promise: Promise<T>,
  timeoutMs: number = 30000
): Promise<T> {
  return Promise.race([
    promise,
    new Promise<T>((_, reject) =>
      setTimeout(() => reject(new Error('Request timeout')), timeoutMs)
    )
  ])
}
