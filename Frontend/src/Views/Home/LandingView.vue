<script setup>
import HeroSection from '@/components/HeroSection.vue';
import BookPostCard from '@/components/BookPostCard.vue'
import BookDetailsModal from '@/components/BookDetailsModal.vue'
import BookRequestForm from '@/components/BookRequestForm.vue'
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuth } from '@/composables/useAuth.js'

const { authState } = useAuth()
const books = ref([]);
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

const fetchBooks = async () => {
  try {
    const response = await axios.get(`${apiBaseUrl}/getAllBooks`, { withCredentials: true });

    if (response.data?.success && Array.isArray(response.data.books)) {
      books.value = response.data.books;
    } else {
      books.value = [sampleBook];
    }

    console.log('Fetched books:', books.value);
  } catch (error) {
    console.error('Error fetching books:', error);
    books.value = [sampleBook];
  }
};

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
</template>