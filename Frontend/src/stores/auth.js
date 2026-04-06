import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios, { getAuthToken, setAuthToken as setApiAuthToken } from '@/utils/axios.js'
import { useChatStore } from '@/stores/chat.js'

let responseInterceptorInitialized = false

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(getAuthToken())
  const loading = ref(true)
  const isLoggedIn = computed(() => !!user.value)

  function clearSessionState() {
    clearAuth()

    // Ensure chat websocket/data are reset when auth is cleared.
    try {
      const chatStore = useChatStore()
      chatStore.clearChat()
    } catch (err) {
      console.error('Failed to clear chat state during logout:', err)
    }
  }

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
          clearSessionState()
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
        clearSessionState()
      }
    } catch (error) {
      console.error('Error fetching logged in user:', error)
      clearSessionState()
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
    loading.value = false
    setApiAuthToken(null)
  }

  // Call backend logout for completeness, then always clear client session.
  async function logout() {
    try {
      await axios.post(`/logout`, {})
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      clearSessionState()
    }
  }

  return {
    // State
    user,
    token,
    loading,
    isLoggedIn,
    // Actions
    fetchLoggedInUser,
    logout,
    setAuthToken,
    clearAuth,
    setupAxiosInterceptors,
  }
})
