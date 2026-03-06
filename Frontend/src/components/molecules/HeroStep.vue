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
    <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">{{ step }}</span>
                </div>

                <article class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        {{ title }}
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        {{ description }}
                    </p>
                </article>
            </div>
</template>