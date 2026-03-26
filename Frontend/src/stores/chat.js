import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '@/utils/axios.js'

export const useChatStore = defineStore('chat', () => {
  // State
  const messages = ref([])
  const partners = ref([])
  const activeConversationId = ref(null)
  const currentUserId = ref(null)
  const currentRecipientId = ref(null)
  const currentRecipientInfo = ref(null)
  const socket = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const unreadCounts = ref({})

  // Computed
  const activeMessages = computed(() => {
    if (!currentRecipientId.value || !currentUserId.value) return []
    return messages.value.filter(
      (msg) =>
        (msg.sender_id === currentUserId.value && msg.recipient_id === currentRecipientId.value) ||
        (msg.sender_id === currentRecipientId.value && msg.recipient_id === currentUserId.value)
    )
  })

  // Actions
  /**
   * Fetch recipient user info
   * @param {number|string} recipientId - Recipient user ID
   */
  async function fetchRecipientInfo(recipientId) {
    try {
      const response = await axios.get(`/getUserInfo?userId=${recipientId}`)
      
      if (response.data?.success && response.data.user) {
        currentRecipientInfo.value = response.data.user
        //console.log('Fetched recipient info:', currentRecipientInfo.value)
      }
    } catch (err) {
      console.error('Error fetching recipient info:', err)
      currentRecipientInfo.value = null
    }
  }

  /**
   * Fetch chat messages for a conversation
   * @param {number|string} senderId - Current user ID
   * @param {number|string} recipientId - Other user ID
   */
  async function fetchMessages(senderId, recipientId) {
    loading.value = true
    error.value = null

    try {
      currentUserId.value = senderId
      const response = await axios.get(
        `/getChatMessages?senderId=${senderId}&recipientId=${recipientId}`
      )

      if (response.data?.success && Array.isArray(response.data.messages)) {
        messages.value = response.data.messages
        currentRecipientId.value = parseInt(recipientId)
        // Fetch recipient info
        await fetchRecipientInfo(recipientId)
        console.log('Fetched messages:', messages.value)
      } else {
        error.value = 'Failed to fetch messages'
        messages.value = []
      }
    } catch (err) {
      console.error('Error fetching messages:', err)
      error.value = err.message || 'Failed to fetch messages'
      messages.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch all conversations for the user
   * @param {number|string} userId - User ID
   */
  async function fetchChatPartners(userId) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `/getChatPartners`
      )

      if (response.data?.success && Array.isArray(response.data.partners)) {
        partners.value = response.data.partners
        console.log('Fetched chat partners:', partners.value)
      } else {
        partners.value = []
      }
    } catch (err) {
      console.error('Error fetching chat partners:', err)
      error.value = err.message || 'Failed to fetch chat partners'
      partners.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Initialize WebSocket connection for real-time messaging
   * @param {number|string} userId - User ID
   */
  function initWebSocket() {
    const token = localStorage.getItem('authToken')
    if (!token) {
      console.warn('No auth token found, cannot initialize WebSocket')
      return
    }
    

    try {
      socket.value = new WebSocket(`ws://localhost:6001/?token=${encodeURIComponent(token)}`)

      socket.value.onopen = () => {
        console.log('Connected to WebSocket chat server')
      }

      socket.value.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)
          addMessage(data)
        } catch (err) {
          console.error('Error parsing WebSocket message:', err)
        }
      }

      socket.value.onerror = (error) => {
        console.error('WebSocket error:', error)
        error.value = 'WebSocket connection error'
      }

      socket.value.onclose = () => {
        console.log('Disconnected from WebSocket chat server')
      }
    } catch (err) {
      console.error('Error initializing WebSocket:', err)
      error.value = err.message || 'Failed to initialize WebSocket'
    }
  }

  /**
   * Add a message to the store
   * @param {Object} message - Message object
   */
  function addMessage(message) {
    // Normalize message format from WebSocket (camelCase to snake_case)
    const normalizedMessage = {
      sender_id: message.sender_id || message.senderId,
      recipient_id: message.recipient_id || message.recipientId,
      message: message.message,
      created_at: message.created_at || new Date().toISOString(),
      id: message.id || `ws-${Date.now()}-${Math.random()}`
    }
    
    console.log('Adding message to store:', normalizedMessage)
    messages.value.push(normalizedMessage)
  }

  /**
   * Send a message via API
   * @param {number|string} senderId - Sender ID
   * @param {number|string} recipientId - Recipient ID
   * @param {string} messageText - Message content
   */
  async function sendMessage(senderId, recipientId, messageText) {
    if (!messageText.trim()) {
      error.value = 'Message cannot be empty'
      return
    }

    try {
      const response = await axios.post(
        `/sendDirectMessage`,
        { senderId, recipientId, message: messageText }
      )

      if (response.data?.success) {
        console.log('Message sent:', response.data)
        // Message will be received via WebSocket
        return response.data
      } else {
        throw new Error(response.data?.message || 'Failed to send message')
      }
    } catch (err) {
      console.error('Error sending message:', err)
      error.value = err.message || 'Failed to send message'
      throw err
    }
  }

  /**
   * Close WebSocket connection
   */
  function closeWebSocket() {
    if (socket.value) {
      socket.value.close()
      socket.value = null
    }
  }

  /**
   * Set unread count for a conversation
   * @param {number|string} conversationId - Conversation ID
   * @param {number} count - Unread message count
   */
  function setUnreadCount(conversationId, count) {
    unreadCounts.value[conversationId] = count
  }

  /**
   * Clear unread count for a conversation
   * @param {number|string} conversationId - Conversation ID
   */
  function clearUnreadCount(conversationId) {
    unreadCounts.value[conversationId] = 0
  }

  /**
   * Clear all chat data
   */
  function clearChat() {
    messages.value = []
    conversations.value = []
    activeConversationId.value = null
    currentUserId.value = null
    currentRecipientId.value = null
    unreadCounts.value = {}
    error.value = null
    closeWebSocket()
  }

  return {
    // State
    messages,
    partners,
    activeConversationId,
    currentUserId,
    currentRecipientId,
    currentRecipientInfo,
    socket,
    loading,
    error,
    unreadCounts,
    // Computed
    activeMessages,
    // Actions
    fetchMessages,
    fetchChatPartners,
    fetchRecipientInfo,
    initWebSocket,
    addMessage,
    sendMessage,
    closeWebSocket,
    setUnreadCount,
    clearUnreadCount,
    clearChat,
  }
})
