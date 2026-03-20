<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { onMounted, watch, onUnmounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useChatStore } from '@/stores/chat.js'
import Form from '@/components/ChatForm.vue'

const authStore = useAuthStore()
const chatStore = useChatStore()
const route = useRoute()

const recipientId = parseInt(route.query.recipientId)

const handleMessagesLoaded = () => {
  console.log('Messages loaded, details:', {
    totalMessages: chatStore.messages.length,
    currentUserId: chatStore.currentUserId,
    recipientId: chatStore.currentRecipientId,
    activeMessages: chatStore.activeMessages.length
  })
}

onMounted(() => {
  if (authStore.user?.id) {
    console.log('Mounted with user:', authStore.user.id)
    chatStore.fetchMessages(authStore.user.id, recipientId)
    // Ensure socket is only initialized once
    if (!chatStore.socket) {
      setTimeout(() => {
        chatStore.initWebSocket()
      }, 100)
    }
  }
})

// Watch for auth state changes and fetch/connect when user is loaded
watch(() => authStore.user?.id, (userId) => {
  if (userId && chatStore.messages.length === 0) {
    console.log('Auth loaded, fetching messages')
    chatStore.fetchMessages(userId, recipientId)
    if (!chatStore.socket) {
      setTimeout(() => {
        chatStore.initWebSocket()
      }, 100)
    }
  }
})

// Watch for message changes to log
watch(() => chatStore.messages, (newMessages) => {
  console.log('Messages updated:', newMessages.length, 'Active messages:', chatStore.activeMessages.length)
}, { deep: true })

// Clean up the connection when the user navigates away from the DM page
onUnmounted(() => {
  if (typeof chatStore.closeWebSocket === 'function') {
    chatStore.closeWebSocket()
  }
})

</script>

<template>
  <div class="text-white">
    <h1>Direct Message View</h1>
    <p v-if="chatStore.currentRecipientInfo">
      Chat with <strong>{{ chatStore.currentRecipientInfo.firstName }} {{ chatStore.currentRecipientInfo.lastName }}</strong> (ID: {{ recipientId }})
    </p>
    <p v-else>Chat with user ID: {{ recipientId }}</p>
    
    <!-- Show loading state -->
    <p v-if="chatStore.loading" class="text-yellow-500">Loading messages...</p>
    
    <!-- Show error if any -->
    <p v-if="chatStore.error" class="text-red-500">{{ chatStore.error }}</p>
    
    <!-- Show messages -->
    <div v-if="!chatStore.loading && chatStore.activeMessages.length > 0" class="space-y-2">
      <div v-for="message in chatStore.activeMessages" :key="message.id" class="bg-gray-800 p-2 rounded">
        <p><strong>{{ message.sender_id === authStore.user?.id ? 'You' : 'Them' }}:</strong> {{ message.message }}</p>
        <small class="text-gray-400">{{ new Date(message.created_at).toLocaleString() }}</small>
      </div>
    </div>
    
    <!-- Show empty state -->
    <p v-else-if="!chatStore.loading" class="text-gray-400">No messages in this conversation</p>
    
    <Form :recipientId="recipientId" :senderId="authStore.user?.id" @messageSent="chatStore.fetchMessages(authStore.user.id, recipientId)" />
  </div>
</template>
