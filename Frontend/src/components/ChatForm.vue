<script setup>
import { RouterLink } from 'vue-router'
import { ref } from 'vue'
import { useChatStore } from '@/stores/chat.js'

const chatStore = useChatStore()
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

const emit = defineEmits(['messageSent'])

const sendMessage = async () => {
    if (!message.value.trim()) return;

    try {
        await chatStore.sendMessage(props.senderId, props.recipientId, message.value)
        message.value = ''
        emit('messageSent')
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