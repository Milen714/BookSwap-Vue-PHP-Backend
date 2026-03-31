<script setup>
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

const handleKeyDown = (event) => {
    // Send on Enter, unless Shift is pressed (allow multiline with Shift+Enter)
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault()
        sendMessage()
    }
}

</script>

<template>
    <form @submit.prevent="sendMessage" class=" p-4">
        <div class="flex gap-3 items-end">

            <textarea 
                v-model="message" 
                placeholder="Aa" 
                @keydown="handleKeyDown"
                class="flex-1 resize-none rounded-full px-4 py-3 bg-colors-secondary-light text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500  transition max-h-24"
                rows="1"
            ></textarea>
            <button 
                type="submit"
                class="flex-shrink-0 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white rounded-full p-3 transition transform hover:scale-105 active:scale-95 flex items-center justify-center"
                aria-label="Send message"
            >
                <i class="pi pi-send text-xl"></i>
            </button>
        </div>
    </form>
</template>