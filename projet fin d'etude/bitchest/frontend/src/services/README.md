# API Services Documentation

This directory contains all the API service modules for the BitChest cryptocurrency trading platform.

## File Structure

```
services/
├── api.ts                  # Axios instance with interceptors
├── auth.ts                 # Authentication APIs
├── authGuard.ts           # Route guard for authentication
├── cryptoApi.ts           # Cryptocurrency APIs
├── walletApi.ts           # Wallet/Portfolio APIs
├── transactionApi.ts      # Transaction APIs
├── portfolioApi.ts        # Portfolio summary and history
├── alertApi.ts            # Alert management APIs
├── favoriteApi.ts         # Favorites management APIs
├── notificationApi.ts     # Notification APIs
├── registrationApi.ts     # Registration request APIs
├── adminApi.ts            # Admin panel APIs
├── twoFactorAuth.ts       # 2FA APIs
├── realtime.ts            # Real-time updates via Laravel Echo
├── errorHandler.ts        # Error handling utilities
├── requestUtils.ts        # Generic request utilities
└── index.ts               # Central export file
```

## API Modules

### Authentication (`auth.ts`)

User login, registration, profile management, and authentication state.

```typescript
import { login, register, logout, fetchUserProfile, updateProfile } from '@/services/auth'

// Login
const response = await login('user@email.com', 'password')

// Register
const response = await register({
  name: 'John Doe',
  email: 'john@email.com',
  password: 'password',
  password_confirmation: 'password'
})

// Logout
await logout()

// Get current user profile
const user = await fetchUserProfile()

// Update profile
const updated = await updateProfile({
  name: 'Jane Doe',
  email: 'jane@email.com',
  phone: '+1234567890'
})

// Upload avatar
const user = await uploadAvatar(fileObject)

// Delete avatar
await deleteAvatar()
```

**Exports:**
- `login(email, password)` - Login user
- `register(payload)` - Register new user
- `logout()` - Logout user
- `fetchUserProfile()` - Get current user profile
- `updateProfile(payload)` - Update user profile
- `uploadAvatar(file)` - Upload user avatar
- `deleteAvatar()` - Delete user avatar
- `currentUser` - Reactive user object
- `isAuthenticated` - Reactive auth state
- `token` - Reactive auth token

### Cryptocurrencies (`cryptoApi.ts`)

Fetch cryptocurrency data, prices, and history.

```typescript
import { getCryptos, getCryptoById, getCryptoHistory, getMarketData } from '@/services/cryptoApi'

// Get all cryptos
const cryptos = await getCryptos()

// Get single crypto
const crypto = await getCryptoById(1)

// Get crypto history (default 365 days)
const history = await getCryptoHistory(1, 30) // Last 30 days

// Get market data
const market = await getMarketData()

// Get crypto count
const count = await getCryptoCount()
```

**Exports:**
- `getCryptos()` - Get all cryptocurrencies
- `getCryptoById(id)` - Get single crypto
- `getCryptoHistory(id, days)` - Get price history
- `getMarketData()` - Get market overview
- `getCryptoCount()` - Get total crypto count

### Wallet (`walletApi.ts`)

Manage user wallet and holdings.

```typescript
import { getWallet, buyCrypto, sellCrypto } from '@/services/walletApi'

// Get wallet
const wallets = await getWallet()

// Buy crypto
const result = await buyCrypto({
  crypto_id: 1,
  amount: 100,
  currency: 'EUR'
})

// Sell crypto
const result = await sellCrypto({
  crypto_id: 1,
  quantity: 0.5
})

// Get wallet for specific crypto
const wallet = await getWalletByCryptoId(1)
```

**Exports:**
- `getWallet()` - Get all wallets
- `buyCrypto(payload)` - Buy cryptocurrency
- `sellCrypto(payload)` - Sell cryptocurrency
- `getWalletByCryptoId(id)` - Get wallet for specific crypto

### Transactions (`transactionApi.ts`)

View and manage transactions.

```typescript
import { 
  getTransactions, 
  getTransactionById, 
  exportTransactionsCSV, 
  exportTransactionsPDF 
} from '@/services/transactionApi'

// Get transactions with pagination
const result = await getTransactions(1, 20)

// Get single transaction
const transaction = await getTransactionById(123)

// Get transaction proof
const proof = await getTransactionProof(123)

// Export as CSV
const csvData = await exportTransactionsCSV()

// Export as PDF
const pdfData = await exportTransactionsPDF()

// Download proof file
const blob = await downloadTransactionProof(123)
```

**Exports:**
- `getTransactions(page, perPage)` - Get transactions list
- `getTransactionById(id)` - Get single transaction
- `getTransactionProof(id)` - Get transaction proof
- `exportTransactionsCSV()` - Export to CSV
- `exportTransactionsPDF()` - Export to PDF
- `downloadTransactionProof(id)` - Download proof file

### Portfolio (`portfolioApi.ts`)

View portfolio summary and history.

