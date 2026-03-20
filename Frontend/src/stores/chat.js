import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from '@/utils/axios.js'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'

export const useChatStore = defineStore('chat', () => {
  // State
  const messages = ref([])
  const conversations = ref([])
  const activeConversationId = ref(null)
  const currentRecipientId = ref(null)
  const socket = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const unreadCounts = ref({})

  // Computed
  const activeMessages = computed(() => {
    if (!currentRecipientId.value) return []
    return messages.value.filter(
      (msg) =>
        msg.sender_id === currentRecipientId.value ||
        msg.recipient_id === currentRecipientId.value
    )
  })

  // Actions
  /**
   * Fetch chat messages for a conversation
   * @param {number|string} senderId - Current user ID
   * @param {number|string} recipientId - Other user ID
   */
  async function fetchMessages(senderId, recipientId) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `${apiBaseUrl}/getChatMessages?senderId=${senderId}&recipientId=${recipientId}`,
        { withCredentials: true }
      )

      if (response.data?.success && Array.isArray(response.data.messages)) {
        messages.value = response.data.messages
        currentRecipientId.value = recipientId
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
  async function fetchConversations(userId) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `${apiBaseUrl}/getConversations?userId=${userId}`,
        { withCredentials: true }
      )

      if (response.data?.success && Array.isArray(response.data.conversations)) {
        conversations.value = response.data.conversations
        console.log('Fetched conversations:', conversations.value)
      } else {
        conversations.value = []
      }
    } catch (err) {
      console.error('Error fetching conversations:', err)
      error.value = err.message || 'Failed to fetch conversations'
      conversations.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Initialize WebSocket connection for real-time messaging
   * @param {number|string} userId - User ID
   */
  function initWebSocket(userId) {
    if (!userId) {
      console.warn('Cannot initialize WebSocket without userId')
      return
    }

    try {
      socket.value = new WebSocket(`ws://localhost:6001/?userId=${userId}`)

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
    messages.value.push(message)
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
        `${apiBaseUrl}/sendMessage`,
        { senderId, recipientId, message: messageText },
        { withCredentials: true }
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
    currentRecipientId.value = null
    unreadCounts.value = {}
    error.value = null
    closeWebSocket()
  }

  return {
    // State
    messages,
    conversations,
    activeConversationId,
    currentRecipientId,
    socket,
    loading,
    error,
    unreadCounts,
    // Computed
    activeMessages,
    // Actions
    fetchMessages,
    fetchConversations,
    initWebSocket,
    addMessage,
    sendMessage,
    closeWebSocket,
    setUnreadCount,
    clearUnreadCount,
    clearChat,
  }
})
