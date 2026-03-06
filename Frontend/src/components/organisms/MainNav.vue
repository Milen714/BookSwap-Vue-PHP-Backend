<script setup>
import { computed, ref } from 'vue'
import axios from 'axios'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

const { authState } = useAuth()

const isMenuOpen = ref(false)   
const isUserMenuOpen = ref(false)
const route = useRoute()
const router = useRouter()
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost'

const isActive = (path) => route.path === path

const userInitials = computed(() => {
    const first = authState.user?.fname?.[0] ?? ''
    const last = authState.user?.lname?.[0] ?? ''
    const initials = `${first}${last}`.toUpperCase()
    return initials || 'U'
})

const credits = computed(() => authState.user?.swapTokens ?? 0)

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value
}

const logout = async () => {
    try {
        await axios.post(`${apiBaseUrl}/logout`, {}, { withCredentials: true })
    } catch (error) {
        console.error('Logout error:', error)
    } finally {
        authState.isLoggedIn = false
        authState.user = null
        isUserMenuOpen.value = false
        router.push('/login')
    }
}
</script>

<template>
    <nav class="fixed top-0 left-0 right-0 z-20 border-b border-gray-300 bg-colors">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between p-4">
            <RouterLink to="/" class="flex items-center gap-3" @click="isMenuOpen = false">
                <img src="/Assets/BookSwapLogo.svg" class="h-7" alt="BookSwap Logo" />
                <span class="text-xl font-semibold text-colors whitespace-nowrap">BookSwap</span>
            </RouterLink>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md text-gray-700 md:hidden"
                aria-controls="navbar-multi-level-dropdown"
                :aria-expanded="isMenuOpen"
                @click="isMenuOpen = !isMenuOpen"
            >
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
                </svg>
            </button>

            <div id="navbar-multi-level-dropdown" class="w-full md:block md:w-auto " :class="isMenuOpen ? 'block' : 'hidden'">
                <ul v-if="!authState.isLoggedIn" class="font-bold text-colors mt-4 flex flex-col items-center gap-2 rounded-lg border border-gray-300 bg-colors p-4 shadow-lg md:mt-0 md:flex-row md:gap-6 md:border-0 md:bg-transparent md:p-0 md:shadow-none">
                    <li>
                        <RouterLink
                            to="/"
                            class="block rounded px-3 py-2 font-bold"
                            :class="isActive('/') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                            Browse
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            to="/login"
                            class="block rounded px-3 py-2"
                            :class="isActive('/login') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                            Login
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            to="/signup"
                            class="block rounded px-3 py-2 text-colors"
                            @click="isMenuOpen = false"
                        >
                            Signup
                        </RouterLink>
                    </li>
                </ul>

                <ul v-else class="font-bold mt-4 flex flex-col items-center gap-2 rounded-lg border border-gray-300 bg-colors p-4 shadow-lg md:mt-0 md:flex-row md:gap-6 md:border-0 md:bg-transparent md:p-0 md:shadow-none">
                    <li>
                        <RouterLink
                            to="/"
                            class="block rounded px-3 py-2"
                            :class="isActive('/') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                            Browse
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            to="/addBook"
                            class="block rounded px-3 py-2"
                            :class="isActive('/addBook') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                           List a Book
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            :to="`/myListings/?id=${authState.user?.id}&status=all`"
                            class="block rounded px-3 py-2"
                            :class="route.path.includes('/myListings') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                            My Listings
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink
                            :to="`/myRequests/?id=${authState.user?.id}&status=all`"
                            class="block rounded px-3 py-2"
                            :class="route.path.includes('/myRequests') ? 'text-blue-600' : 'text-colors'"
                            @click="isMenuOpen = false"
                        >
                            My Requests
                        </RouterLink>
                    </li>
                </ul>

                
            </div>
            <div v-if="authState.isLoggedIn" class="relative mt-3 flex flex-row items-center justify-center gap-2 md:mt-0">
                    <button
                        id="userMenuButton"
                        type="button"
                        :aria-expanded="isUserMenuOpen"
                        class="inline-flex items-center rounded-full bg-[#CBCBCB] p-2 font-semibold text-black hover:bg-[#b5b5b5] focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-[#222222] dark:text-white dark:hover:bg-[#3a3a3a]"
                        @click="toggleUserMenu"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-black dark:bg-[#151819] dark:text-white">
                            {{ userInitials }}
                        </span>
                        <span class="sr-only">Toggle user menu</span>
                    </button>

                    <div
                        v-if="isUserMenuOpen"
                        id="userMenuDropdown"
                        class="absolute right-0 top-full z-50 mt-2 w-44 rounded-lg border border-[#2C3233] bg-[#F2F0EF] shadow-lg dark:bg-[#0F0F0F]"
                    >
                        <ul class="py-1 text-sm text-colors" aria-labelledby="userMenuButton">
                            <li>
                                <RouterLink to="/settings" class="block rounded-md px-4 py-2 hover:bg-[#CBCBCB] dark:hover:bg-[#222222]" @click="isUserMenuOpen = false">
                                    Settings
                                </RouterLink>
                            </li>
                            <li>
                                <button type="button" class="w-full rounded-md px-4 py-2 text-left hover:bg-[#CBCBCB] dark:hover:bg-[#222222]" @click="logout">
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>

                    <span class="whitespace-nowrap font-medium text-colors">
                        {{ credits }} Credits
                    </span>
                </div>
        </div>
    </nav>
</template>
