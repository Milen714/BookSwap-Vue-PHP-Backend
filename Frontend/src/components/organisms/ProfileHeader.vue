<script setup>
import Spinner from '@/components/molecules/Spinner.vue'

defineProps({
  profileUser: {
    type: Object,
    default: null
  },
  isOwnProfile: {
    type: Boolean,
    required: true
  },
  userBooks: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})
</script>

<template>
  <!-- Profile Header -->
  <header class="bg-colors-secondary-light dark:bg-gray-900 border-b border-colors-secondary dark:border-gray-700">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <Spinner v-if="loading && !profileUser" />
      
      <div v-if="profileUser && !loading" class="flex items-start gap-8">
        <!-- User Avatar -->
        <div class="flex-shrink-0">
          <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
            <span class="text-white text-3xl font-bold">
              {{ profileUser?.fname?.charAt(0) || '' }}{{ profileUser?.lname?.charAt(0) || '' }}
            </span>
          </div>
        </div>

        <!-- User Info -->
        <div class="flex-1">
          <!-- Name -->
          <h1 class="text-3xl font-bold text-colors dark:text-white">
            {{ profileUser?.fname }} {{ profileUser?.lname }}
          </h1>

          <!-- Bio -->
          <p v-if="profileUser?.bio" class="text-colors-secondary dark:text-gray-400 mt-3 text-lg">
            {{ profileUser.bio }}
          </p>

          <!-- Location -->
          <div class="flex items-center gap-2 mt-4 text-colors-secondary dark:text-gray-400">
            <i class="pi pi-map-marker text-lg"></i>
            <span>
              {{ profileUser?.state || 'City not specified' }}<span v-if="profileUser?.state && profileUser?.country">, </span>{{ profileUser?.country || 'Country not specified' }}
            </span>
          </div>

          <!-- Stats or additional info -->
          <div class="flex gap-6 mt-6">
            <div>
              <p class="text-2xl font-bold text-colors dark:text-white">{{ userBooks.length }}</p>
              <p class="text-sm text-colors-secondary dark:text-gray-400">Books for Swap</p>
            </div>
          </div>

          <!-- Edit profile button if viewing own profile -->
          <div v-if="isOwnProfile" class="mt-6">
            <router-link
              to="/settings/profile"
              class="inline-block px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition"
            >
              Edit Profile
            </router-link>
          </div>

          <!-- Message button if viewing someone else's profile -->
          <div v-else class="mt-6">
            <router-link
              :to="`/chat?recipientId=${profileUser?.id}`"
              class="inline-flex px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition items-center gap-2"
            >
              <i class="pi pi-send"></i>
              Message User
            </router-link>
          </div>
        </div>
      </div>

      <div v-if="error && !profileUser" class="text-center py-8">
        <p class="text-lg text-red-500">{{ error }}</p>
      </div>
    </div>
  </header>
</template>

<style scoped></style>
