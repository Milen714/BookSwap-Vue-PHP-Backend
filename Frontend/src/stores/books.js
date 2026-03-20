import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '@/utils/axios.js'

export const useBooksStore = defineStore('books', () => {
  // State
  const books = ref([])
  const selectedBook = ref(null)
  const currentPage = ref(1)
  const hasNextPage = ref(false)
  const loading = ref(false)
  const error = ref(null)
  const previewBook = ref(null)
  const previewLoading = ref(false)
  const previewError = ref(null)

  // Actions
  /**
   * Fetch books from API with optional filters
   * @param {string} genre - Filter by genre
   * @param {string} search - Search term
   * @param {number} page - Page number for pagination
   */
  async function fetchBooks(genre = '', search = '', page = 1) {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get(
        `/getAllBooks?genre=${encodeURIComponent(
          genre
        )}&search=${encodeURIComponent(search)}&page=${page}`
      )

      if (response.data?.success && Array.isArray(response.data.books)) {
        books.value = response.data.books
        currentPage.value = response.data.currentPage || 1
        hasNextPage.value = response.data.hasNextPage || false
      } else {
        error.value = 'Failed to fetch books'
        books.value = []
      }
    } catch (err) {
      console.error('Error fetching books:', err)
      error.value = err.message || 'Failed to fetch books'
      books.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Set the currently selected book
   * @param {Object} book - The book object to select
   */
  function selectBook(book) {
    selectedBook.value = book
  }

  /**
   * Clear the selected book
   */
  function clearSelectedBook() {
    selectedBook.value = null
  }

  /**
   * Clear all book state
   */
  function clearBooks() {
    books.value = []
    selectedBook.value = null
    currentPage.value = 1
    hasNextPage.value = false
    error.value = null
  }

  /**
   * Fetch book preview by ISBN
   * @param {string} isbn - The ISBN of the book to fetch
   */
  async function fetchBookPreview(isbn) {
    previewLoading.value = true
    previewError.value = null
    previewBook.value = null

    try {
      const response = await axios.post(
        `/fetchBookPreview`,
        { isbn }
      )

      if (response.data?.success && response.data.book) {
          previewBook.value = response.data.book
          console.log('Fetched book preview:', previewBook.value)
      } else {
        previewError.value = response.data?.message || 'Failed to fetch book preview'
      }
    } catch (err) {
      console.error('Error fetching book preview:', err)
      previewError.value = err.message || 'Failed to fetch book preview'
    } finally {
      previewLoading.value = false
    }
  }

  /**
   * Clear preview book state
   */
  function clearPreview() {
    previewBook.value = null
    previewError.value = null
    previewLoading.value = false
  }

  return {
    // State
    books,
    selectedBook,
    currentPage,
    hasNextPage,
    loading,
    error,
    previewBook,
    previewLoading,
    previewError,
    // Actions
    fetchBooks,
    selectBook,
    clearSelectedBook,
    clearBooks,
    fetchBookPreview,
    clearPreview,
  }
})
