<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from '@/utils/axios.js'
import AccountSettingsSidebar from '@/components/organisms/AccountSettingsSidebar.vue'
import ProfileInformationForm from '@/components/organisms/ProfileInformationForm.vue'
import ProfileSecurityForm from '@/components/organisms/ProfileSecurityForm.vue'
import AccountAddressForm from '@/components/organisms/AccountAdressFrom.vue'
import SettingsLanding from '@/components/organisms/SettingsLanding.vue'
const router = useRouter()
const route = useRoute()
const showError = ref(false)
const errorMessage = ref('')

</script>

<template>
    <section class="flex flex-col md:flex-row justify-start h-[70vh] mt-4">
        <AccountSettingsSidebar />
        <div class="flex-1 p-6 bg-colors-secondary-light text-colors rounded-lg shadow-md  mx-12 overflow-auto">
            <ProfileInformationForm v-if="route.path.includes('/settings/profile')" />
            <ProfileSecurityForm v-if="route.path.includes('/settings/security')" />
            <AccountAddressForm v-if="route.path.includes('/settings/shipping')" />
            <SettingsLanding v-if="route.path === '/settings'" />
             <div v-if="showError" class="mt-4">
                <p class="text-red-500">{{ errorMessage }}</p>
             </div>

        </div>
    </section>
</template>