<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import MainNav from '@/components/MainNav.vue'
import MainFooter from '@/components/MainFooter.vue'
import { useAuth } from '@/composables/useAuth.js'

const { fetchLoggedInUser } = useAuth()

onMounted(() => {
  fetchLoggedInUser()
})
</script>

<template>
  <MainNav />
  <main class="min-h-[75vh] bg-colors px-4 pt-28 pb-8 font-sans">
    <RouterView />
  </main>
  <MainFooter />
</template>

<style scoped>

</style>
