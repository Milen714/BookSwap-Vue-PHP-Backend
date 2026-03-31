<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useProfileSettingsStore } from '@/stores/profileSettings.js'
import InputGroup from '@/components/organisms/InputGroup.vue'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'

const authStore = useAuthStore()
const settingsStore = useProfileSettingsStore()

const phoneNumber = ref('')
const bio = ref('')

onMounted(() => {
  if (authStore.user) {
    phoneNumber.value = authStore.user.phone_number || ''
    bio.value = authStore.user.bio || ''
  }
})

const handleSaveProfile = async (event) => {
  event.preventDefault()
  settingsStore.resetState()

  if (!authStore.user?.id) {
    settingsStore.showError = true
    settingsStore.message = 'User not authenticated'
    return
  }

  const success = await settingsStore.updateProfileInfo(authStore.user.id, {
    phone_number: phoneNumber.value,
    bio: bio.value
  })

  if (success) {
    authStore.user.phone_number = phoneNumber.value
    authStore.user.bio = bio.value
  }
}
</script>

<template>
    <div class="p-6 bg-colors-secondary-light text-colors ">
        <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
        <p class="mb-6 text-sm text-colors-secondary">Update your profile information and personal details.</p>
        
        <form @submit="handleSaveProfile" class="space-y-6">
            <InputGroup 
              label="Phone Number:"
              type="tel"
              id="phone_number"
              name="phone_number"
              v-model="phoneNumber"
              placeholder="+1 (555) 123-4567"
              wrapper-class="mb-4"
            />

            <article class="mb-4">
              <label class="block text-sm font-medium text-colors mb-1" for="bio">
                Bio:
              </label>
              <textarea
                id="bio"
                name="bio"
                v-model="bio"
                placeholder="Tell others about yourself..."
                class="form_input w-full"
                rows="4"
              ></textarea>
              <p class="text-xs text-colors-secondary mt-1">
                {{ bio.length }} / 500 characters
              </p>
            </article>

            <button
              type="submit"
              class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              :disabled="settingsStore.isSaving"
            >
              {{ settingsStore.isSaving ? 'Saving...' : 'Save Changes' }}
            </button>
        </form>

        <ErrorCard v-if="settingsStore.showError" :message="settingsStore.message" />
        <SuccessCard v-if="settingsStore.showSuccess" :message="settingsStore.message" />
    </div>
</template>