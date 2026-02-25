<script setup>
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

const props = defineProps({
  book: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['close', 'request-book'])

const { authState } = useAuth()
const route = useRoute()

const isRequesteeDetailsView = computed(() => route.path.includes('/requesteeDetails'))
const isLoggedIn = computed(() => !!authState.user)
const userTokens = computed(() => authState.user?.swapTokens ?? 0)
const isOwnBook = computed(() => {
  if (!isLoggedIn.value) {
    return false
  }
  return props.book?.shared_by?.id === authState.user?.id
})

const bookDescription = computed(() => props.book?.description || 'No description available.')
const ownerReview = computed(() => props.book?.owner_review || '')

const handleRequestBook = () => {
  emit('request-book', props.book)
}

const closeModal = () => {
  emit('close')
}
</script>

<template>
  <div class="book-modal mx-auto mt-10 flex w-[90%] flex-col rounded-md bg-colors p-6 shadow-md md:w-3/4">
    <div id="bookOverview" class="StepTwo flex w-full flex-col rounded-md shadow-md">
      <div class="mt-6 flex flex-col gap-4 border-b-2 border-[#2C3233] p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row">
          <div class="flex flex-col gap-4 md:flex-row">
            <div
              id="preview_image"
              class="min-h-[160px] min-w-[116px] max-h-[160px] max-w-[116px] bg-center bg-cover"
              :style="{ backgroundImage: `url('${book?.cover_image_url || ''}')` }"
            ></div>

            <div class="flex flex-col items-start justify-start">
              <div class="mb-2 flex flex-col justify-start gap-1">
                <span class="rounded-full border border-[#ccc] bg-[#e5e5e5] px-2 py-1 text-sm font-semibold text-colors dark:border-[#2C3233] dark:bg-[#151819]">
                  ISBN: {{ book?.isbn || '-' }}
                </span>
                <h2 class="text-left text-lg font-semibold leading-none tracking-tight text-colors">
                  {{ book?.title || 'Untitled' }}
                </h2>
              </div>

              <span id="preview_author" class="text-md font-medium text-[#555] dark:text-[#7b8186]">
                {{ book?.author || 'Unknown author' }}
              </span>

              <div class="flex flex-col items-center gap-4">
                <span class="text-md font-medium text-blue-600">
                  Shared by: {{ book?.shared_by?.fname || '' }} {{ book?.shared_by?.lname || '' }}
                </span>
                <span class="rounded-full border border-[#ccc] bg-[#e5e5e5] px-2 py-1 text-sm font-semibold text-colors dark:border-[#2C3233] dark:bg-[#151819]">
                  CONDITION: {{ book?.condition?.label || book?.condition || 'Unknown' }}
                </span>
              </div>
            </div>
          </div>

          <div>
            <div class="flex flex-col items-start">
              <h3 class="mb-2 text-lg font-semibold text-colors">Book Details</h3>
              <ul class="list-inside list-disc text-left text-sm text-[#555] dark:text-[#7b8186]">
                <li><strong>Genre:</strong> {{ book?.genre || '-' }}</li>
                <li><strong>Published Year:</strong> {{ book?.published_year || '-' }}</li>
                <li><strong>Page Count:</strong> {{ book?.page_count || '-' }}</li>
                <li><strong>Owner's Location:</strong> {{ book?.shared_by?.state || '-' }}</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="book-modal-description">
          <h3 class="mb-2 text-lg font-semibold text-colors">Book overview</h3>
          <p class="whitespace-pre-line text-left text-sm text-[#555] dark:text-[#7b8186]">
            {{ bookDescription }}
          </p>
        </div>

        <div v-if="ownerReview" class="book-modal-description">
          <h3 class="mb-2 text-lg font-semibold text-colors">Owner's Review</h3>
          <p class="whitespace-pre-line text-left text-sm text-[#555] dark:text-[#7b8186]">
            {{ ownerReview }}
          </p>
        </div>

        <div v-if="!isRequesteeDetailsView">
          <div class="flex flex-col items-start">
            <h3 class="mb-2 text-lg font-semibold text-colors">How it works</h3>

            <div class="mb-2 ml-4 flex flex-row items-center gap-4">
              <span class="text-lg font-bold text-blue-600">1</span>
              <div class="flex flex-col items-start">
                <h3 class="text-lg font-semibold text-colors">Request the book</h3>
                <p class="text-sm text-[#555] dark:text-[#7b8186]">The current owner will be notified of your interest and can approve or decline your request.</p>
              </div>
            </div>

            <div class="mb-2 ml-4 flex flex-row items-center gap-4">
              <span class="text-lg font-bold text-blue-600">2</span>
              <div class="flex flex-col items-start">
                <h3 class="text-lg font-semibold text-colors">Owner Accepts</h3>
                <p class="text-left text-sm text-[#555] dark:text-[#7b8186]">As soon as it's approved, pay the postage fee and track your shipment. If both sides agree, a physical meet can be arranged, instead of delivery.</p>
              </div>
            </div>

            <div class="mb-2 ml-4 flex flex-row items-center gap-4">
              <span class="text-lg font-bold text-blue-600">3</span>
              <div class="flex flex-col items-start">
                <h3 class="text-lg font-semibold text-colors">Track delivery</h3>
                <p class="text-sm text-[#555] dark:text-[#7b8186]">Follow the shipment status and estimated delivery date. Mark the book as received once it arrives.</p>
              </div>
            </div>
          </div>

          <div class="mt-4 flex flex-col gap-4 md:flex-row">
            <button class="button_neutral" type="button" @click="closeModal">Close</button>

            <button
              v-if="isOwnBook"
              disabled
              class="button_disabled"
              title="You cannot request your own book."
            >
              <span class="text-red-500">You cannot request your own book</span>
            </button>

            <button
              v-else-if="isLoggedIn && userTokens > 0"
              class="button_primary"
              type="button"
              @click="handleRequestBook"
            >
              Request Book
            </button>

            <RouterLink
              v-else-if="!isLoggedIn"
              to="/login"
              class="button_disabled"
              title="You need to be logged in to request a book."
            >
              <span class="text-red-500">Login to request</span>
            </RouterLink>

            <button
              v-else
              class="button_disabled"
              disabled
              title="You need at least 1 swap token to request a book."
            >
              <span class="text-red-500">Insufficient swap tokens</span>
            </button>
          </div>
        </div>

        <div v-else class="mt-4 flex flex-col gap-4 md:flex-row">
          <button class="button_neutral" type="button" @click="closeModal">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>
