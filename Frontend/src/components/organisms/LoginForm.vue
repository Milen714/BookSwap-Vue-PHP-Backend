<script setup>
import router from '@/Router';
import { useRoute } from 'vue-router';
import ErrorCard from '@/components/molecules/ErrorCard.vue';
import SuccessCard from '@/components/molecules/SuccessCard.vue';
import InputGroup from '@/components/organisms/InputGroup.vue';
import axios from '@/utils/axios.js';
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/auth.js'

const authStore = useAuthStore()

const showError = ref(false);
const showSuccess = ref(false);
const message = ref('');
const route = useRoute();
const email = ref('');
const password = ref('');

onMounted(() => {
  const signupMessage = route.query.signupMessage;
  const errorMessage = route.query.errorMessage;
  if (typeof signupMessage === 'string' && signupMessage.trim() !== '') {
    showSuccess.value = true;
    message.value = signupMessage;
    router.replace({ path: '/login', query: {} });
  }
  if (typeof errorMessage === 'string' && errorMessage.trim() !== '') {
    showError.value = true;
    message.value = errorMessage;
    router.replace({ path: '/login', query: {} });
  }
});
  
  const handleLogin = async (event) => {
    event.preventDefault();

    try {
      const response = await axios.post(`/login`, {
        email: email.value,
        password: password.value
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
    <article class="max-w-md mx-auto bg-colors-secondary-light text-colors p-6 rounded-md shadow-md">
    <form @submit="handleLogin">
        <InputGroup 
          label="Email:"
          type="email"
          id="email"
          name="email"
          v-model="email"
          :required="true"
          wrapper-class="input_group-4"
        />
        <InputGroup 
          label="Password:"
          type="password"
          id="password"
          name="password"
          v-model="password"
          :required="true"
          wrapper-class="input_group"
        />
        <button class="w-full rounded-md bg-blue-600 text-white py-2 font-medium hover:bg-blue-700" type="submit">Login</button>
    </form>
    <ErrorCard v-if="showError" :message="message" />
    <SuccessCard v-if="showSuccess" :message="message" />
    <article class="mt-4 text-center text-colors text-sm">
      <p>Don't have an account? <RouterLink class="text-blue-600 hover:underline" to="/signup">Sign up here</RouterLink>.</p>
        <p>Forgot your password? <a class="text-blue-600 hover:underline" href="/forgot-password">Reset it here</a>.</p>
    </article>
    </article>
</template>
