<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import MainNav from '@/components/organisms/MainNav.vue'
import MainFooter from '@/components/organisms/MainFooter.vue'
import { useAuthStore } from '@/stores/auth.js'

const authStore = useAuthStore()

onMounted(() => {
  authStore.fetchLoggedInUser()
})
</script>

<template>
  <MainNav />
  <main class="min-h-[75vh] bg-colors  pt-20 pb-8 font-sans">
    <RouterView />
  </main>
  <MainFooter />
</template>

<style scoped>

</style>
