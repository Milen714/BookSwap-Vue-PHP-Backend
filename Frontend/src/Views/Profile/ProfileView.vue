<script setup>
import { onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { useProfileStore } from '@/stores/profile.js'
import BookPostCard from '@/components/BookPostCard.vue'
import Spinner from '@/components/molecules/Spinner.vue'
import Error from '@/components/molecules/ErrorCard.vue'
import ProfileHeader from '@/components/organisms/ProfileHeader.vue'

const authStore = useAuthStore()
const profileStore = useProfileStore()
const route = useRoute()

// Get userId from route param or use logged-in user's id
const userId = computed(() => {
  return route.params.userId ? parseInt(route.params.userId) : authStore.user?.id
})

onMounted(async () => {
  if (userId.value) {
    await profileStore.loadProfile(userId.value)
  }
})

// Watch for route changes to reload profile
watch(() => route.params.userId, async () => {
  if (userId.value) {
    await profileStore.loadProfile(userId.value)
  }
})
</script>

<template>
  <div class="text-colors dark:text-white min-h-screen bg-colors-light dark:bg-gray-950">
    <!-- Profile Header Component -->
    <ProfileHeader
      :profileUser="profileStore.profileUser"
      :isOwnProfile="profileStore.isOwnProfile"
      :userBooks="profileStore.userBooks"
      :loading="profileStore.loading"
      :error="profileStore.error"
    />

    <!-- Books Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-2xl font-bold text-colors dark:text-white mb-8">
        {{ profileStore.isOwnProfile ? 'My Books' : `${profileStore.profileUser?.fname}'s Books` }} for Swap
      </h2>

      <Spinner v-if="profileStore.loading" />

      <Error v-if="profileStore.error" :message="profileStore.error" />

      <div v-if="!profileStore.loading && !profileStore.error && profileStore.userBooks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <BookPostCard
          v-for="book in profileStore.userBooks"
          :key="book.id"
          :book="book"
        />
      </div>

      <div v-if="!profileStore.loading && !profileStore.error && profileStore.userBooks.length === 0" class="text-center py-12">
        <i class="pi pi-book text-5xl text-colors-secondary dark:text-gray-600 mb-4"></i>
        <p class="text-lg text-colors-secondary dark:text-gray-400">No books posted yet</p>
        <router-link
          v-if="profileStore.isOwnProfile"
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
