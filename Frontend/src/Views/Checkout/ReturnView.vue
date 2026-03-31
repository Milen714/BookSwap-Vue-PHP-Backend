<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/utils/axios.js'
import CheckoutSuccessState from '@/components/CheckoutSuccessState.vue'
import CheckoutFailureState from '@/components/CheckoutFailureState.vue'

const route = useRoute()
const loading = ref(true)
const isPaid = ref(false)
const requestId = ref(route.query.requestId || '')
const sessionId = ref('')
const session = ref({
    amount_total: 0,
    payment_status: 'unknown'
})

const checkStatus = async () => {
    try {
        const sid = route.query.session_id
        const requestId = route.query.requestId
        
        if (!sid || !requestId) {
            loading.value = false
            return
        }

        sessionId.value = sid
        const response = await axios.post('/checkout-status', {
            sessionId: sid,
            requestId: requestId
        })
        
        const data = response.data
        if (data.success && data.status === 'complete') {
            isPaid.value = true
            session.value = {
                amount_total: data.amount_total || 0,
                payment_status: 'paid'
            }
        } else {
            isPaid.value = false
            session.value = {
                amount_total: data.amount_total || 0,
                payment_status: data.status || 'unknown'
            }
        }
    } catch (error) {
        isPaid.value = false
        session.value.payment_status = 'error'
    } finally {
        loading.value = false
    }
}

const formattedAmount = computed(() => {
    return (session.value.amount_total / 100).toFixed(2)
})

const shortSessionId = computed(() => {
    return sessionId.value.substring(0, 20) + '...'
})

onMounted(() => {
    checkStatus()
})

</script>

<template>
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Card Container -->
            <div class="bg-neutral-secondary-soft border border-default rounded-2xl p-8 text-center shadow-xl">
                
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-8">
                    <div class="inline-block animate-spin">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <p class="text-body mt-4">Loading checkout status...</p>
                </div>

                <!-- Success State -->
                <CheckoutSuccessState v-else-if="isPaid" :formattedAmount="formattedAmount" 
                :shortSessionId="shortSessionId"
                :requestId="requestId" />

                <!-- Failed State -->
                <CheckoutFailureState v-else :session="session" />

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <router-link to="/" class="flex-1 inline-flex items-center justify-center gap-2 bg-neutral-primary hover:bg-neutral-secondary border border-default text-heading font-medium py-3 px-6 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Back to Home
                    </router-link>
                    <router-link v-if="isPaid" to="/myRequests" class="flex-1 inline-flex items-center justify-center gap-2 bg-neutral-primary hover:bg-neutral-secondary border border-default text-heading font-medium py-3 px-6 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View My Requests
                    </router-link>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="text-center text-body-muted text-sm mt-6">
                Questions? <router-link to="/contact" class="text-fg-brand hover:underline">Contact Support</router-link>
            </p>
        </div>
    </div>
</template>