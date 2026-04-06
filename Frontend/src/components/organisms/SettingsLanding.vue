<script setup>
import { useAuthStore } from '@/stores/auth.js'
import { useRouter } from 'vue-router'
import SettingsOptionCard from '@/components/molecules/SettingsOptionCard.vue'

const authStore = useAuthStore()
const router = useRouter()

const settingsOptions = [
  {
    id: 'profile',
    icon: 'pi-user',
    title: 'Profile Information',
    description: 'Update your phone number and bio',
    route: '/settings/profile'
  },
  {
    id: 'address',
    icon: 'pi-map-marker',
    title: 'Address Information',
    description: 'Manage your address and location',
    route: '/settings/shipping'
  },
  {
    id: 'security',
    icon: 'pi-lock',
    title: 'Security Settings',
    description: 'Change your password and security options',
    route: '/settings/security'
  }
]


const selectOption = (optionRoute) => {
  router.push(optionRoute)
}
</script>

<template>
  <div class="p-8 bg-colors-secondary-light text-colors ">
    <!-- Welcome Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-2">Account Settings</h1>
      <p class="text-colors-secondary">
        Welcome back, <span class="font-semibold">{{ authStore.user?.fname }}</span>. 
        Manage your account settings below.
      </p>
    </div>

    <!-- Settings Options Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <SettingsOptionCard
        v-for="option in settingsOptions"
        :key="option.id"
        :icon="option.icon"
        :title="option.title"
        :description="option.description"
        @click="selectOption(option.route)"
      />
    </div>

    <!-- Quick Tips Section -->
    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-950 border-l-4 border-blue-600 rounded">
      <p class="text-sm text-colors">
        <span class="font-semibold"><i class="pi pi-lightbulb"></i> Tip:</span> Keep your information up to date to ensure a better experience on BookSwap.
      </p>
    </div>
  </div>
</template>
