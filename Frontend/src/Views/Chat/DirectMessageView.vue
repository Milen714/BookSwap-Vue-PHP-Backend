<script setup>
import { ref, onMounted } from 'vue'
import DMChat from '@/components/DMChat.vue'
import { useChatStore } from '@/stores/chat.js'
const chatStore = useChatStore()

const partners = ref([])

onMounted(async () => {
  await chatStore.fetchChatPartners()
  partners.value = chatStore.partners
})

</script>

<template>
    <section class="flex">
      <aside class="w-1/4 p-4 border-r border-gray-300">
        <h2 class="text-xl font-semibold mb-4">Conversations</h2>
        <!-- Placeholder for conversation list -->
        <p class="text-gray-400">Conversation list will go here</p>
        <ul>
          <li v-for="partner in partners" :key="partner.id" class="mb-2 p-2 bg-gray-200 rounded cursor-pointer hover:bg-gray-300 text-black">
            <div>{{ partner.fname }} {{ partner.lname }}</div>
            <div class="text-sm text-gray-500 text-right">{{ partner.message }}</div>
          </li>
        </ul>

      </aside>
      
      <section class="h-[80vh] w-full">
          <DMChat />
      </section>
    </section>
</template>

