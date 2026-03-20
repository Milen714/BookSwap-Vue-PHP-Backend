<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import axios from '@/utils/axios.js'
import config from '@/config.js';

const authStore = useAuthStore()
const route = useRoute()
onMounted(async () => {
    if (!authStore.user?.id) {
        console.log('User ID not available yet')
        return
    }
    try{
        const stripe = Stripe(config.stripePublicKey);

        const { data } = await axios.get(`/create-checkout-session`);
        
        stripe.initEmbeddedCheckout({
            clientSecret: data.clientSecret
        }).then(checkout => {
            checkout.mount('#checkout');
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