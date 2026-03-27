<script setup>
import { useRoute } from 'vue-router'
import { onMounted, watch, onUnmounted, computed, ref, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useChatStore } from '@/stores/chat.js'
import Form from '@/components/ChatForm.vue'
import Message from '@/components/atoms/Message.vue'
import Error from '@/components/molecules/ErrorCard.vue'
import Spinner from '@/components/molecules/Spinner.vue'

const props = defineProps({
  recipientId: {
    type: Number,
    required: true
  }
})
const authStore = useAuthStore()
const chatStore = useChatStore()
const route = useRoute()
const messagesContainer = ref(null)

const recipientId = props.recipientId

const getInitials = (firstName, lastName) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
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
  <div class="text-white flex flex-col h-[calc(100vh-7rem)]">
    <!-- WhatsApp-style Header -->
    <div class="bg-gray-900 border-b border-gray-700 px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <!-- Avatar Circle -->
        <div v-if="chatStore.currentRecipientInfo" class="flex items-center">
          <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-white font-semibold">{{ getInitials(chatStore.currentRecipientInfo.firstName, chatStore.currentRecipientInfo.lastName) }}</span>
          </div>
        </div>
        
        <!-- User Info -->
        <div>
          <h2 v-if="chatStore.currentRecipientInfo" class="text-white font-semibold">
            {{ chatStore.currentRecipientInfo.firstName }} {{ chatStore.currentRecipientInfo.lastName }}
          </h2>
          <h2 v-else class="text-white font-semibold">User #{{ recipientId }}</h2>
          <p class="text-xs text-gray-400">Active now</p>
        </div>
      </div>
      
      <!-- Action Icons (optional) -->
      <div class="flex gap-4">
        <button class="text-gray-400 hover:text-white transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.686l2.752 1.657a1 1 0 001.063 0l2.752-1.657a1 1 0 00.502-.686l1.498-4.493a1 1 0 00-.948-.684H19a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
          </svg>
        </button>
        <button class="text-gray-400 hover:text-white transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Show loading state -->
    <Spinner v-if="chatStore.loading" />
    
    <!-- Show error if any -->
    <Error v-if="chatStore.error" :message="chatStore.error" />
    
    <!-- Scrollable messages container -->
    <div id="messageCont" ref="messagesContainer" class="flex-1 overflow-y-auto py-4 px-4">
      <!-- Show messages -->
      <div v-if="!chatStore.loading && chatStore.messages.length > 0" class="space-y-2">
        <Message :message="message" v-for="message in chatStore.messages" :key="message.id" />
      </div>
      
      <!-- Show empty state -->
      <p v-else-if="!chatStore.loading" class="text-gray-400 text-center">No messages in this conversation</p>
    </div>
    
    <Form :recipientId="recipientId" :senderId="authStore.user?.id" @messageSent="scrollToBottom()" />
  </div>
</template>

<style scoped>
#messageCont::-webkit-scrollbar {
  display: none;
}
</style>
