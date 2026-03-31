<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useProfileSettingsStore } from '@/stores/profileSettings.js'
import InputGroup from '@/components/organisms/InputGroup.vue'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'
import PasswordStrengthFeedback from '@/components/PasswordStrengthFeedback.vue'
import { getPasswordFeedback, isPasswordStrong } from '@/utils/PasswordStrength.js'

const authStore = useAuthStore()
const settingsStore = useProfileSettingsStore()

const oldPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const passwordFeedback = computed(() => getPasswordFeedback(newPassword.value))
const isNewPasswordValid = computed(() => isPasswordStrong(newPassword.value))
const passwordsMatch = computed(() => newPassword.value === confirmPassword.value && newPassword.value.length > 0)

const handleChangePassword = async (event) => {
  event.preventDefault()
  settingsStore.resetState()

  // Validate old password provided
  if (!oldPassword.value) {
    settingsStore.showError = true
    settingsStore.message = 'Old password is required'
    return
  }

  // Validate new password strength
  if (!isNewPasswordValid.value) {
    settingsStore.showError = true
    settingsStore.message = 'New password does not meet all requirements'
    return
  }

  // Validate passwords match
  if (!passwordsMatch.value) {
    settingsStore.showError = true
    settingsStore.message = 'New password and confirm password do not match'
    return
  }

  if (!authStore.user?.id) {
    settingsStore.showError = true
    settingsStore.message = 'User not authenticated'
    return
  }

  const success = await settingsStore.changePassword(authStore.user.id, {
    old_password: oldPassword.value,
    new_password: newPassword.value,
    confirm_password: confirmPassword.value
  })

  if (success) {
    // Reset form fields
    oldPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  }
}
</script>

<template>
    <div class="p-6 bg-colors-secondary-light text-colors ">
        <h2 class="text-xl font-semibold mb-4">Security Settings</h2>
        <p class="mb-6 text-sm text-colors-secondary">Change your password to keep your account secure.</p>
        
        <form @submit="handleChangePassword" class="space-y-6">
            <InputGroup 
              label="Current Password:"
              type="password"
              id="old_password"
              name="old_password"
              v-model="oldPassword"
              :required="true"
              placeholder="Enter your current password"
              wrapper-class="mb-4"
            />

            <article class="mb-4">
              <InputGroup 
                label="New Password:"
                type="password"
                id="new_password"
                name="new_password"
                v-model="newPassword"
                :required="true"
                placeholder="Enter a new password"
                wrapper-class="mb-2"
              />
              
              <!-- Password Strength Feedback Component -->
              <PasswordStrengthFeedback :feedback="passwordFeedback" />
            </article>

            <article class="mb-4">
              <InputGroup 
                label="Confirm New Password:"
                type="password"
                id="confirm_password"
                name="confirm_password"
                v-model="confirmPassword"
                :required="true"
                placeholder="Confirm your new password"
                wrapper-class="mb-2"
              />
              
              <!-- Password Match Indicator -->
              <div class="flex items-center mt-2" v-if="confirmPassword">
                <i 
                  :class="[
                    'pi',
                    passwordsMatch ? 'pi-check text-green-600' : 'pi-times text-red-600',
                    'mr-2'
                  ]"
                ></i>
                <span 
                  :class="passwordsMatch ? 'text-green-600' : 'text-red-600'"
                  class="text-sm"
                >
                  {{ passwordsMatch ? 'Passwords match' : 'Passwords do not match' }}
                </span>
              </div>
            </article>

            <button
              type="submit"
              class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              :disabled="settingsStore.isSaving || !isNewPasswordValid || !passwordsMatch"
            >
              {{ settingsStore.isSaving ? 'Changing Password...' : 'Change Password' }}
            </button>
        </form>

        <ErrorCard v-if="settingsStore.showError" :message="settingsStore.message" />
        <SuccessCard v-if="settingsStore.showSuccess" :message="settingsStore.message" />
    </div>
</template>