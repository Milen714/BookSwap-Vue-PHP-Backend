<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import ListingsFilterButtonBar from '@/components/molecules/ListingsFilterButtonBar.vue';
import { onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useBookRequestsStore } from '@/stores/bookRequests.js'
import BookSwapRequestCard from '@/components/molecules/BookSwapRequestCard.vue'

const authStore = useAuthStore()
const bookRequestsStore = useBookRequestsStore()
const route = useRoute()

// Watch for filter changes
watch(() => route.query.status, () => {
  if (authStore.user?.id) {
    const status = route.query.status || 'all'
    bookRequestsStore.fetchMyRequests(authStore.user.id, status)
  }
})

onMounted(async () => {
  if (authStore.user?.id) {
    const status = route.query.status || 'all'
    await bookRequestsStore.fetchMyRequests(authStore.user.id, status)
  } else {
    // Wait for user to load (max 5 seconds)
    const checkUser = setInterval(() => {
      if (authStore.user?.id) {
        clearInterval(checkUser)
        const status = route.query.status || 'all'
        bookRequestsStore.fetchMyRequests(authStore.user.id, status)
      }
    }, 100)
    setTimeout(() => clearInterval(checkUser), 5000)
  }
})
</script>

<template>
  <div class="my-requests-container flex flex-col items-center gap-6 mx-auto max-w-4xl px-4 py-8">
    <header>
      <h1 class="text-3xl font-bold mb-6 text-colors">My Book Requests</h1>
      <ListingsFilterButtonBar :basePath="'/myRequests'" />
    </header>
    <div class="flex flex-col gap-4">
      <BookSwapRequestCard
        v-for="(request, index) in bookRequestsStore.myRequests"
        :key="request.id"
        :request="request"
        :reverse="index % 2 === 0"
      />
    </div>
  </div>
</template>