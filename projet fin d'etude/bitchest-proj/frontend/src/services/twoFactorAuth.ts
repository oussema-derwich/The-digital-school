import api from './api'

export default {
  enable2FA() {
    return api.post('/auth/2fa/enable')
  },

  confirm2FA(code: string) {
    return api.post('/auth/2fa/confirm', { code })
  },

  verify2FA(code: string) {
    return api.post('/auth/2fa/verify', { code })
  },

  disable2FA() {
    return api.post('/auth/2fa/disable')
  }
}