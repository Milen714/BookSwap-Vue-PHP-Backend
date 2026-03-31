<script setup>
defineProps({
  feedback: {
    type: Array,
    required: true,
    default: () => []
  },
  showWhenEmpty: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div v-if="feedback.length > 0 || showWhenEmpty" class="mt-3 space-y-2">
    <div 
      v-for="requirement in feedback" 
      :key="requirement.id"
      class="flex items-center text-sm"
      :class="requirement.valid ? 'text-green-600' : 'text-red-600'"
    >
      <i 
        :class="[
          'pi',
          requirement.valid ? 'pi-check' : 'pi-times',
          'mr-2'
        ]"
      ></i>
      <span>{{ requirement.text }}</span>
    </div>
  </div>
</template>
