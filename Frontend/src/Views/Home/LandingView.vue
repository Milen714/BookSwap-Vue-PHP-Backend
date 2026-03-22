<script setup>
import HeroSection from '@/components/HeroSection.vue';
import BookPostCard from '@/components/BookPostCard.vue'
import BookDetailsModal from '@/components/BookDetailsModal.vue'
import BookRequestForm from '@/components/BookRequestForm.vue'
import Pagination from '@/components/Pagination.vue'
import Filter from '@/components/BookSearchFilter.vue'
import { onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js'
import { useBooksStore } from '@/stores/books.js'
import { useUIStore } from '@/stores/ui.js'

const route = useRoute();
const authStore = useAuthStore()
const booksStore = useBooksStore()
const uiStore = useUIStore()

const openBookDetails = (book) => {
  booksStore.selectBook(book)
  uiStore.openBookModal()
}

const closeBookDetails = () => {
  booksStore.clearSelectedBook()
  uiStore.closeBookModal()
}

const handleRequestBook = (book) => {
  console.log('Request book clicked for book:', book)
  uiStore.showBookRequestForm()
  
}

const handlePageChange = watch(() => route.query, (newQuery) => {
  const genre = newQuery.genre || '';
  const search = newQuery.search || '';
  const page = parseInt(newQuery.page) || 1;
  booksStore.fetchBooks(genre, search, page);
}, { immediate: true });

onMounted(() => {
  booksStore.fetchBooks();
});

</script>

<template>
  <HeroSection />
  <section class="mx-auto max-w-6xl">
    <h1 class="mb-6 text-center text-2xl font-bold text-colors">Welcome to BookSwap</h1>
    <Filter />
    <div class="flex justify-center flex-wrap gap-6">
      <BookPostCard
        v-for="book in booksStore.books"
        :key="book.id"
        :book="book"
        @get-book="openBookDetails"
      />
    </div>

    <section
      v-if="uiStore.isBookModalOpen && booksStore.selectedBook"
      class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-8"
      @click.self="closeBookDetails"
    >
      <BookDetailsModal
      v-if="uiStore.isBookDetailsOpen"
        :book="booksStore.selectedBook"
        @close="closeBookDetails"
        @request-book="handleRequestBook"
      />
      <BookRequestForm
      v-if="uiStore.isBookRequestFormOpen"
        :bookId="booksStore.selectedBook.id"
        :ownerId="booksStore.selectedBook.shared_by.id"
        :requesterId="authStore.user.id"
        @close="closeBookDetails"

        
      />
    </section>
  </section>
  <Pagination
    :has-next-page="booksStore.hasNextPage"
    :current-page="booksStore.currentPage"
    @page-change="handlePageChange"
  />
</template>