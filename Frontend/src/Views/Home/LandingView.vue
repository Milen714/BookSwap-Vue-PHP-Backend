<script setup>
import HeroSection from '@/components/HeroSection.vue';
import BookPostCard from '@/components/BookPostCard.vue'
import BookDetailsModal from '@/components/BookDetailsModal.vue'
import BookRequestForm from '@/components/BookRequestForm.vue'
import Pagination from '@/components/Pagination.vue'
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth.js'

const route = useRoute();
const { authState } = useAuth()
const books = ref([]);
const hasNextPage = ref(false);
const currentPage = ref(1);
const selectedBook = ref(null)
const isBookModalOpen = ref(false)
const showBookDetails = ref(false)
const showRequestForm = ref(false)
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost';

const openBookDetails = (book) => {
  selectedBook.value = book
  isBookModalOpen.value = true
  showBookDetails.value = true
}

const closeBookDetails = () => {
  isBookModalOpen.value = false
  selectedBook.value = null
  showBookDetails.value = false
  showRequestForm.value = false
}

const handleRequestBook = (book) => {
  console.log('Request book clicked for book:', book)
  showBookDetails.value = false
  showRequestForm.value = true
  
}

const fetchBooks = async (genre = '', search = '', page = 1) => {
  try {
    const response = await axios.get(`${apiBaseUrl}/getAllBooks?genre=${encodeURIComponent(genre)}&search=${encodeURIComponent(search)}&page=${page}`, { withCredentials: true });

    if (response.data?.success && Array.isArray(response.data.books)) {
      books.value = response.data.books;
      console.log('Has next page:', response.data.hasNextPage);
      console.log('Current page:', response.data.currentPage);
      currentPage.value = response.data.currentPage || 1;
      hasNextPage.value = response.data.hasNextPage || false;
    } else {
      books.value = [sampleBook];
    }

    console.log('Fetched books:', books.value);
  } catch (error) {
    console.error('Error fetching books:', error);
    books.value = [sampleBook];
  }
};

const handlePageChange = watch(() => route.query, (newQuery) => {
  const genre = newQuery.genre || '';
  const search = newQuery.search || '';
  const page = parseInt(newQuery.page) || 1;
  fetchBooks(genre, search, page);
  currentPage.value = page;
}, { immediate: true });

onMounted(() => {
  fetchBooks();
});

</script>

<template>
  <HeroSection />
  <section class="mx-auto max-w-6xl">
    <h1 class="mb-6 text-center text-2xl font-bold text-colors">Welcome to BookSwap</h1>
    <div class="flex justify-center flex-wrap gap-6">
      <BookPostCard
        v-for="book in books"
        :key="book.id"
        :book="book"
        @get-book="openBookDetails"
      />
    </div>

    <div
      v-if="isBookModalOpen && selectedBook"
      class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-8"
      @click.self="closeBookDetails"
    >
      <BookDetailsModal
      v-if="showBookDetails"
        :book="selectedBook"
        @close="closeBookDetails"
        @request-book="handleRequestBook"
      />
      <BookRequestForm
      v-if="showRequestForm"
        :bookId="selectedBook.id"
        :ownerId="selectedBook.shared_by.id"
        :requesterId="authState.user.id"
        @close="closeBookDetails"

        
      />
    </div>
  </section>
  <Pagination
    :has-next-page="hasNextPage"
    :current-page="currentPage"
    @page-change="handlePageChange"
  />
</template>