<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import ListingsFilterButtonBar from '@/components/ListingsFilterButtonBar.vue';
import { onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useBookRequestsStore } from '@/stores/bookRequests.js'
import BookSwapRequestCard from '@/components/BookSwapRequestCard.vue'

const authStore = useAuthStore()
const bookRequestsStore = useBookRequestsStore()
const route = useRoute()

// Watch for filter changes
watch(() => route.query.status, () => {
  if (authStore.token) {
    const status = route.query.status || 'all'
    bookRequestsStore.fetchMyListings(status)
  }
})

onMounted(async () => {
  if (authStore.token) {
    const status = route.query.status || 'all'
    await bookRequestsStore.fetchMyListings(status)
  } else {
    // Wait for token to load (max 5 seconds)
    const checkToken = setInterval(() => {
      if (authStore.token) {
        clearInterval(checkToken)
        const status = route.query.status || 'all'
        bookRequestsStore.fetchMyListings(status)
      }
    }, 100)
    setTimeout(() => clearInterval(checkToken), 5000)
  }
})
</script>

<template>
  <div class="my-requests-container flex flex-col items-center gap-6 mx-auto max-w-4xl px-4 py-8">
    <header>
      <h1 class="text-3xl font-bold mb-6 text-colors">My Book Listings</h1>
      <ListingsFilterButtonBar
      :basePath="'/myListings'"
      :filters="[
        { label: 'All', status: 'all' },
        { label: 'Listed', status: 'listed' },
        { label: 'Completed', status: 'completed' },
        { label: 'Takendown', status: 'takenDown' },
    ]" />
    </header>
    <div class="flex flex-col gap-4">
      <BookSwapRequestCard
        v-for="(request, index) in bookRequestsStore.myListings"
        :key="request.id"
        :request="request"
        :reverse="index % 2 === 0"
      />
    </div>
  </div>
</template>