<script setup>
import { computed, ref } from 'vue'
import axios from '@/utils/axios.js'
import { useBooksStore } from '@/stores/books.js'
import { useAuthStore } from '@/stores/auth.js'
import config from '@/config.js'

const authStore = useAuthStore()
const booksStore = useBooksStore()
const isSubmitting = ref(false)
const submitError = ref(null)
const submitSuccess = ref(false)

// Form data refs
const condition = ref('New')
const userReview = ref('')

const book = computed(() => booksStore.previewBook)

const handleFormSubmit = async (e) => {
  e.preventDefault()
  
  if (!book.value) {
    submitError.value = 'No book selected'
    return
  }

  isSubmitting.value = true
  submitError.value = null

  try {
    const formData = {
      isbn: book.value.isbn || book.value.ISBN,
      condition: condition.value,
      userReview: userReview.value,
      userId: authStore.user?.id
    }

    const response = await axios.post(
      `${config.apiDomain}/addBook`,
      formData,
      { withCredentials: true }
    )

    if (response.data?.success) {
      submitSuccess.value = true
      // Reset form
      condition.value = 'New'
      userReview.value = ''
      booksStore.clearPreview()
      // Redirect after success
      setTimeout(() => {
        window.location.href = '/'
      }, 1500)
    } else {
      submitError.value = response.data?.message || 'Failed to add book'
    }
  } catch (err) {
    console.error('Error submitting form:', err)
    submitError.value = err.response?.data?.message || err.message || 'Failed to add book'
  } finally {
    isSubmitting.value = false
  }
}

</script>

<template>
    <div v-if="book" class="StepTwo flex flex-col mt-10 w-3/4 mx-auto p-6 rounded-md shadow-md bg-colors-secondary-light text-colors">

        <h2 class="text-xl font-bold text-black dark:text-white" id="preview_title">{{ book.title || 'Title' }}</h2>

        <div class="w-full mt-4 flex flex-col">
            <div class="mb-1 text-base font-medium text-colors">Rating</div>
            <div class="w-2/4 bg-colors-secondary-light rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: (book.rating || 0) * 10 + '%' }"></div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4 p-6 border border-[#ccc] dark:border-[#2C3233] rounded-md mt-6">
            <div 
                id="preview_image" 
                class="min-w-[116px] max-w-[116px] min-h-[160px] max-h-[160px] bg-center bg-cover rounded"
                :style="{ backgroundImage: `url('${book.thumbnail_image_url || ''}')` }">
            </div>
            <div class="">
                <div class="flex flex-row mb-2">
                    <span class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-black dark:text-gray-200">
                        ISBN: <span id="preview_isbn">{{ book.isbn || book.ISBN || 'N/A' }}</span>
                    </span>
                </div>
                <span id="preview_author" class="font-bold text-black dark:text-white">{{ book.author || book.authors?.[0] || 'Unknown Author' }}</span>

                <p id="preview_description" class="text-sm text-[#555] dark:text-[#7b8186] mb-2">
                    {{ book.description || book.summary || 'No description available' }}
                </p>
            </div>
        </div>

        <form @submit="handleFormSubmit" id="addBook-Form">
            <div v-if="submitError" class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 rounded">
                {{ submitError }}
            </div>
            <div v-if="submitSuccess" class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 rounded">
                Book added successfully! Redirecting...
            </div>

            <!-- User choices -->
            <div class="mt-4 mb-4 pb-4">
                <label for="condition" class="block mb-2.5 text-sm font-medium text-black dark:text-white">Book Condition:</label>
                <select 
                    v-model="condition"
                    name="condition" 
                    id="condition"
                    class="block w-1/4 px-3 py-2.5 bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] text-black dark:text-white text-md rounded-md focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"
                    required>
                    <option value="New">Like New</option>
                    <option value="Good">Gently Used</option>
                    <option value="Fair">Used</option>
                    <option value="Poor">Heavily Used</option>
                </select>
            </div>

            <div class="mt-4 mb-4 pb-4">
                <label for="userReview" class="block mb-2.5 text-sm font-medium text-black dark:text-white">Your Review: (optional)</label>
                <p class="text-sm text-[#555] dark:text-[#7b8186] mb-2">Share why this book matters and you might spark an instant swap.</p>
                <textarea 
                    v-model="userReview"
                    name="userReview" 
                    id="userReview"
                    class="block w-full px-3 py-2.5 bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] text-black dark:text-white text-md rounded-md focus:ring-brand focus:border-brand shadow-xs placeholder:text-body"></textarea>
            </div>

            <button 
                class="button_primary" 
                type="submit"
                :disabled="isSubmitting">
                {{ isSubmitting ? 'Saving...' : 'Save Book' }}
            </button>
        </form>
    </div>
</template>