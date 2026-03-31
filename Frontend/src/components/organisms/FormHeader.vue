<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: 'pi-user'
  },
  description: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <header>
    <h2 class="text-xl font-semibold mb-4">
      <i :class="`pi ${icon} mr-2`"></i>
      {{ title }}
    </h2>
    <p v-if="description" class="mb-6 text-sm text-colors-secondary">
      {{ description }}
    </p>
  </header>
</template>

<style scoped></style>
