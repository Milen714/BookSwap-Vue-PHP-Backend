<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import axios from '@/utils/axios.js'
import BookPostCard from '@/components/BookPostCard.vue'
import Spinner from '@/components/molecules/Spinner.vue'
import Error from '@/components/molecules/ErrorCard.vue'
import ProfileHeader from '@/components/organisms/ProfileHeader.vue'

const authStore = useAuthStore()
const route = useRoute()
const profileUser = ref(null)
const userBooks = ref([])
const loading = ref(false)
const error = ref(null)

// Get userId from route param or use logged-in user's id
const userId = computed(() => {
  return route.params.userId ? parseInt(route.params.userId) : authStore.user?.id
})

const isOwnProfile = computed(() => {
  return userId.value === authStore.user?.id
})

onMounted(async () => {
  if (userId.value) {
    await Promise.all([fetchUserProfile(), fetchUserBooks()])
  }
})

// Watch for route changes to reload profile
watch(() => route.params.userId, async () => {
  if (userId.value) {
    profileUser.value = null
    userBooks.value = []
    await Promise.all([fetchUserProfile(), fetchUserBooks()])
  }
})

const fetchUserProfile = async () => {
  error.value = null
  
  try {
    const response = await axios.get(`/getUser/${userId.value}`)
    
    if (response.data?.success && response.data.user) {
      profileUser.value = response.data.user
    } else {
      error.value = response.data?.message || 'Failed to fetch user profile'
    }
  } catch (err) {
    console.error('Error fetching user profile:', err)
    error.value = err.response?.data?.message || err.message || 'Failed to fetch user profile'
  }
}

const fetchUserBooks = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await axios.get(`/getUserBooks/${userId.value}`)
    
    if (response.data?.success) {
      userBooks.value = response.data.books || []
    } else {
      error.value = response.data?.message || 'Failed to fetch user books'
    }
  } catch (err) {
    console.error('Error fetching user books:', err)
    error.value = err.response?.data?.message || err.message || 'Failed to fetch user books'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="text-colors dark:text-white min-h-screen bg-colors-light dark:bg-gray-950">
    <!-- Profile Header Component -->
    <ProfileHeader
      :profileUser="profileUser"
      :isOwnProfile="isOwnProfile"
      :userBooks="userBooks"
      :loading="loading"
      :error="error"
    />

    <!-- Books Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-2xl font-bold text-colors dark:text-white mb-8">
        {{ isOwnProfile ? 'My Books' : `${profileUser?.fname}'s Books` }} for Swap
      </h2>

      <Spinner v-if="loading" />

      <Error v-if="error" :message="error" />

      <div v-if="!loading && !error && userBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <BookPostCard
          v-for="book in userBooks"
          :key="book.id"
          :book="book"
        />
      </div>

      <div v-if="!loading && !error && userBooks.length === 0" class="text-center py-12">
        <i class="pi pi-book text-5xl text-colors-secondary dark:text-gray-600 mb-4"></i>
        <p class="text-lg text-colors-secondary dark:text-gray-400">No books posted yet</p>
        <router-link
          v-if="isOwnProfile"
          to="/addBook"
          class="mt-4 inline-block px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
        >
          Add Your First Book
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
