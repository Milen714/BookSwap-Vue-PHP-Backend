<script setup>
defineProps({
  step: {
    type: Number,
    required: true,
  },
    title: {
        type: String,
        required: true,
    },
        description: {
            type: String,
            required: true,
        },
})
</script>

<template>
    <article class="mb-2 ml-4 flex flex-row items-center gap-4">
              <span class="text-lg font-bold text-blue-600">{{ step }}</span>
              <header class="flex flex-col items-start">
                <h3 class="text-lg font-semibold text-colors">{{ title }}</h3>
                <p class="text-sm text-[#555] dark:text-[#7b8186]">{{ description }}</p>
              </header>
            </article>
</template>