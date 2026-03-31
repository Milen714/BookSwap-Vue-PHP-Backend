<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const prop = defineProps({
    session: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <div class="mb-6">
                        <div class="w-20 h-20 mx-auto bg-red-500/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-heading mb-2">Payment Not Completed</h1>
                        <p class="text-body">Something went wrong with your payment. Please try again.</p>
                    </div>

                    <!-- Error Details -->
                    <div class="bg-neutral-primary rounded-xl p-4 mb-6 text-left">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-body-muted text-sm">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                {{ session.payment_status.charAt(0).toUpperCase() + session.payment_status.slice(1) }}
                            </span>
                        </div>
                    </div>

                    <router-link :to="`/checkout?requestId=${route.query.requestId}`" class="inline-flex items-center justify-center gap-2 w-full bg-brand hover:bg-brand-hover text-white font-medium py-3 px-6 rounded-xl transition-colors mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Try Again
                    </router-link>
</template>