<script setup>
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
import router from '@/Router';
import { useRoute } from 'vue-router';
import ErrorCard from '@/components/ErrorCard.vue';
import SuccessCard from '@/components/SuccessCard.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

import { useAuth } from '@/composables/useAuth.js'

const { fetchLoggedInUser } = useAuth()



const showError = ref(false);
const showSuccess = ref(false);
const message = ref('');
const route = useRoute();

onMounted(() => {
  const signupMessage = route.query.signupMessage;
  if (typeof signupMessage === 'string' && signupMessage.trim() !== '') {
    showSuccess.value = true;
    message.value = signupMessage;
    router.replace({ path: '/login', query: {} });
  }
});
  
  const handleLogin = async (event) => {
    const formData = new FormData(event.target);
    const email = formData.get('email');
    const password = formData.get('password');

    try {
      const response = await axios.post(`${apiBaseUrl}/login`, {
        email: email,
        password: password
      }, {
        withCredentials: true
      });
      console.log(response.data);
      if (response.data.success) {
        showSuccess.value = true;
        message.value = response.data.message;
      } else {
        showError.value = true;
        message.value = response.data.message;
        return;
      }
      await fetchLoggedInUser(); // Refresh auth state after login
      router.push('/'); // Redirect to home page after successful login
    } catch (error) {
      console.error('Login error:', error);
      showError.value = true;
      message.value = "Login failed. Please try again.";
    }
    finally {
      setTimeout(() => {
        showError.value = false;
        message.value = '';
      }, 30000); 
    }
  };
</script>


<template>
    <article class="max-w-md mx-auto bg-white p-6 rounded-md shadow-md">
    <form @submit.prevent="handleLogin">
        <article class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email:</label>
            <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="email" id="email" name="email" required>

        </article>
        <article class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password:</label>
            <input class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" id="password" name="password" required>

        </article>
        <button class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700" type="submit">Login</button>
    </form>
    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />
    <article class="mt-4 text-center text-gray-700 text-sm">
      <p>Don't have an account? <RouterLink class="text-blue-600 hover:underline" to="/signup">Sign up here</RouterLink>.</p>
        <p>Forgot your password? <a class="text-blue-600 hover:underline" href="/forgot-password">Reset it here</a>.</p>
    </article>
    </article>
</template>