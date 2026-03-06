<script setup>
import { ref, computed } from 'vue'
const props = defineProps({
    bookDescription: {
        type: String,
        required: true
    },
    title: {
        type: String,
        required: true
    }
})

const showFullDescription = ref(false)
const truncatedDescription = computed(() => {
    let description = props.bookDescription
    if (!showFullDescription.value) {
        description = description.substring(0, 200) + '...'
    }
    return description
})

const toggleFullDescription = () => {
    showFullDescription.value = !showFullDescription.value
}
</script>

<template>

    <article class="book-modal-description">
          <h3 class="mb-2 text-lg font-semibold text-colors">{{ title }}</h3>
          <p class="whitespace-pre-line text-left text-sm text-[#555] dark:text-[#7b8186]">
            {{ truncatedDescription }}
          </p>
          <button
                  @click="toggleFullDescription"
                  class="text-green-500 hover:text-green-700 text-sm mt-2"
                >
                  {{ showFullDescription ? 'Show Less' : 'Show More' }}
                </button>
        </article>
</template>