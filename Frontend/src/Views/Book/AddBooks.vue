<script setup>
import { computed } from 'vue'
import AddBookForm from '@/components/organisms/AddBookForm.vue'
import Spinner from '@/components/molecules/Spinner.vue'
import BookPreview from '@/components/BookPreview.vue'
import { useBooksStore } from '@/stores/books.js'

const booksStore = useBooksStore()
const isLoading = computed(() => booksStore.previewLoading)
const hasPreview = computed(() => !!booksStore.previewBook)
</script>

<template>
    <section class="h-min">
        <header>
            <h1 class="text-center m-5 text-3xl font-semibold text-colors">List Your Book</h1>
        </header>
        <AddBookForm />
        <Spinner v-if="isLoading" />
        <BookPreview v-if="hasPreview" />
    </section>
</template>
