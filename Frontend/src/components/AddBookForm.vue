<script setup>
import axios from 'axios'
import { BrowserMultiFormatReader } from '@zxing/library';
import { ref, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { useBooksStore } from '@/stores/books.js'
import config from '@/config.js'
import ErrorCard from '@/components/molecules/ErrorCard.vue'
import SuccessCard from '@/components/molecules/SuccessCard.vue'

const videoRef = ref(null);
const isbnRef = ref(null);
const codeReader = ref(null);
const isScanning = ref(false);
const error = ref(null);
const isbn = ref('');

const authStore = useAuthStore()
const booksStore = useBooksStore()

const startScanning = async () => {
  try {
    error.value = null;
    isScanning.value = true;
    codeReader.value = new BrowserMultiFormatReader();
    
    const result = await codeReader.value.decodeOnceFromVideoDevice(
      undefined, 
      videoRef.value
    );
    
    if (result) {
      isbn.value = result.text;
      if (isbnRef.value) {
        isbnRef.value.value = result.text;
      }
      console.log('Barcode found:', result.text);
      await stopScanning();
    }
  } catch (err) {
    console.error('Error scanning:', err);
    if (err.name === 'NotAllowedError') {
      error.value = 'Camera permission denied. Please allow camera access to scan barcodes.';
    } else if (err.name === 'NotFoundError') {
      error.value = 'No camera found on this device.';
    } else {
      error.value = 'Failed to scan barcode. Try again.';
    }
  }
};

const stopScanning = async () => {
  if (codeReader.value) {
    codeReader.value.reset();
  }
  isScanning.value = false;
};

onBeforeUnmount(() => {
  stopScanning();
});

const handleIsbnSubmit = async (event) => {
  event.preventDefault();

  if (!authStore.user?.id) {
    console.log('User ID not available yet')
    return
  }
  if (!isbn.value.trim()) {
    error.value = 'Please enter a valid ISBN.';
    return;
  }
  
  try {
    const response = await axios.post(`${config.apiDomain}/fetchBookPreview`,
      {
        isbn: isbn.value.trim()
      },
      {
        withCredentials: true
      }
    )

    if (response.data.success) {
      // Handle successful book retrieval (e.g., show book details, allow listing)
      console.log('Book data:', response.data.book);
      error.value = null;
    } else {
      throw new Error(response.data.message || 'Book not found.');
    }
  } catch (err) {
    console.error('Error fetching book:', err);
    error.value = err.message || 'Failed to fetch book details. Please try again.';
  }
};

</script>


<template>
    
    <article
        class="StepOne max-w-md mx-auto bg-colors border border-[#ccc] dark:border-[#2C3233] text-colors p-6 rounded-md shadow-md">
        <form id="ISBN-form" method='POST' @submit="handleIsbnSubmit">
             <article class="input_group">
                <h2 class="text-xl font-semibold mb-4 text-colors">Step 1: Enter ISBN</h2>
                <p class="mb-4 text-sm text-colors">You can either enter the ISBN manually or scan the barcode using your camera.</p>
             </article>
            <article class="input_group">
                <label class="input_label text-colors" for="ISBN">ISBN:</label>
                <input 
                  ref="isbnRef"
                  v-model="isbn"
                  class="form_input bg-[#e5e5e5] dark:bg-[#2C3233] text-colors" 
                  type="text" 
                  id="ISBN" 
                  name="isbn"
                  required>
            </article>
            <ErrorCard v-if="error" :message="error" />

            <button id="addBookButton" class="button_primary" type="submit">Add Book</button>
        </form>
        <div class="my-4 border-t border-[#ccc] dark:border-[#2C3233] text-center text-colors font-semibold">OR</div>
        
        <!-- Barcode Scanner Section -->
        <div class="mt-4 flex flex-col items-center">
          <!-- Scanning Interface -->
          <div v-if="isScanning" class="w-full mb-4">
            <div class="relative bg-black rounded-lg overflow-hidden mb-3">
              <video 
                ref="videoRef" 
                style="width: 100%; height: 300px; object-fit: cover;"
              ></video>
              <div class="absolute inset-0 border-2 border-red-500 opacity-50"></div>
            </div>
            <button 
              @click="stopScanning" 
              type="button"
              class="w-full py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
              Cancel Scan
            </button>
          </div>
          
          <!-- Scan Upload Area -->
          <div v-if="!isScanning" class="w-full">
            <label for="scanButton" class="block mb-2.5 text-sm font-medium text-colors">Scan Your Book</label>
            <button 
              @click="startScanning"
              type="button"
              class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#ccc] dark:border-[#2C3233] rounded-lg cursor-pointer bg-[#e5e5e5] dark:bg-[#1a1a1a] hover:bg-[#d5d5d5] dark:hover:bg-[#222222] transition-colors">
              <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-3 text-[#555] dark:text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-sm text-[#555] dark:text-gray-400"><span
                        class="font-semibold text-blue-500">Click to scan</span>
                    barcode with camera</p>
              </div>
            </button>
          </div>
        </div>
    </article>
</template>

