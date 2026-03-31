<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useProfileSettingsStore } from '@/stores/profileSettings.js'
import InputGroup from '@/components/organisms/InputGroup.vue'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'

const authStore = useAuthStore()
const settingsStore = useProfileSettingsStore()

const address = ref('')
const state = ref('')
const country = ref('')
const postCode = ref('')

onMounted(() => {
  if (authStore.user) {
    address.value = authStore.user.address || ''
    state.value = authStore.user.state || ''
    country.value = authStore.user.country || ''
    postCode.value = authStore.user.post_code || ''
  }
})

const handleSaveAddress = async (event) => {
  event.preventDefault()
  settingsStore.resetState()

  // Validate required fields
  const errors = []
  if (!address.value.trim()) errors.push('Address is required')
  if (!state.value.trim()) errors.push('State/Province is required')
  if (!country.value.trim()) errors.push('Country is required')
  if (!postCode.value.trim()) errors.push('Postal Code is required')

  if (errors.length > 0) {
    settingsStore.showError = true
    settingsStore.message = errors.join(', ')
    return
  }

  if (!authStore.user?.id) {
    settingsStore.showError = true
    settingsStore.message = 'User not authenticated'
    return
  }

  const success = await settingsStore.updateAddressInfo(authStore.user.id, {
    address: address.value,
    state: state.value,
    country: country.value,
    post_code: postCode.value
  })

  if (success) {
    authStore.user.address = address.value
    authStore.user.state = state.value
    authStore.user.country = country.value
    authStore.user.post_code = postCode.value
  }
}
</script>

<template>
    <div class="p-6 bg-colors-secondary-light text-colors ">
        <h2 class="text-xl font-semibold mb-4">Address Information</h2>
        <p class="mb-6 text-sm text-colors-secondary">Update your address information.</p>
        
        <form @submit="handleSaveAddress" class="space-y-6">
            <InputGroup 
              label="Street Address:"
              type="text"
              id="address"
              name="address"
              v-model="address"
              :required="true"
              placeholder="123 Main Street"
              wrapper-class="mb-4"
            />

            <InputGroup 
              label="State/Province:"
              type="text"
              id="state"
              name="state"
              v-model="state"
              :required="true"
              placeholder="California"
              wrapper-class="mb-4"
            />

            <InputGroup 
              label="Country:"
              type="text"
              id="country"
              name="country"
              v-model="country"
              :required="true"
              placeholder="United States"
              wrapper-class="mb-4"
            />

            <InputGroup 
              label="Postal Code:"
              type="text"
              id="post_code"
              name="post_code"
              v-model="postCode"
              :required="true"
              placeholder="90210"
              wrapper-class="mb-5"
            />

            <button
              type="submit"
              class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              :disabled="settingsStore.isSaving"
            >
              {{ settingsStore.isSaving ? 'Saving...' : 'Save Address' }}
            </button>
        </form>

        <ErrorCard v-if="settingsStore.showError" :message="settingsStore.message" />
        <SuccessCard v-if="settingsStore.showSuccess" :message="settingsStore.message" />
    </div>
</template>