<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  variant: {
    type: String,
    default: 'neutral',
  },
})

const variantClass = computed(() => {
  if (props.variant === 'success') return 'admin-badge-success'
  if (props.variant === 'danger') return 'admin-badge-danger'
  if (props.variant === 'warning') return 'admin-badge-warning'
  return 'admin-badge-neutral'
})
</script>

<template>
  <span class="admin-badge" :class="variantClass">{{ label }}</span>
</template>
