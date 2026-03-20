<script setup>
import { ref } from 'vue'
const props = defineProps({
  message: {
    type: String,
    default: 'Loading...'
  }
})

</script>

<template>
    <div class="flex items-center justify-center p-5">
      <div class="text-center">
        <div
          class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"
        ></div>
        <p class="text-gray-600">{{ props.message }}</p>
      </div>
    </div>
</template>
