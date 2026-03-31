<script setup>
import { useRouter, useRoute } from 'vue-router'

defineProps({
  path: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  }
})

const router = useRouter()
const route = useRoute()
</script>

<template>
  <li class="mb-3">
    <button 
      @click="router.push(path)" 
      :class="route.path.includes(path) ? 'text-blue-600 bg-colors-secondary' : 'text-colors'"
      class="w-full text-left px-4 py-2 rounded-lg hover:bg-colors-secondary transition-colors"
    >
      <i :class="`${icon} mr-1`"></i>
      {{ label }}
    </button>
  </li>
</template>
