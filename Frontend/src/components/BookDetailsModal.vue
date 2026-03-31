<script setup>
import { computed } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import BookDetailsHeader from '@/components/BookDetailsHeader.vue'
import ModalManual from '@/components/organisms/ModalManual.vue'
import Description from '@/components/molecules/ModalBookDescription.vue'

const props = defineProps({
  book: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['close', 'request-book', 'open-chat'])

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()

const isRequesteeDetailsView = computed(() => route.path.includes('/requesteeDetails'))
const isLoggedIn = computed(() => !!authStore.user)
const userTokens = computed(() => authStore.user?.swapTokens ?? 0)
const isOwnBook = computed(() => {
  if (!isLoggedIn.value) {
    return false
  }
  return props.book?.shared_by?.id === authStore.user?.id
})

const bookDescription = computed(() => props.book?.description || 'No description available.')
const ownerReview = computed(() => props.book?.owner_review || '')

const handleRequestBook = () => {
  emit('request-book', props.book)
}

const openChat = (sharedBy) => {
  router.push(`/chat?recipientId=${sharedBy.id}`)
  console.log('Open chat with owner', sharedBy)
}

const closeModal = () => {
  emit('close')
}
const steps = [
  {'step' : '1.','title': 'Request the book', 'description': 'The current owner will be notified of your interest and can approve or decline your request.'},
  {'step' : '2.','title': 'Owner Accepts', 'description': 'As soon as it\'s approved, pay the postage fee and track your shipment. If both sides agree, a physical meet can be arranged, instead of delivery.'},
  {'step' : '3.','title': 'Track delivery', 'description': 'Follow the shipment status and estimated delivery date. Mark the book as received once it arrives.'}
]
</script>

<template>
  <div class="book-modal mx-auto mt-10 flex w-[90%] flex-col rounded-md bg-colors p-6 shadow-md md:w-3/4">
    <div id="bookOverview" class="StepTwo flex w-full flex-col rounded-md shadow-md">
      <div class="mt-6 flex flex-col gap-4 border-b-2 border-[#2C3233] p-6">
        <BookDetailsHeader :book="book" :isLoggedIn="isLoggedIn" @open-chat="openChat" />

        <Description :bookDescription="bookDescription" title="Book overview" />

        <Description v-if="ownerReview" :bookDescription="ownerReview" title="Owner's Review" />

        <div v-if="!isRequesteeDetailsView">

          <ModalManual Title="How it works" :steps="steps" />

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
