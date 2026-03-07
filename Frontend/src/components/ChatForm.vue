<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost';
import { RouterLink } from 'vue-router'
import { ref } from 'vue'
import axios from 'axios'

const message = ref('')

const props = defineProps({
    recipientId: {
        type: Number,
        required: true,
    },
    senderId: {
        type: Number,
        required: true,
    },
})

const sendMessage = async () => {
    if (!message.value.trim()) return;

    try {
        await axios.post(`${apiBaseUrl}/sendDirectMessage`, {
            recipientId: props.recipientId,
            senderId: props.senderId,
            message: message.value
        }, { withCredentials: true });
        message.value = '';
    } catch (error) {
        console.error('Error sending message:', error);
    }
};

</script>

<template>
    <form @submit.prevent="sendMessage">
        <textarea v-model="message" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</template>