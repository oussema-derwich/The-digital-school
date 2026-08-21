import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './index.css'
// realtime must be initialized early so components can subscribe
import './services/realtime'
// Load user from storage on app startup
import { loadUserFromStorage } from './services/auth'

// Initialize app
const app = createApp(App)

// Load authentication state from storage
loadUserFromStorage()

// Use router
app.use(router)

// Mount app
app.mount('#app')

