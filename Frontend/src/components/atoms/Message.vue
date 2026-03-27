<script setup>
import { computed } from 'vue'
import { useChatStore } from '@/stores/chat.js'
import { useAuthStore } from '@/stores/auth.js'
const chatStore = useChatStore()
const authStore = useAuthStore()
const props = defineProps({
  message: {
    type: Object,
    required: true
  }
})
const isCurrentUser = computed(() => {
  // Handle both snake_case (from WebSocket) and camelCase (from database)
  const senderId = props.message.sender_id || props.message.senderId
  return senderId === authStore.user?.id
})

const formattedDate = computed(() => {
  try {
    let dateStr = props.message.created_at
    
    // Handle DateTime object from database (has .date property)
    if (typeof dateStr === 'object' && dateStr.date) {
      dateStr = dateStr.date
    }
    
    // Handle PHP date format (Y-m-d H:i:s) by converting to ISO 8601
    if (typeof dateStr === 'string' && dateStr.includes(' ') && !dateStr.includes('T')) {
      dateStr = dateStr.replace(' ', 'T') + 'Z'
    }
    
    const date = new Date(dateStr)
    
    // Check if the date is valid
    if (isNaN(date.getTime())) {
      return 'Invalid date'
    }
    
    return date.toLocaleString()
  } catch (e) {
    return 'Invalid date'
  }
})

</script>

<template>
  <div :class="[isCurrentUser ? 'text-right bg-green-900' : 'text-left bg-gray-800']" class="text-white p-3 rounded-lg mb-2 max-w-xs">
    <p>{{ props.message.message }}</p>
    <div class="flex justify-between space-x-2 mt-1">
        <span class="text-xs text-gray-400">{{ props.message.sender_id || props.message.senderId }}</span>
        <span class="text-xs text-gray-400">{{ formattedDate }}</span>
    </div>
  </div>
</template>