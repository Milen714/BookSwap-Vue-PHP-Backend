<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost';
import { RouterLink, useRoute, useRouter } from 'vue-router'
import ListingsFilterButtonBar from '@/components/ListingsFilterButtonBar.vue';
import { ref, onMounted, watch } from 'vue'
import { useAuth } from '@/composables/useAuth.js'
import BookSwapRequestCard from '@/components/BookSwapRequestCard.vue'

const { authState } = useAuth()
const requests = ref([])
const route = useRoute()

const fetchRequests = async (status = null) => {
  if (!authState.user?.id) {
    console.log('User ID not available yet')
    return
  }
  
  try {
    const response = await fetch(`${apiBaseUrl}/getMyBookRequests?id=${authState.user.id}&status=${status || route.query.status || 'all'}`, {
      credentials: 'include'
    })
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}  ${response.statusText}`);
    }
    const data = await response.json()
    requests.value = data.bookRequests || []
    console.log('Fetched requests:', requests.value)
  } catch (error) {
    console.log('Error fetching book requests:', error.message || error);
  }
}

// Watch for filter changes
watch(() => route.query.status, () => {
  if (authState.user?.id) {
    fetchRequests()
  }
})

onMounted(async () => {
  if (authState.user?.id) {
    await fetchRequests()
  } else {
    // Wait for user to load (max 5 seconds)
    const checkUser = setInterval(() => {
      if (authState.user?.id) {
        clearInterval(checkUser)
        fetchRequests()
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
        v-for="(request, index) in requests"
        :key="request.id"
        :request="request"
        :reverse="index % 2 === 0"
      />
    </div>
  </div>
</template>