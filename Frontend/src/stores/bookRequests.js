import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '@/utils/axios.js'

export const useBookRequestsStore = defineStore('bookRequests', () => {
  // State
  const myListings = ref([])
  const myRequests = ref([])
  const currentRequest = ref(null)  
  const loading = ref(false)
  const error = ref(null)

  // Actions
  /**
   * Fetch user's book listings
   * @param {string} status - Filter by status (all, listed, completed, takenDown)
   */
  async function fetchMyListings(status = 'all') {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `/getMyBookListings?status=${status}`
      )

      if (response.data?.success) {
        myListings.value = response.data.bookRequests || []
        console.log('Fetched listings:', myListings.value)
      } else {
        error.value = response.data?.error || 'Failed to fetch listings'
        myListings.value = []
      }
    } catch (err) {
      console.error('Error fetching listings:', err)
      error.value = err.message || 'Failed to fetch listings'
      myListings.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch user's book requests (requests made by the user)
   * @param {string} userId - User ID
   * @param {string} status - Filter by status (all, inProgress, completed)
   */
  async function fetchMyRequests(userId, status = 'all') {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `/getMyBookRequests?id=${userId}&status=${status}`
      )

      if (response.data?.success) {
        myRequests.value = response.data.bookRequests || []
        console.log('Fetched requests:', myRequests.value)
      } else {
        error.value = response.data?.error || 'Failed to fetch requests'
        myRequests.value = []
      }
    } catch (err) {
      console.error('Error fetching requests:', err)
      error.value = err.message || 'Failed to fetch requests'
      myRequests.value = []
    } finally {
      loading.value = false
    }
  }
  /**
   * Fetch a specific book request by ID
   * @param {number|string} requestId - Request ID
   */
  async function fetchRequestById(requestId) {
    const request = myListings.value.find((r) => r.id === requestId)

    if(request) {
      console.log('Found request in listings:', request)
      currentRequest.value = request
      return request
    }
    try {
      const response = await axios.get(`/getBookRequestById?requestId=${requestId}`)
      currentRequest.value = response.data.bookRequest
      return response.data.bookRequest
    } catch (err) {
      console.error('Error fetching request by ID:', err)
      error.value = err.message || 'Failed to fetch request'
    } finally {
      loading.value = false
    }
  }

  /**
   * Create a new book request
   * @param {Object} requestData - Request data (bookId, ownerId, etc.)
   */
  async function createBookRequest(requestData) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post(
        `/createBookRequest`,
        requestData
      )

      if (response.data?.success) {
        console.log('Book request created:', response.data)
        return response.data
      } else {
        throw new Error(response.data?.message || 'Failed to create request')
      }
    } catch (err) {
      console.error('Error creating request:', err)
      error.value = err.message || 'Failed to create request'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Update book request status
   * @param {number|string} requestId - Request ID
   * @param {string} status - New status
   */
  async function updateRequestStatus(requestId, status) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post(
        `/updateRequest`,
        { requestId, status }
      )

      if (response.data?.success) {
        console.log('Request status updated:', response.data)
        // Refresh listings
        await fetchMyListings()
        return response.data
      } else {
        throw new Error(response.data?.message || 'Failed to update status')
      }
    } catch (err) {
      console.error('Error updating status:', err)
      error.value = err.message || 'Failed to update status'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Clear all request data
   */
  function clearRequests() {
    myListings.value = []
    myRequests.value = []
    error.value = null
    currentRequest.value = null
  }

  return {
    // State
    myListings,
    myRequests,
    loading,
    error,
    currentRequest,
    // Actions
    fetchMyListings,
    fetchMyRequests,
    fetchRequestById,
    createBookRequest,
    updateRequestStatus,
    clearRequests,
  }
})
