<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'
import PasswordStrengthFeedback from '@/components/PasswordStrengthFeedback.vue'
import axios from '@/utils/axios.js'
import { getPasswordFeedback, isPasswordStrong } from '@/utils/PasswordStrength.js'

const route = useRoute()
const router = useRouter()

const email = ref(route.query.email || '')
const token = ref(route.query.token || '')
const password = ref('')
const repeatPassword = ref('')
const showError = ref(false)
const showSuccess = ref(false)
const message = ref('')

const passwordFeedback = computed(() => getPasswordFeedback(password.value))
const isPasswordValid = computed(() => isPasswordStrong(password.value))
const passwordsMatch = computed(() => password.value && repeatPassword.value && password.value === repeatPassword.value)
const isFormValid = computed(() => isPasswordValid.value && passwordsMatch.value)

const handlePasswordReset = async () => {
  showError.value = false
  showSuccess.value = false
  message.value = ''

  // Validate password strength
  if (!isPasswordValid.value) {
    showError.value = true
    message.value = 'Password does not meet all requirements'
    return
  }

  // Validate passwords match
  if (!passwordsMatch.value) {
    showError.value = true
    message.value = 'Passwords do not match. Please try again.'
    return
  }

  // Validate token and email
  if (!token.value || !email.value) {
    showError.value = true
    message.value = 'Invalid reset link. Please try again.'
    return
  }

  try {
    const response = await axios.post(`/reset-password`, {
      email: email.value,
      token: token.value,
      password: password.value,
      repeatPassword: repeatPassword.value
    }, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    const responseData = response.data;
    if (responseData?.success) {
      showSuccess.value = true
      message.value = responseData.message || 'Password has been reset successfully! Please log in with your new password.'
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else {
      showError.value = true
      message.value = responseData.message || 'Password reset failed. Please try again.'
    }
  } catch (error) {
    showError.value = true
    // Handle nested error structure: {error: {message: "..."}}
    const errorData = error?.response?.data?.error || error?.response?.data
    message.value = errorData?.message || error.message || 'An error occurred while processing your request. Please try again.'
  }
}
</script>

<template>
  <article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <h1 class="text-center mb-6 text-gray-800 font-serif text-2xl">Reset Password</h1>

    <form @submit.prevent="handlePasswordReset">
      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">New Password:</label>
        <input 
          class="form_input" 
          type="password" 
          id="password" 
          v-model="password"
          placeholder="Enter your new password"
          required
        >
        
        <!-- Password Strength Feedback Component -->
        <PasswordStrengthFeedback :feedback="passwordFeedback" />
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="repeatPassword">Repeat New Password:</label>
        <input 
          class="form_input" 
          type="password" 
          id="repeatPassword" 
          v-model="repeatPassword"
          placeholder="Confirm your new password"
          required
        >
        
        <!-- Password Match Indicator -->
        <div v-if="repeatPassword" class="mt-2">
          <p 
            class="text-sm flex items-center"
            :class="passwordsMatch ? 'text-green-600' : 'text-red-600'"
          >
            <svg 
              class="w-4 h-4 mr-2"
              :class="passwordsMatch ? 'text-green-500' : 'text-red-500'"
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path 
                v-if="passwordsMatch"
                fill-rule="evenodd" 
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" 
                clip-rule="evenodd" 
              />
              <path 
                v-else
                fill-rule="evenodd" 
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                clip-rule="evenodd" 
              />
            </svg>
            {{ passwordsMatch ? 'Passwords match' : 'Passwords do not match' }}
          </p>
        </div>
      </article>

      <button 
        class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors" 
        type="submit"
        :disabled="!isFormValid"
      >
        Set New Password
      </button>
    </form>

    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />

    <article class="mt-4 text-center text-gray-700 text-sm">
      <p>Remember your password? <RouterLink class="text-blue-600 hover:underline" to="/login">Log in here</RouterLink>.</p>
    </article>
  </article>
</template>