<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
import router from '@/Router';
import { useRoute } from 'vue-router';
import ErrorCard from '@/components/molecules/ErrorCard.vue';
import SuccessCard from '@/components/molecules/SuccessCard.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/auth.js'

const authStore = useAuthStore()



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
      });
      console.log(response.data);
      if (response.data.success) {
        // Store JWT token and user data
        if (response.data.token) {
          authStore.setAuthToken(response.data.token, response.data.user);
          console.log('Token set, authStore updated');
        }
        showSuccess.value = true;
        message.value = response.data.message;
        
        // Wait a tick for Vue reactivity to update, then fetch user data
        await new Promise(resolve => setTimeout(resolve, 0));
        await authStore.fetchLoggedInUser();
        
        // Redirect to home page after successful login
        router.push('/');
      } else {
        throw new Error(response.data.message || 'Login failed. Please try again.');
      }
    } catch (error) {
      console.error('Login error:', error);
      showError.value = true;
      message.value = error.response?.data?.message || 'An error occurred during login. Please try again.';
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
            <input class="form_input" type="email" id="email" name="email" required>

        </article>
        <article class="input_group">
            <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password:</label>
            <input class="form_input" type="password" id="password" name="password" required>

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