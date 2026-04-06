<script setup>
defineProps({
  message: {
    type: String,
    default: 'Something went wrong while loading dashboard data.',
  },
})

const emit = defineEmits(['retry'])
</script>

<template>
  <div class="admin-empty-state">
    <p class="admin-badge admin-badge-danger mb-3">Dashboard error</p>
    <h2 class="text-xl font-bold text-colors">Unable to load admin data</h2>
    <p class="mt-2 opacity-80">{{ message }}</p>
    <button type="button" class="admin-action-button mt-4" @click="emit('retry')">
      Retry
    </button>
  </div>
</template>