```typescript
import { 
  getPortfolioSummary, 
  getPortfolioHistory,
  getPortfolioAssets 
} from '@/services/portfolioApi'

// Get portfolio summary
const portfolio = await getPortfolioSummary()

// Get portfolio history
const history = await getPortfolioHistory()

// Get portfolio assets breakdown
const assets = await getPortfolioAssets()
```

**Exports:**
- `getPortfolioSummary()` - Get portfolio overview
- `getPortfolioHistory()` - Get portfolio value history
- `getPortfolioAssets()` - Get asset allocation

### Alerts (`alertApi.ts`)

Manage price and volume alerts.

```typescript
import { 
  getAlerts, 
  createAlert, 
  updateAlert, 
  deleteAlert 
} from '@/services/alertApi'

// Get all alerts
const alerts = await getAlerts()

// Create alert
const alert = await createAlert({
  crypto_id: 1,
  alert_type: 'price_above',
  target_value: 50000
})

// Update alert
const updated = await updateAlert(1, {
  target_value: 55000,
  status: 'active'
})

// Delete alert
await deleteAlert(1)

// Resume alert
const resumed = await resumeAlert(1)
```

**Exports:**
- `getAlerts()` - Get all alerts
- `getAlertById(id)` - Get single alert
- `createAlert(payload)` - Create new alert
- `updateAlert(id, payload)` - Update alert
- `deleteAlert(id)` - Delete alert
- `resumeAlert(id)` - Resume triggered alert

### Favorites (`favoriteApi.ts`)

Manage favorite cryptocurrencies.

```typescript
import { 
  getFavorites, 
  addFavorite, 
  removeFavorite, 
  toggleFavorite 
} from '@/services/favoriteApi'

// Get all favorites
const favorites = await getFavorites()

// Add favorite
const fav = await addFavorite(1)

// Remove favorite
await removeFavorite(1)

// Toggle favorite
const fav = await toggleFavorite(1)

// Check if crypto is favorite
const isFav = await isFavorite(1)
```

**Exports:**
- `getFavorites()` - Get all favorites
- `addFavorite(cryptoId)` - Add to favorites
- `removeFavorite(cryptoId)` - Remove from favorites
- `toggleFavorite(cryptoId)` - Toggle favorite status
- `isFavorite(cryptoId)` - Check if favorite

### Notifications (`notificationApi.ts`)

Manage user notifications.

```typescript
import { 
  getNotifications, 
  markNotificationAsRead, 
  markAllNotificationsAsRead 
} from '@/services/notificationApi'

// Get notifications
const result = await getNotifications(1, 10)

// Mark as read
await markNotificationAsRead(1)

// Mark all as read
await markAllNotificationsAsRead()

// Delete notification
await deleteNotification(1)

// Get unread count
const count = await getUnreadCount()
```

**Exports:**
- `getNotifications(page, perPage)` - Get notifications
- `markNotificationAsRead(id)` - Mark as read
- `markAllNotificationsAsRead()` - Mark all as read
- `deleteNotification(id)` - Delete notification
- `getUnreadCount()` - Get unread count

### Admin (`adminApi.ts`)

Administrative functions and management.

```typescript
import { 
  getAdminUsers, 
  getAdminCryptos,
  getAdminTransactions,
  getAdminStats,
  getAdminSettings,
  updateAdminSettings
} from '@/services/adminApi'

// User management
const users = await getAdminUsers(1, 20)
await createAdminUser({ name, email, password, role })
await updateAdminUser(1, { name, role })
await deleteAdminUser(1)

// Crypto management
const cryptos = await getAdminCryptos(1, 20)
await createAdminCrypto({ name, symbol, price })
await updateAdminCrypto(1, { price, market_cap })
await deleteAdminCrypto(1)

// Transactions management
const transactions = await getAdminTransactions(1, 20)

// Alerts management
const alerts = await getAdminAlerts(1, 20)
await resumeAdminAlert(1)

// Settings management
const settings = await getAdminSettings()
await updateAdminSettings({ transaction_fee: 2.5 })

// Statistics
const stats = await getAdminStats()

// Registration requests
const requests = await getAdminRegistrationRequests(1, 20)
await approveRegistrationRequest(1)
await rejectRegistrationRequest(1, 'Reason for rejection')
```

**Exports:**
- User management: `getAdminUsers`, `createAdminUser`, `updateAdminUser`, `deleteAdminUser`
- Crypto management: `getAdminCryptos`, `createAdminCrypto`, `updateAdminCrypto`, `deleteAdminCrypto`
- Transaction management: `getAdminTransactions`
- Alert management: `getAdminAlerts`, `resumeAdminAlert`
- Settings: `getAdminSettings`, `updateAdminSettings`
- Statistics: `getAdminStats`
- Registration: `getAdminRegistrationRequests`, `approveRegistrationRequest`, `rejectRegistrationRequest`

### Two-Factor Authentication (`twoFactorAuth.ts`)

Manage 2FA settings.

