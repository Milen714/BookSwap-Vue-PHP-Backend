import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import axios from '@/utils/axios.js'

export const useAdminStore = defineStore('admin', () => {
  const users = ref([])
  const analytics = ref({
    summary: {},
    statusBreakdown: [],
    monthlyTrend: [],
    genreBreakdown: [],
  })
  const loading = ref(false)
  const error = ref(null)
  const actionLoadingId = ref(null)
  const lastUpdated = ref(null)

  const summary = computed(() => analytics.value.summary || {})

  async function fetchUsers() {
    const response = await axios.get('/getAllUsers')

    if (response.data?.success && Array.isArray(response.data.users)) {
      users.value = response.data.users
      return response.data.users
    }

    throw new Error(response.data?.error || 'Failed to fetch users')
  }

  async function fetchAnalytics() {
    const response = await axios.get('/getAdminAnalytics')

    if (response.data?.success && response.data.analytics) {
      analytics.value = {
        summary: response.data.analytics.summary || {},
        statusBreakdown: response.data.analytics.statusBreakdown || [],
        monthlyTrend: response.data.analytics.monthlyTrend || [],
        genreBreakdown: response.data.analytics.genreBreakdown || [],
      }
      return analytics.value
    }

    throw new Error(response.data?.error || 'Failed to fetch analytics')
  }

  async function fetchDashboardData() {
    loading.value = true
    error.value = null

    try {
      await Promise.all([fetchUsers(), fetchAnalytics()])
      lastUpdated.value = new Date()
    } catch (err) {
      console.error('Error loading admin dashboard:', err)
      error.value = err.response?.data?.error || err.message || 'Failed to load admin dashboard'
    } finally {
      loading.value = false
    }
  }

  async function toggleUserStatus(userId, isActive) {
    actionLoadingId.value = userId
    error.value = null

    try {
      const response = await axios.put('/toggleUserStatus', {
        userId,
        isActive,
      })

      if (!response.data?.success) {
        throw new Error(response.data?.error || 'Failed to update user status')
      }

      const index = users.value.findIndex((user) => Number(user.id) === Number(userId))
      if (index !== -1) {
        users.value[index] = {
          ...users.value[index],
          isActive,
        }
      }

      await fetchAnalytics()
      lastUpdated.value = new Date()

      return response.data
    } catch (err) {
      console.error('Error updating user status:', err)
      error.value = err.response?.data?.error || err.message || 'Failed to update user status'
      throw err
    } finally {
      actionLoadingId.value = null
    }
  }

  function clearAdminState() {
    users.value = []
    analytics.value = {
      summary: {},
      statusBreakdown: [],
      monthlyTrend: [],
      genreBreakdown: [],
    }
    loading.value = false
    error.value = null
    actionLoadingId.value = null
    lastUpdated.value = null
  }

  return {
    users,
    analytics,
    summary,
    loading,
    error,
    actionLoadingId,
    lastUpdated,
    fetchUsers,
    fetchAnalytics,
    fetchDashboardData,
    toggleUserStatus,
    clearAdminState,
  }
})
