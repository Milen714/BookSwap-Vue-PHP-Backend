<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
})

defineEmits(['click'])
</script>

<template>
  <button
    @click="$emit('click')"
    class="p-6 bg-colors-secondary rounded-lg hover:shadow-lg transition-shadow duration-200 text-left group cursor-pointer border-2 border-transparent hover:border-blue-600"
  >
    <div class="flex items-start mb-3">
      <i 
        :class="['pi', icon, 'text-2xl text-blue-600 group-hover:scale-110 transition-transform']"
      ></i>
    </div>
    <h3 class="text-lg font-semibold mb-2 group-hover:text-blue-600 transition-colors">
      {{ title }}
    </h3>
    <p class="text-sm text-colors-secondary">
      {{ description }}
    </p>
  </button>
</template>
