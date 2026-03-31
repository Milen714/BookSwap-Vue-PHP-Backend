<script setup>
import ManualStep from '@/components/molecules/ManualStep.vue'
defineProps({
  Title: {
    type: String,
    required: true,
  },
  steps: {
    type: Array,
    required: true,
  }
})
</script>

<template>
    <div class="flex flex-col items-start">
            <h3 class="mb-2 text-lg font-semibold text-colors">{{ Title }}</h3>

            <ManualStep
                v-for="(step, index) in steps"
                :key="index"
                :step="step.step"
                :title="step.title"
                :description="step.description"
              />
            
          </div>
</template>