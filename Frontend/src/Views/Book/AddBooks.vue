<script setup>
import AddBook from '@/components/AddBookForm.vue'
import Spinner from '@/components/molecules/Spinner.vue'
import BookPreview from '@/components/BookPreview.vue'
</script>

<template>
    <section class="h-min">
        <header>
            <h1 class="text-center m-5 text-3xl font-semibold text-colors">List Your Book</h1>
        </header>
        <AddBook />
        <Spinner />
        <BookPreview />
    </section>
</template>
