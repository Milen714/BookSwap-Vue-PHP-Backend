<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'
import InputGroup from '@/components/organisms/InputGroup.vue'
import axios from '@/utils/axios.js'

const router = useRouter()

const showError = ref(false)
const showSuccess = ref(false)
const message = ref('')
const email = ref('')

const handlePasswordReset = async (event) => {
  event.preventDefault()
  showError.value = false
  showSuccess.value = false
  message.value = ''

  const data = {
    email: email.value,
  }

  try {
    const response = await axios.post(`/forgot-password`, data, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    const responseData = response.data;
    if (responseData?.success) {
      showSuccess.value = true
      message.value = responseData.message || 'Password reset request successful! Please check your email.'
      setTimeout(() => {
        router.push({
          path: '/login',
          query: { signupMessage: message.value },
        })
      }, 2000)
    } else {
      showError.value = true
      message.value = responseData.message || 'Password reset request failed. Please try again.'
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
    <h1 class="text-center mb-6 text-gray-800 font-serif text-2xl">Forgot Password</h1>

    <form @submit="handlePasswordReset">
      <InputGroup 
        label="Email:"
        type="email"
        id="email"
        name="email"
        v-model="email"
        placeholder="Enter your email address"
        :required="true"
        wrapper-class="mb-4"
      />

      <button 
        class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700 transition-colors" 
        type="submit"
      >
        Send Reset Link
      </button>
    </form>

    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />

    <article class="mt-4 text-center text-gray-700 text-sm">
      <p>Remember your password? <RouterLink class="text-blue-600 hover:underline" to="/login">Log in here</RouterLink>.</p>
    </article>
  </article>
</template>