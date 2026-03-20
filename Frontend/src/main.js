import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './Router/index.js'
import { useTheme } from './composables/useTheme.js'
import { useAuthStore } from './stores/auth.js'


const { initializeTheme } = useTheme()
initializeTheme()

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Set up axios interceptors for JWT authentication
const authStore = useAuthStore()
authStore.setupAxiosInterceptors()

app.mount('#app')
