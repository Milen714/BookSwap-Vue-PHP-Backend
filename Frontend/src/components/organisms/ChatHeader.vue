<script setup>
import { useChatStore } from '@/stores/chat.js'

defineProps({
  recipientId: {
    type: Number,
    required: true
  }
})

const chatStore = useChatStore()

const getInitials = (firstName, lastName) => {
  return `${firstName?.charAt(0) || ''}${lastName?.charAt(0) || ''}`.toUpperCase()
}
</script>

<template>
  <header class="bg-colors-secondary-light dark:bg-gray-900 border-b border-colors-secondary dark:border-gray-700 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <!-- Avatar Circle -->
      <div v-if="chatStore.currentRecipientInfo" class="flex items-center">
        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
          <span class="text-white font-semibold">{{ getInitials(chatStore.currentRecipientInfo.firstName, chatStore.currentRecipientInfo.lastName) }}</span>
        </div>
      </div>
      
      <!-- User Info -->
      <div>
        <h2 v-if="chatStore.currentRecipientInfo" class="text-colors dark:text-white font-semibold">
          {{ chatStore.currentRecipientInfo.firstName }} {{ chatStore.currentRecipientInfo.lastName }}
        </h2>
        <h2 v-else class="text-colors dark:text-white font-semibold">User #{{ recipientId }}</h2>
        <p class="text-xs text-colors-secondary dark:text-gray-400">Active now</p>
      </div>
    </div>
    
    <!-- Action Icons (optional) -->
    <div class="flex gap-4">
      <RouterLink :to="`/profile/${recipientId}`" class="text-colors-secondary dark:text-gray-400 hover:text-colors dark:hover:text-white transition">
        <i class="pi pi-user text-lg"></i>
      </RouterLink>
    </div>
  </header>
</template>
