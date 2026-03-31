<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import axios from '@/utils/axios.js'
import config from '@/config.js';

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()
const checkoutReady = ref(false)
const errorMessage = ref(null)
const checkoutInstance = ref(null)

function clearCheckout() {
    if (checkoutInstance.value) {
        checkoutInstance.value = null
    }
    const checkoutDiv = document.getElementById('checkout')
    if (checkoutDiv) {
        checkoutDiv.innerHTML = ''
    }
}

async function initializeCheckout() {
    clearCheckout()
    checkoutReady.value = false
    errorMessage.value = null
    
    const requestId = route.query.requestId
    
    if (!authStore.user?.id) {
        errorMessage.value = 'User ID not available'
        return
    }
    
    if (!requestId) {
        errorMessage.value = 'Invalid request ID'
        return
    }
    
    try {
        // Initialize Stripe
        const stripe = Stripe(config.stripePublicKey);
        console.log('Stripe Public Key loaded')

        // Fetch the client secret from backend, passing requestId
        const { data } = await axios.get(`/create-checkout-session?requestId=${requestId}`)
        
        console.log('Client secret received:', data)
        
        if (!data.clientSecret) {
            throw new Error('No client secret in response')
        }
        
        // Initialize embedded checkout (only call once!)
        const checkout = await stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        })
        
        // Store checkout instance
        checkoutInstance.value = checkout
        
        // Mount the checkout
        checkout.mount('#checkout')
        
        checkoutReady.value = true
        
    } catch(error) {
        console.error('Stripe checkout error:', error)
        errorMessage.value = error.message || 'Failed to load checkout'
    }
}

onMounted(() => {
    initializeCheckout()
})

// Watch for route changes to reinitialize checkout
watch(() => route.query.requestId, (newRequestId, oldRequestId) => {
    if (newRequestId !== oldRequestId) {
        initializeCheckout()
    }
})

// Clean up when leaving the page
onBeforeUnmount(() => {
    clearCheckout()
})
</script>

<template>
    <div class="checkout-container">
        <h1>Checkout</h1>
        
        <div v-if="errorMessage" class="error-message text-red-500 p-4 bg-red-100 rounded mb-4">
            {{ errorMessage }}
        </div>
        
        <div v-if="!checkoutReady" class="loading">
            <p>Loading checkout...</p>
        </div>
        
        <div id="checkout"></div>
    </div>
</template>

<style scoped>
.checkout-container {
    max-width: 100%;
    padding: 20px;
}

.error-message {
    margin-bottom: 16px;
}

.loading {
    text-align: center;
    padding: 40px;
    font-size: 18px;
    color: #666;
}
</style>