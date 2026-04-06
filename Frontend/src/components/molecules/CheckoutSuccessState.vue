<script setup>
import ModalManual from '@/components/organisms/ModalManual.vue'
import { useBookRequestsStore } from '@/stores/bookRequests'
import { onMounted, ref, watch, computed } from 'vue'
import BookPostCard from '@/components/molecules/BookPostCard.vue'

const bookRequestsStore = useBookRequestsStore()  
const props = defineProps({
    requestId: {
        type: Number,
        required: true
    },
    formattedAmount: {
        type: String,
        required: true
    },
    shortSessionId: {
        type: String,
        required: true
    }
})

const book = ref(null)

onMounted(async () => {
    await bookRequestsStore.fetchRequestById(props.requestId)
    book.value = bookRequestsStore.currentRequest?.book || null
})
const steps = [
  {'step' : '1.','title': '', 'description': 'The book owner has been notified'},
  {'step' : '2.','title': '', 'description': 'Your book will be shipped soon'},
  {'step' : '3.','title': '', 'description': 'You\'ll receive tracking information via email'}
]
</script>

<template>
    <div class="mb-6">
                        <div class="w-20 h-20 mx-auto bg-green-500/20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-heading mb-2">Payment Successful!</h1>
                        <p class="text-body">Your transaction has been completed successfully.</p>
                    </div>

                    <div class="flex justify-center mb-6">
                        <BookPostCard v-if="!bookRequestsStore.loading && book" :book="book" :isLoggedIn="true" />
                    </div>

                    <!-- Order Details -->
                    <div class="bg-neutral-primary rounded-xl p-4 mb-6 text-left">
                        <div class="flex justify-between items-center py-2 border-b border-default">
                            <span class="text-body-muted text-sm">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                Paid
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-default">
                            <span class="text-body-muted text-sm">Amount</span>
                            <span class="text-heading font-medium">€{{ formattedAmount }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-body-muted text-sm">Transaction ID</span>
                            <span class="text-body text-xs font-mono">{{ shortSessionId }}</span>
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-6 text-left">
                        <ModalManual Title="What's next?" :steps="steps" />
                    </div>
</template>