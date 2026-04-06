<script setup>
defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    required: true,
  },
  valueClass: {
    type: String,
    default: 'text-3xl',
  },
})
</script>

<template>
  <div class="admin-stat-card">
    <p class="text-sm opacity-75">{{ label }}</p>
    <p class="mt-1 font-bold text-colors" :class="valueClass">{{ value }}</p>
  </div>
</template>
