<script setup>
  import LoginForm from '@/components/organisms/LoginForm.vue';
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  const router = useRouter();

</script>


<template>
  <LoginForm />
</template>