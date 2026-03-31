<script setup>
import { useRoute } from 'vue-router'
const route = useRoute()
</script>

<template>
    <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gradient-to-b from-neutral-primary to-neutral-secondary">
        <div class="max-w-md w-full text-center">
            <!-- Error Card -->
            <div class="bg-neutral-secondary-soft border border-default rounded-2xl p-8 shadow-xl">
                
                <!-- 404 Display -->
                <div class="mb-8">
                    <div class="text-8xl font-black text-brand/30 mb-4">404</div>
                    <h1 class="text-3xl font-bold text-heading mb-3">Page Not Found</h1>
                    <p class="text-body text-lg">Sorry, the page you're looking for doesn't exist or has been moved.</p>
                </div>

                <!-- Illustration -->
                <div class="mb-8">
                    <svg class="w-32 h-32 mx-auto text-body-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <router-link 
                        to="/" 
                        class="inline-flex items-center justify-center gap-2 w-full bg-brand hover:bg-brand-hover text-white font-medium py-3 px-6 rounded-xl transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Home
                    </router-link>
                    <router-link 
                        to="/contact" 
                        class="inline-flex items-center justify-center gap-2 w-full bg-neutral-primary hover:bg-neutral-secondary border border-default text-heading font-medium py-3 px-6 rounded-xl transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Support
                    </router-link>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="text-center text-body-muted text-sm mt-6">
                Error Code: 404
            </p>
        </div>
    </div>
</template>