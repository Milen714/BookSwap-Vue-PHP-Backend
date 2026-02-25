<script setup>
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'
  import LoginForm from '@/components/LoginForm.vue';
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  const router = useRouter();

</script>


<template>
  <LoginForm />
</template>