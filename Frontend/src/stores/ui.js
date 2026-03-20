import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUIStore = defineStore('ui', () => {
  // State - Modal visibility
  const isBookModalOpen = ref(false)
  const isBookDetailsOpen = ref(false)
  const isBookRequestFormOpen = ref(false)
  const isUserMenuOpen = ref(false)
  const isNavMenuOpen = ref(false)

  // State - Filters and pagination
  const bookFilters = ref({
    genre: '',
    search: '',
    page: 1,
  })

  const requestFilters = ref({
    status: 'all',
  })

  const listingFilters = ref({
    status: 'all',
  })

  // State - UI preferences
  const sidebarCollapsed = ref(false)

  // Actions - Modal Management
  /**
   * Open book details modal
   */
  function openBookModal() {
    isBookModalOpen.value = true
    isBookDetailsOpen.value = true
    isBookRequestFormOpen.value = false
  }

  /**
   * Show book request form (hide details)
   */
  function showBookRequestForm() {
    isBookDetailsOpen.value = false
    isBookRequestFormOpen.value = true
  }

  /**
   * Close all modals
   */
  function closeBookModal() {
    isBookModalOpen.value = false
    isBookDetailsOpen.value = false
    isBookRequestFormOpen.value = false
  }

  /**
   * Toggle user menu visibility
   */
  function toggleUserMenu() {
    isUserMenuOpen.value = !isUserMenuOpen.value
  }

  /**
   * Close user menu
   */
  function closeUserMenu() {
    isUserMenuOpen.value = false
  }

  /**
   * Toggle navigation menu visibility
   */
  function toggleNavMenu() {
    isNavMenuOpen.value = !isNavMenuOpen.value
  }

  /**
   * Close navigation menu
   */
  function closeNavMenu() {
    isNavMenuOpen.value = false
  }

  // Actions - Filter Management
  /**
   * Update book search filters
   * @param {Object} filters - Filters object { genre?, search?, page? }
   */
  function setBookFilters(filters) {
    bookFilters.value = {
      ...bookFilters.value,
      ...filters,
    }
  }

  /**
   * Reset book filters
   */
  function resetBookFilters() {
    bookFilters.value = {
      genre: '',
      search: '',
      page: 1,
    }
  }

  /**
   * Update request status filter
   * @param {string} status - Status filter value
   */
  function setRequestFilter(status) {
    requestFilters.value.status = status
  }

  /**
   * Update listing status filter
   * @param {string} status - Status filter value
   */
  function setListingFilter(status) {
    listingFilters.value.status = status
  }

  /**
   * Set current page for pagination
   * @param {number} page - Page number
   */
  function setCurrentPage(page) {
    bookFilters.value.page = page
  }

  // Actions - UI Preferences
  /**
   * Toggle sidebar collapse state
   */
  function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
  }

  /**
   * Set sidebar collapse state
   * @param {boolean} collapsed - Whether sidebar should be collapsed
   */
  function setSidebarCollapsed(collapsed) {
    sidebarCollapsed.value = collapsed
  }

  /**
   * Reset all UI state to defaults
   */
  function resetUIState() {
    isBookModalOpen.value = false
    isBookDetailsOpen.value = false
    isBookRequestFormOpen.value = false
    isUserMenuOpen.value = false
    isNavMenuOpen.value = false
    sidebarCollapsed.value = false
    bookFilters.value = {
      genre: '',
      search: '',
      page: 1,
    }
    requestFilters.value = {
      status: 'all',
    }
    listingFilters.value = {
      status: 'all',
    }
  }

  return {
    // State - Modals
    isBookModalOpen,
    isBookDetailsOpen,
    isBookRequestFormOpen,
    isUserMenuOpen,
    isNavMenuOpen,
    // State - Filters
    bookFilters,
    requestFilters,
    listingFilters,
    // State - Preferences
    sidebarCollapsed,
    // Actions - Modals
    openBookModal,
    showBookRequestForm,
    closeBookModal,
    toggleUserMenu,
    closeUserMenu,
    toggleNavMenu,
    closeNavMenu,
    // Actions - Filters
    setBookFilters,
    resetBookFilters,
    setRequestFilter,
    setListingFilter,
    setCurrentPage,
    // Actions - Preferences
    toggleSidebar,
    setSidebarCollapsed,
    // Actions - Reset
    resetUIState,
  }
})