```typescript
import twoFactorAuth from '@/services/twoFactorAuth'

// Enable 2FA
const { qrCode, secret } = await twoFactorAuth.enable2FA()

// Confirm 2FA
await twoFactorAuth.confirm2FA('123456')

// Verify 2FA code
await twoFactorAuth.verify2FA('123456')

// Disable 2FA
await twoFactorAuth.disable2FA()
```

### Error Handler (`errorHandler.ts`)

Error handling and formatting utilities.

```typescript
import { 
  formatApiError, 
  getErrorMessages, 
  isAuthError,
  getFieldError,
  retryApiCall 
} from '@/services/errorHandler'

// Format error
const error = formatApiError(apiError)

// Get all error messages
const messages = getErrorMessages(apiError)

// Check error type
if (isAuthError(error)) { /* handle auth error */ }
if (isValidationError(error)) { /* handle validation */ }
if (isServerError(error)) { /* handle server error */ }

// Get specific field error
const emailError = getFieldError(apiError, 'email')

// Retry with exponential backoff
const result = await retryApiCall(() => fetchData(), 3, 1000)
```

### Request Utils (`requestUtils.ts`)

Generic request utilities.

```typescript
import { 
  checkHealth, 
  get, 
  post, 
  uploadFile, 
  downloadFile,
  batch 
} from '@/services/requestUtils'

// Health check
const health = await checkHealth()

// Generic requests
const data = await get('/endpoint')
const result = await post('/endpoint', { data })
const updated = await put('/endpoint/1', { data })
const deleted = await deleteRequest('/endpoint/1')

// File operations
const uploaded = await uploadFile('/upload', file, { name: 'File' })
await downloadFile('/file/download', 'filename.txt')

// Batch requests
const [users, cryptos] = await batch([
  () => getUsers(),
  () => getCryptos()
])
```

## Auth Guard Setup

Setup authentication guard in your router:

```typescript
import { createRouter, createWebHistory } from 'vue-router'
import { setupAuthGuard } from '@/services/authGuard'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // Your routes...
  ]
})

// Setup authentication guard
setupAuthGuard(router)

export default router
```

## Usage Examples

### Login Flow

```typescript
import { login, currentUser, isAuthenticated } from '@/services/auth'

try {
  const response = await login(email, password)
  console.log('Logged in as:', currentUser.value?.name)
  router.push('/dashboard')
} catch (error) {
  console.error('Login failed:', error.message)
}
```

### Fetch Crypto Data

```typescript
import { getCryptos, getCryptoHistory } from '@/services/cryptoApi'
import { ref, onMounted } from 'vue'

export default {
  setup() {
    const cryptos = ref([])
    
    onMounted(async () => {
      try {
        cryptos.value = await getCryptos()
      } catch (error) {
        console.error('Failed to load cryptos:', error)
      }
    })
    
    return { cryptos }
  }
}
```

### Handle Errors

```typescript
import { getTransactions } from '@/services/transactionApi'
import { formatApiError, isValidationError } from '@/services/errorHandler'

try {
  const transactions = await getTransactions()
} catch (error) {
  const apiError = formatApiError(error)
  
  if (isValidationError(error)) {
    // Handle validation errors
    console.log('Validation errors:', apiError.errors)
  } else {
    // Show user-friendly error message
    console.error('Error:', apiError.message)
  }
}
```

## Configuration

Set API base URL via environment variable:

```env
VITE_API_URL=http://localhost:8000/api
```

If not set, defaults to `http://localhost:8000/api`.

## Error Handling

All API methods throw errors that should be caught and handled:

```typescript
try {
  const result = await apiMethod()
} catch (error) {
  // Error is already formatted by interceptors
  // Access error.response.data for error details
}
```

Common error properties:
- `error.response.status` - HTTP status code
- `error.response.data.message` - Error message
- `error.response.data.errors` - Validation errors (422)
- `error.message` - Network or other errors

## Real-time Updates

Subscribe to real-time events:

```typescript
import echo from '@/services/realtime'

// Subscribe to user channel
echo.private(`user.${userId}`).listen('.crypto.alert', (data) => {
  console.log('Alert received:', data)
})
```

## Best Practices

1. **Always handle errors** - Wrap API calls in try-catch
2. **Use TypeScript** - Import interfaces for type safety
3. **Load on mount** - Use `onMounted` hook to fetch data
4. **Cache when possible** - Use Vuex/Pinia for state management
5. **Show feedback** - Display loading states and error messages
6. **Validate input** - Check user input before API calls
7. **Handle auth** - Use route guards for protected pages
8. **Monitor errors** - Use error handler utilities for consistency

## Troubleshooting

### 401 Unauthorized
- Token expired or invalid
- Check localStorage for valid token
- User will be redirected to login

### 422 Validation Error
- Invalid input data
- Check `error.response.data.errors` for field errors

### 500 Server Error
- Server-side issue
- Check browser console for `__lastServerError`

### Network Error
- No internet connection
- API server not responding
- Check API URL configuration

## Support

For issues or questions about the API services, check:
- Backend API documentation
- Browser console for error messages
- Network tab in browser dev tools
