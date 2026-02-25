<script setup>
import { ref } from 'vue'

const heroTitle = ref('Welcome to BookSwap')
</script>


<template>
    <section class="bg-colors text-colors py-24 mt-24">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Timeline container -->
        <div class="relative w-full flex flex-col md:flex-row md:justify-between items-start">

            <!-- Vertical line (mobile) -->
            <div
                class="absolute left-[39px] -translate-x-1/2 top-0 bottom-0 w-px bg-black/70 dark:bg-white/70 md:hidden">
            </div>

            <!-- Horizontal line (desktop) -->
            <div class="absolute top-[39px] left-0 right-0 h-px bg-black/70 dark:bg-white/70 hidden md:block"></div>

            <!-- STEP 1 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">

                <!-- Dot -->
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">1</span>
                </div>

                <!-- Content -->
                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Browse Books
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Explore our extensive collection of books shared by fellow users.
                    </p>
                </div>

            </div>


            <!-- STEP 2 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">2</span>
                </div>

                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Share Your Books
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Contribute to the community by sharing your own books.
                    </p>
                </div>
            </div>

            <!-- STEP 3 -->
            <div class="relative flex flex-row md:flex-col items-center w-full md:w-auto">
                <div class="flex items-center justify-center
               w-[75px] h-[75px] rounded-full
               border-2 border-black/70 dark:border-white/70 bg-[#F2F0EF] dark:bg-[#0F0F0F] z-10
               shrink-0">
                    <span class="text-colors text-[40px]">3</span>
                </div>

                <div class="ml-6 mt-0 md:ml-0 md:mt-6
               text-left md:text-center max-w-xs">
                    <h3 class="text-xl font-semibold text-colors mb-2">
                        Connect with Readers
                    </h3>
                    <p class="text-black/70 dark:text-white/70">
                        Engage with a community of book lovers and share recommendations.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>
</template>