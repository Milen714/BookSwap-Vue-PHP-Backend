import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios, { getAuthToken, setAuthToken as setApiAuthToken } from '@/utils/axios.js'

let responseInterceptorInitialized = false

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(getAuthToken())
  const loading = ref(true)
  const isLoggedIn = computed(() => !!user.value)

  // Set up axios interceptors to handle expired or invalid sessions
  const setupAxiosInterceptors = () => {
    if (responseInterceptorInitialized) {
      return
    }

    responseInterceptorInitialized = true

    axios.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
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
      const response = await axios.get(`/getLoggedInUser`)

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
    setApiAuthToken(newToken)
  }

  // Clear auth state on logout
  function clearAuth() {
    token.value = null
    user.value = null
    setApiAuthToken(null)
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
