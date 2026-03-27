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
                class="flex-1 resize-none rounded-full px-4 py-3 bg-[#242626] text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500  transition max-h-24"
                rows="1"
            ></textarea>
            <button 
                type="submit"
                class="flex-shrink-0 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white rounded-full p-3 transition transform hover:scale-105 active:scale-95 flex items-center justify-center"
                aria-label="Send message"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path d="M16.6915026,12.4744748 L3.50612381,13.2599618 C3.19218622,13.2599618 3.03521743,13.4170592 3.03521743,13.5741566 L1.15159189,20.0151496 C0.8376543,20.8006365 0.99,21.89 1.77946707,22.52 C2.40,22.99 3.50612381,23.1 4.13399899,22.9429026 L21.714504,14.0454487 C22.6563168,13.5741566 23.1272231,12.6315722 22.9702544,11.6889879 L4.13399899,1.04682739 C3.34915502,0.9 2.40734225,0.8429026 1.77946707,1.31465359 C0.994623095,1.93397722 0.837654326,3.0337417 1.15159189,3.81923506 L3.03521743,10.2602281 C3.03521743,10.4173255 3.03521743,10.5744229 3.50612381,10.5744229 L16.6915026,11.3599098 C16.6915026,11.3599098 17.1624089,11.3599098 17.1624089,10.9886178 L17.1624089,11.3599098 C17.1624089,11.5170072 17.1624089,12.4744748 16.6915026,12.4744748 Z"/>
                </svg>
            </button>
        </div>
    </form>
</template>