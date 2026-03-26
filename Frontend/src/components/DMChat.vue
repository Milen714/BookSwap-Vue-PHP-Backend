<script setup>
import { useRoute } from 'vue-router'
import { onMounted, watch, onUnmounted, computed, ref, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useChatStore } from '@/stores/chat.js'
import Form from '@/components/ChatForm.vue'
import Message from '@/components/atoms/Message.vue'
import Error from '@/components/molecules/ErrorCard.vue'
import Spinner from '@/components/molecules/Spinner.vue'

const authStore = useAuthStore()
const chatStore = useChatStore()
const route = useRoute()
const messagesContainer = ref(null)

const recipientId = parseInt(route.query.recipientId)

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const handleMessagesLoaded = () => {
  console.log('Messages loaded, details:', {
    totalMessages: chatStore.messages.length,
    currentUserId: chatStore.currentUserId,
    recipientId: chatStore.currentRecipientId,
    activeMessages: chatStore.activeMessages.length
  })
  scrollToBottom()
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
  scrollToBottom()
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

// Watch for message changes to scroll to bottom
watch(() => chatStore.messages, (newMessages) => {
  console.log('Messages updated:', newMessages.length, 'Active messages:', chatStore.activeMessages.length)
  scrollToBottom()
}, { deep: true })

// Clean up the connection when the user navigates away from the DM page
onUnmounted(() => {
  if (typeof chatStore.closeWebSocket === 'function') {
    chatStore.closeWebSocket()
  }
})

</script>

<template>
  <div class="text-white flex flex-col h-full ml-3">
    <h1>Direct Message View</h1>
    <p v-if="chatStore.currentRecipientInfo">
      Chat with <strong>{{ chatStore.currentRecipientInfo.firstName }} {{ chatStore.currentRecipientInfo.lastName }}</strong> (ID: {{ recipientId }})
    </p>
    <p v-else>Chat with user ID: {{ recipientId }}</p>
    
    <!-- Show loading state -->
    <Spinner v-if="chatStore.loading" />
    
    <!-- Show error if any -->
    <Error v-if="chatStore.error" :message="chatStore.error" />
    
    <!-- Scrollable messages container -->
    <div id="messageCont" ref="messagesContainer" class="flex-1 overflow-y-auto py-4">
      <!-- Show messages -->
      <div v-if="!chatStore.loading && chatStore.messages.length > 0" class="space-y-2">
        <Message :message="message" v-for="message in chatStore.messages" :key="message.id" />
      </div>
      
      <!-- Show empty state -->
      <p v-else-if="!chatStore.loading" class="text-gray-400">No messages in this conversation</p>
    </div>
    
    <Form :recipientId="recipientId" :senderId="authStore.user?.id" @messageSent="scrollToBottom()" />
  </div>
</template>

<style scoped>
#messageCont::-webkit-scrollbar {
  display: none;
}
</style>
