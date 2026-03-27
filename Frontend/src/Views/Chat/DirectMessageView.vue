<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import DMChat from '@/components/DMChat.vue'
import { useChatStore } from '@/stores/chat.js'
import { useAuthStore } from '@/stores/auth.js'

const authStore = useAuthStore()
const chatStore = useChatStore()
const route = useRoute()

const partners = ref([])
const recipientId = ref(null)

const getInitials = (fname, lname) => {
  return `${fname?.charAt(0) || ''}${lname?.charAt(0) || ''}`.toUpperCase()
}

const getAvatarColor = (id) => {
  const colors = ['bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-cyan-500']
  return colors[id % colors.length]
}

const loadChat = (partner) => {
  console.log('Loading chat with partner:', partner)
  chatStore.fetchMessages(authStore.user.id, partner.id)
  chatStore.fetchRecipientInfo(partner.id)
  recipientId.value = partner.id
  location.href = `/chat?recipientId=${partner.id}`
}

onMounted(async () => {
  await chatStore.fetchChatPartners()
  partners.value = chatStore.partners
  
  // Load recipientId from URL query parameter if present
  const urlRecipientId = parseInt(route.query.recipientId)
  if (!isNaN(urlRecipientId)) {
    recipientId.value = urlRecipientId
    chatStore.fetchMessages(authStore.user.id, urlRecipientId)
    chatStore.fetchRecipientInfo(urlRecipientId)
  }
})

</script>

<template>
    <section class="flex h-screen">
      <aside class="w-1/4 border-r border-gray-300  flex flex-col">
        <div class="p-4 border-b border-gray-300 mb-2">
          <h2 class="text-2xl font-bold text-gray-800 dark:dark:text-gray-200">Chats</h2>
        </div>
        
        <ul class="flex-1 overflow-y-auto">
          <li v-for="partner in partners" :key="partner.id" 
          @click="loadChat(partner)"
          :class="recipientId === partner.id ? 'bg-[#a7a8a8] dark:bg-[#2D2E2E]' : 'hover:bg-gray-200'"
          class="flex items-center gap-3 p-3 mx-2 rounded-lg cursor-pointer transition">
            <!-- Avatar Circle with Initials -->
            <div :class="getAvatarColor(partner.id)" class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white  font-semibold text-lg">{{ getInitials(partner.fname, partner.lname) }}</span>
            </div>
            
            <!-- Chat Info -->
            <div class="flex-1 min-w-0">
              <div class="text-gray-800 dark:text-gray-400 font-medium">{{ partner.fname }} {{ partner.lname }}</div>
              <div class="text-sm text-black dark:text-gray-300 truncate">{{ partner.message }}</div>
            </div>
          </li>
        </ul>

      </aside>
      
      <section class="h-full w-full">
          <DMChat v-if="recipientId !== null" :recipient-id="recipientId" />
          <div v-else class="flex items-center justify-center h-full">
              <p class="text-gray-400 text-lg">Select a conversation to start chatting</p>
          </div>

      </section>
    </section>
</template>

