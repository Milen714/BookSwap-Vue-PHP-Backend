<script setup>
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost';
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ref, onMounted, watch } from 'vue'
import { useAuth } from '@/composables/useAuth.js'
const { authState } = useAuth()
const route = useRoute()
onMounted(() => {
    if (!authState.user?.id) {
        console.log('User ID not available yet')
        return
    }
    try{
        const stripe = Stripe('pk_test_51Sg6KdK2gTp4lSWgeXRs1IOOJnXABzgGD7OOQfiC90hmQPgLqI4XegSnvd2ykeu2LeJb6plpqMxL4ZmsH6vXvWf200YJ4JyDGd');

        fetch(`${apiBaseUrl}/create-checkout-session`, {
            method: 'GET',
            credentials: 'include',
        })
        .then(res => res.json())
        .then(data => {
            stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        }).then(checkout => {
            checkout.mount('#checkout');
        });
    });
        
    }catch(error){
        console.log('Error fetching user data:', error.message || error);
    }
})
</script>

<template>
    <h1>Checkout Page</h1>
  <div id="checkout"></div>
</template>