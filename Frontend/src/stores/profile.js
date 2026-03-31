import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '@/utils/axios.js'
import { useAuthStore } from './auth.js'

export const useProfileStore = defineStore('profile', () => {
  // State
  const profileUser = ref(null)
  const userBooks = ref([])
  const loading = ref(false)
  const error = ref(null)
  const currentUserId = ref(null)

  // Check if viewing own profile
  const isOwnProfile = computed(() => {
    const authStore = useAuthStore()
    return currentUserId.value === authStore.user?.id
  })

  /**
   * Fetch user profile by ID
   * @param {number} userId - User ID to fetch profile for
   */
  async function fetchUserProfile(userId) {
    error.value = null
    
    try {
      const response = await axios.get(`/getUser/${userId}`)
      
      if (response.data?.success && response.data.user) {
        profileUser.value = response.data.user
      } else {
        error.value = response.data?.message || 'Failed to fetch user profile'
      }
    } catch (err) {
      console.error('Error fetching user profile:', err)
      error.value = err.response?.data?.message || err.message || 'Failed to fetch user profile'
    }
  }

  /**
   * Fetch books posted by a user
   * @param {number} userId - User ID to fetch books for
   */
  async function fetchUserBooks(userId) {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/getUserBooks/${userId}`)
      
      if (response.data?.success) {
        userBooks.value = response.data.books || []
      } else {
        error.value = response.data?.message || 'Failed to fetch user books'
      }
    } catch (err) {
      console.error('Error fetching user books:', err)
      error.value = err.response?.data?.message || err.message || 'Failed to fetch user books'
    } finally {
      loading.value = false
    }
  }

  /**
   * Load complete profile for a user (both profile and books)
   * @param {number} userId - User ID to load
   */
  async function loadProfile(userId) {
    currentUserId.value = userId
    profileUser.value = null
    userBooks.value = []
    
    await Promise.all([
      fetchUserProfile(userId),
      fetchUserBooks(userId)
    ])
  }

  /**
   * Reset profile state
   */
  function resetProfile() {
    profileUser.value = null
    userBooks.value = []
    loading.value = false
    error.value = null
    currentUserId.value = null
  }

  return {
    // State
    profileUser,
    userBooks,
    loading,
    error,
    currentUserId,
    // Computed
    isOwnProfile,
    // Actions
    fetchUserProfile,
    fetchUserBooks,
    loadProfile,
    resetProfile,
  }
})
