import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '@/utils/axios.js'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('authToken') || null)
  const loading = ref(true)
  const isLoggedIn = computed(() => !!user.value)

  // Set up axios interceptors to add JWT token to requests
  const setupAxiosInterceptors = () => {
    axios.interceptors.request.use(
      (config) => {
        if (token.value) {
          config.headers.Authorization = `Bearer ${token.value}`
        } else {
          console.warn('No JWT token available for request:', config.url)
        }
        return config
      },
      (error) => {
        return Promise.reject(error)
      }
    )

    // Handle 401 responses - token expired or invalid
    axios.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Clear auth on unauthorized
          clearAuth()
        }
        return Promise.reject(error)
      }
    )
  }

  // Fetch currently logged in user from API
  async function fetchLoggedInUser() {
    if (!token.value) {
      user.value = null
      loading.value = false
      return
    }

    loading.value = true
    try {
      const response = await axios.get(`${apiBaseUrl}/getLoggedInUser`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      })

      if (response.data.success) {
        user.value = response.data.user
        console.log('Logged in user:', user.value)
      } else {
        clearAuth()
      }
    } catch (error) {
      console.error('Error fetching logged in user:', error)
      clearAuth()
    } finally {
      loading.value = false
    }
  }

  // Store token and user after login
  function setAuthToken(newToken, newUser) {
    token.value = newToken
    user.value = newUser
    localStorage.setItem('authToken', newToken)
  }

  // Clear auth state on logout
  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('authToken')
  }

  return {
    // State
    user,
    token,
    loading,
    isLoggedIn,
    // Actions
    fetchLoggedInUser,
    setAuthToken,
    clearAuth,
    setupAxiosInterceptors,
  }
})
