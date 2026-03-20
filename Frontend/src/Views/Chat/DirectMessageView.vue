<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { onMounted, watch, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useChatStore } from '@/stores/chat.js'
import Form from '@/components/ChatForm.vue'

const authStore = useAuthStore()
const chatStore = useChatStore()
const route = useRoute()

const recipientId = route.query.recipientId

onMounted(() => {
  if (authStore.user?.id) {
    chatStore.fetchMessages(authStore.user.id, recipientId)
    chatStore.initWebSocket(authStore.user.id)
  }
})

// Watch for auth state changes and fetch/connect when user is loaded
watch(() => authStore.user, (newUser) => {
  if (newUser?.id) {
    if (chatStore.messages.length === 0) chatStore.fetchMessages(newUser.id, recipientId)
    if (!chatStore.socket) chatStore.initWebSocket(newUser.id)
  }
})

// Clean up the connection when the user navigates away from the DM page
onUnmounted(() => {
  chatStore.closeWebSocket()
})

</script>

<template>
  <h1 class="text-white">Direct Message View</h1>
  <p>Chat with user ID: {{ recipientId }}</p>
  <div v-for="message in chatStore.activeMessages" :key="message.id">
    <p><strong>{{ message.sender_id === authStore.user?.id ? 'You' : 'Them' }}:</strong> {{ message.message }}</p>
  </div>
  <Form :recipientId="recipientId" :senderId="authStore.user?.id" @messageSent="chatStore.fetchMessages(authStore.user.id, recipientId)" />
</template>
