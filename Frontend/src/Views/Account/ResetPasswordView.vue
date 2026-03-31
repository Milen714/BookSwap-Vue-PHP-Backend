<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/utils/axios.js'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'
import RessetPasswordForm from '@/components/organisms/RessetPasswordForm.vue'
const router = useRouter()
const showError = ref(false)
const showSuccess = ref(false)
const message = ref('')
const checkTokenValidity = async () => {
  const query = new URLSearchParams(window.location.search)
  const token = query.get('token')
  const email = query.get('email')

  if (!token || !email) {
    showError.value = true
    message.value = 'Invalid reset link. Please try again.'
    return
  }

    try {
        const response = await axios.get(`/reset-password?token=${encodeURIComponent(token)}&email=${encodeURIComponent(email)}`)
        if (response.data?.success) {
        showSuccess.value = true
        message.value = 'Token is valid. You can now reset your password.'
        } else {
        showError.value = true
        message.value = response.data?.message || 'Invalid or expired token. Please try again.'
        router.push({
          path: '/login',
          query: { errorMessage: message.value },
        })
        }
    } catch (error) {
        showError.value = true
        const errorData = error?.response?.data?.error || error?.response?.data
        message.value = errorData?.message || 'An error occurred while validating the token. Please try again.'
        router.push({
          path: '/login',
          query: { errorMessage: message.value },
        })
    }
}
onMounted(() => {
  checkTokenValidity()
})
</script>

<template>
  <RessetPasswordForm 
    :showError="showError" 
    :showSuccess="showSuccess" 
    :message="message" 
  />
</template>