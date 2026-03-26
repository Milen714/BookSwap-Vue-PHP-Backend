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
  return props.message.senderId === authStore.user?.id
})

const formattedDate = computed(() => {
  try {
    const date = new Date(props.message.created_at)
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
        <span class="text-xs text-gray-400">{{ props.message.senderId }}</span>
        <span class="text-xs text-gray-400">{{ formattedDate }}</span>
    </div>
  </div>
</template>