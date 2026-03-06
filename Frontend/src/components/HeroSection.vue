<script setup>
import { ref } from 'vue'
import Step from '@/components/molecules/HeroStep.vue'

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

             <Step
                step="1"
                title="Browse Books"
                description="Explore our extensive collection of books shared by fellow users."/>
            
            <Step
                step="2"
                title="Share Your Books"
                description="Contribute to the community by sharing your own books."/>
            
            <Step
                step="3"
                title="Connect with Readers"
                description="Engage with a community of book lovers and share recommendations."/>

            <Step
                step="4"
                title="Start Swapping"
                description="Request books from other users and arrange swaps to enjoy new reads."/>

        </div>

    </div>
</section>
</template>