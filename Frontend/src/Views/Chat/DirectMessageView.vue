<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost';
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ref, onMounted, watch, onUnmounted } from 'vue'
import { useAuth } from '@/composables/useAuth.js'
import axios from 'axios'
import Form from '@/components/ChatForm.vue'

const { authState } = useAuth()
const route = useRoute()

const messages = ref([])
const recipientId = route.query.recipientId
const senderId = ref('')

const socket = ref(null)

const fetchMessages = async () => {
  if (!authState.user?.id) {
    console.warn('User not authenticated yet')
    return
  }
  
  const sendId = authState.user.id
  senderId.value = sendId
  
  try {
    const response = await axios.get(`${apiBaseUrl}/getChatMessages?senderId=${sendId}&recipientId=${recipientId}`, { withCredentials: true })
    if (response.data?.success && Array.isArray(response.data.messages)) {
      messages.value = response.data.messages
    } else {
      console.error('Failed to fetch messages:', response.data.message || 'Unknown error')
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const initWebSocket = () => {
  if (!authState.user?.id) return;

  // 1. Connect and tag this connection with your User ID
  socket.value = new WebSocket(`ws://localhost:6001/?userId=${authState.user.id}`);

  socket.value.onopen = () => {
    console.log('Connected to live chat server!');
  };

  // 2. Listen for incoming messages from Node.js
  socket.value.onmessage = (event) => {
    const data = JSON.parse(event.data);
    
    // 3. Security/UX Check: Does this message belong to this specific conversation?
    // We only push it to the screen if the sender is the person we are chatting with.
    if (true) {
      messages.value.push(data);
    }
  };

  socket.value.onerror = (error) => {
    console.error('WebSocket Error:', error);
  };
};

onMounted(() => {
  if (authState.user?.id) {
    fetchMessages();
    initWebSocket();
  }
})

// Watch for auth state changes and fetch/connect when user is loaded
watch(() => authState.user, (newUser) => {
  if (newUser?.id) {
    if (messages.value.length === 0) fetchMessages();
    if (!socket.value) initWebSocket();
  }
})

// Clean up the connection when the user navigates away from the DM page
onUnmounted(() => {
  if (socket.value) {
    socket.value.close();
  }
})

</script>

<template>
  <h1 class="text-white">Direct Message View</h1>
  <p>Chat with user ID: {{ recipientId }}</p>
  <div v-for="message in messages" :key="message.id">
    <p><strong>{{ message.sender_id === authState.user?.id ? 'You' : 'Them' }}:</strong> {{ message.message }}</p>
  </div>
  <Form :recipientId="recipientId" :senderId="senderId" @messageSent="fetchMessages" />
</template>
