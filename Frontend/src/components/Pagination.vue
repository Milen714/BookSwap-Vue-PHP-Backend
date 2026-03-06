<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
    hasNextPage: {
        type: Boolean,
        required: true
    },
    currentPage: {
        type: Number,
        required: true
    },
    genre: {
        type: String,
        default: ''
    },
    search: {
        type: String,
        default: ''
    }
})
const emit = defineEmits(['page-change'])

const goToPreviousPage = () => {
    if (props.currentPage > 1) {
        emit('page-change', props.currentPage - 1)
    }
}

const goToNextPage = () => {
    if (props.hasNextPage) {
        emit('page-change', props.currentPage + 1)
    }
}
</script>

<template>
    <nav class="w-full flex justify-center mt-6">
        <div class="ul-wrap">
            <ul
                class="flex flex-row justify-center gap-2 bg-[#e5e5e5] dark:bg-[#222222] px-1 py-1 rounded-full whitespace-nowrap text-colors font-semibold">
                <li :class="`${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`" 
                class="hover:bg-[#d5d5d5] hover:dark:bg-[#0F0F0F] hover:text-black hover:dark:text-white rounded-full px-4 py-2 text-colors font-semibold hover:animate-pulse">
                    <RouterLink :to="`/?genre=${encodeURIComponent(props.genre ?? '')}&search=${encodeURIComponent(props.search ?? '')}&page=${props.currentPage == 1 ? 1 : props.currentPage - 1}`"
                        >
                        <span>
                            < </span>
                    </RouterLink>
                </li>
               
                <li :class="`${hasNextPage === true ? '' : 'opacity-50 cursor-not-allowed pointer-events-none'}`" 
                class="hover:bg-[#d5d5d5] hover:dark:bg-[#0F0F0F] hover:text-black hover:dark:text-white rounded-full px-4 py-2 text-colors font-semibold hover:animate-pulse">
                    <RouterLink :to="`/?genre=${encodeURIComponent(props.genre ?? '')}&search=${encodeURIComponent(props.search ?? '')}&page=${props.currentPage == props.totalPages ? props.currentPage : props.currentPage + 1}`"
>
                        <span>
                            > </span>
                    </RouterLink>
                </li>
            </ul>
            <input id="page" type="hidden" value="<?php echo htmlspecialchars($paginatedBooks->currentPage) ?>">
        </div>
    </nav>
</template>