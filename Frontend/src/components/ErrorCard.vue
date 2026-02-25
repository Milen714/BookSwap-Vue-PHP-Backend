<script setup>
const props = defineProps({
    message: {
        type: String,
        default: 'An error occurred while processing your request. Please try again later.'
    }
});
</script>

<template>
    <div class="mt-4 mb-4 p-4 bg-red-300 text-red-900 border border-red-400 rounded">
        <h2 class="text-lg font-semibold mb-2">Error</h2>
        <p class="text-sm">{{ props.message }}</p>
    </div>
</template>