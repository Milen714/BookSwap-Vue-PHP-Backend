<script setup>
import { ref } from 'vue'   
import { useRouter, useRoute } from 'vue-router'
import SettingsMenuItem from '@/components/molecules/SettingsMenuItem.vue'
import { useAuthStore } from '@/stores/auth.js'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
</script>

<template>
    <aside class="max-w-xs  bg-colors p-6 rounded-lg shadow-md text-colors mb-6 md:mb-0">
            <header class="mb-5">
                <h2 class="text-2xl font-bold mb-4">Account Settings</h2>
                <p class="mb-4">Manage your personal information, security preferences, and shipping details to ensure a seamless book-swapping experience.</p>
            </header>
            <ul>
                <SettingsMenuItem 
                  path="/settings/profile" 
                  icon="pi pi-user" 
                  label="Profile Information" 
                />
                <SettingsMenuItem 
                  path="/settings/security" 
                  icon="pi pi-shield" 
                  label="Security Settings" 
                />
                <SettingsMenuItem 
                  path="/settings/shipping" 
                  icon="pi pi-map-marker" 
                  label="Shipping Addresses" 
                />
            </ul>
        </aside>
</template>