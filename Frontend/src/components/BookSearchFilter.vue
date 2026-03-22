<script setup>
import { ref, onMounted, computed } from 'vue'
import { useBooksStore } from '@/stores/books.js'
import axios from '@/utils/axios.js'

const booksStore = useBooksStore()

// State
const genre = ref('')
const searchQuery = ref('')
const genres = ref([])
const genreLoading = ref(false)

// Computed
const isSearching = computed(() => booksStore.loading)

// Fetch genres from backend
const fetchGenres = async () => {
  genreLoading.value = true
  try {
    const response = await axios.get('/getAllGenres')
    if (response.data?.success && Array.isArray(response.data.genres)) {
      genres.value = response.data.genres
    }
  } catch (err) {
    console.error('Error fetching genres:', err)
    genres.value = []
  } finally {
    genreLoading.value = false
  }
}

// Perform search
const handleSearch = async () => {
  await booksStore.fetchBooks(genre.value, searchQuery.value, 1)
}

// Handle genre change
const handleGenreChange = async () => {
  await handleSearch()
}

// Handle search input enter key
const handleSearchKeydown = (e) => {
  if (e.key === 'Enter') {
    handleSearch()
  }
}

// Clear filters
const clearFilters = async () => {
  genre.value = ''
  searchQuery.value = ''
  await booksStore.fetchBooks('', '', 1)
}

onMounted(() => {
  fetchGenres()
})
</script>

<template>
  <div class="border-b-2 border-[#2C3233] mb-4">
    <h2 class="text-center m-5 text-3xl font-semibold">Browse & Search Listings</h2>
    <p class="text-center text-md font-semibold text-[#7b8186] mb-6">
      Find your next great read from our diverse collection.
    </p>

    <!-- Search Form -->
    <form
      class="w-[75vw] max-w-screen-xl mx-auto md:flex md:flex-row md:flex-wrap justify-center  mb-4"
      @submit.prevent="handleSearch"
    >
      <!-- Genre Select -->
      <select
        v-model="genre"
        @change="handleGenreChange"
        :disabled="genreLoading"
        class="shrink-0 text-colors bg-colors md:mb-0 mb-4
          hover-color focus:ring-4 focus:ring-neutral-tertiary
          font-medium text-sm px-4 py-2.5 focus:outline-none
          rounded-lg md:rounded-r-none md:rounded-l-lg border border-[#2C3233]
          w-full md:w-auto order-2 md:order-1 cursor-pointer
          disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <option value="">All Genres</option>
        <option v-for="gen in genres" :key="gen" :value="gen">
          {{ gen }}
        </option>
      </select>

      <!-- Search Input and Button -->
      <div class="relative flex shadow-xs rounded-base w-full md:w-auto md:flex-1 order-1 md:order-2">
        <input
          v-model="searchQuery"
          type="search"
          @keydown="handleSearchKeydown"
          placeholder="Search books by Name, Author, or ISBN"
          class="px-3 py-2.5 bg-colors text-colors text-sm
            block w-full placeholder:text-body hover:bg-[#CBCBCB] dark:hover:bg-[#222222]
            border border-[#2C3233]
            focus:outline-none focus:ring-0 focus:border-[#2C3233]
            rounded-l-lg md:rounded-l-none border-r-0"
        />
        <button
          type="submit"
          :disabled="isSearching"
          class="inline-flex items-center text-colors bg-colors hover-color
            focus:ring-4 focus:ring-brand-medium font-medium text-sm px-4 py-2.5
            focus:outline-none rounded-r-lg border border-[#2C3233]
            disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg
            class="w-4 h-4 me-1.5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            fill="none"
            viewBox="0 0 24 24"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-width="2"
              d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
            />
          </svg>
          <span v-if="isSearching">Searching...</span>
          <span v-else>Search</span>
        </button>
      </div>

      <!-- Clear Filters Button -->
      <button
        v-if="genre || searchQuery"
        type="button"
        @click="clearFilters"
        :disabled="isSearching"
        class="text-colors bg-colors hover-color font-medium text-sm px-4 py-2.5
          focus:outline-none rounded-lg border border-[#2C3233]
          disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Clear Filters
      </button>
    </form>

    <!-- Loading Indicator -->
    <p v-if="isSearching" class="text-center text-[#7b8186] mb-4">Loading books...</p>
  </div>
</template>
