<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ErrorCard from '@/components/ErrorCard.vue'
import SuccessCard from '@/components/SuccessCard.vue'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
const router = useRouter()

const showError = ref(false)
const showSuccess = ref(false)
const message = ref('')

const handleSignup = async (event) => {
  showError.value = false
  showSuccess.value = false
  message.value = ''

  const formData = new FormData(event.target);
  const data = {
    email: formData.get('email'),
    password: formData.get('password'),
    fname: formData.get('fname'),
    lname: formData.get('lname'),
    country: formData.get('country'),
    address: formData.get('address'),
    state: formData.get('state'),
    post_code: formData.get('post_code'),
  }

  try {
    const response = await axios.post(`${apiBaseUrl}/signUp`, data, {
      headers: {
        'Content-Type': 'application/json',
      },
      withCredentials: true,
    });
    if (response.data?.success) {
      showSuccess.value = true
      message.value = response.data.message || 'Signup successful! Please log in.'
      setTimeout(() => {
        router.push({
          path: '/login',
          query: { signupMessage: message.value },
        })
      })
    } else {
      showError.value = true
      console.error('Signup failed:', response.data);
      message.value = response.data?.message || 'Signup failed. Please try again.'
    }
  } catch (error) {
    showError.value = true
    message.value = error?.response?.data?.message || 'Signup failed. Please try again.'
  }
}
</script>

<template>
  <article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <h1 class="text-center m-5 font-serif text-2xl">Signup Page</h1>

    <form @submit.prevent="handleSignup">
      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" id="email" name="email" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" id="password" name="password" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="fname">First Name:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="fname" name="fname" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="lname">Last Name:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="lname" name="lname" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="country">Country:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="country" name="country" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="address">Address:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="address" name="address" required>
      </article>

      <article class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="state">State/Province:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="state" name="state" required>
      </article>

      <article class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1" for="post_code">PostCode:</label>
        <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="post_code" name="post_code" required>
      </article>

      <button class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700" type="submit">Signup</button>
    </form>

    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />

    <article class="mt-4 text-center text-gray-700 text-sm">
      <p>Already have an account? <RouterLink class="text-blue-600 hover:underline" to="/login">Log in here</RouterLink>.</p>
    </article>
  </article>
</template>
