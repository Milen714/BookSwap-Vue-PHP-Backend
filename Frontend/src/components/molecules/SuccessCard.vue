<script setup>
const props = defineProps({
    message: {
        type: String,
        default: 'Operation completed successfully.',
        required: true,
    }
});
</script>

<template>
    <div class="mb-4 p-4 bg-green-300 text-green-900 border border-green-400 rounded">
        <h2 class="text-lg font-semibold mb-2">Success</h2>
        <p class="text-sm">{{ props.message }}</p>
    </div>
</template>