<script setup>
import { computed, ref } from 'vue'
import axios from '@/utils/axios.js'
import { useAuthStore } from '@/stores/auth.js'
import { useBookRequestsStore } from '@/stores/bookRequests.js'
import BookPostCard from '@/components/BookPostCard.vue'
import router from '@/Router'

const props = defineProps({
  request: {
    type: Object,
    required: true,
  },
  reverse: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['pay-shipping', 'cancel-request', 'report-issue'])

const authStore = useAuthStore()
const bookRequestsStore = useBookRequestsStore()
const isUpdating = ref(false)
const errorMessage = ref(null)
const successMessage = ref(null)

const isAlternate = computed(() => {
  return props.reverse
})

const flexReverse = computed(() => {
  return props.reverse ? 'md:flex-row-reverse items-start' : 'md:flex-row items-start'
})

const isOwner = computed(() => {
  if (!authStore.user) return false
  return authStore.user.id === props.request?.owner?.id
})

const statusSteps = ['SHIPPINGPAID', 'SHIPPED', 'DELIVERED', 'COMPLETED']

const currentStatusIndex = computed(() => {
  const index = statusSteps.indexOf(props.request?.status)
  return index === -1 ? -1 : index
})

const stepStyles = computed(() => {
  return statusSteps.map((_, idx) => {
    if (idx <= currentStatusIndex.value) {
      return 'completed flex flex-col items-center gap-2 text-colors'
    }
    return 'incomplete flex flex-col items-center gap-2 text-[#888] dark:text-gray-500'
  })
})

const stepImages = computed(() => {
  return statusSteps.map((_, idx) => {
    if (idx <= currentStatusIndex.value) {
      return '/Assets/Checkmark.svg'
    }
    return '/Assets/NeutralCircle.svg'
  })
})

const statusTitle = computed(() => {
  const status = props.request?.status
  switch (status) {
    case 'PENDING':
      return isOwner.value ? 'Listed for Swap' : 'Pay for Shipping'
    case 'SHIPPINGPAID':
      return 'Shipping Paid'
    case 'SHIPPED':
      return 'Book Shipped'
    case 'DELIVERED':
      return 'Book Delivered'
    case 'COMPLETED':
      return 'Swap Completed'
    case 'TAKENDOWN':
      return 'Post Taken Down'
    default:
      return ''
  }
})

const statusDescription = computed(() => {
  const status = props.request?.status
  if (status === 'PENDING') {
    return isOwner.value
      ? 'Your book is available. Waiting for someone to request it.'
      : 'Complete payment to proceed with your book swap request.'
  } else if (status === 'SHIPPINGPAID') {
    return isOwner.value
      ? 'Download your shipping label and ship the book.'
      : `Waiting for ${props.request?.owner?.fname} to ship your book.`
  } else if (status === 'SHIPPED') {
    return isOwner.value
      ? `Your book is on its way to ${props.request?.requester?.fname}.`
      : 'Your book is on its way! Confirm delivery when it arrives.'
  } else if (status === 'DELIVERED') {
    return isOwner.value
      ? `${props.request?.requester?.fname} has received the book.`
      : 'Mark as completed to finish the swap.'
  } else if (status === 'COMPLETED') {
    return 'This book swap has been successfully completed.'
  } else if (status === 'TAKENDOWN') {
    return 'This book swap post has been taken down.'
  }
  return ''
})

const handleStatusUpdate = async (newStatus) => {
  errorMessage.value = null
  successMessage.value = null
  
  // Authorization check for TAKENDOWN status
  if (newStatus === 'TAKENDOWN' && !isOwner.value) {
    errorMessage.value = 'Unauthorized to take down the book post.'
    return
  }

  isUpdating.value = true
  try {
    await bookRequestsStore.updateRequestStatus(props.request.id, newStatus)
    successMessage.value = 'Status updated successfully!'
    setTimeout(() => {
      successMessage.value = null
    }, 3000)
  } catch (err) {
    console.error('Error updating status:', err)
    errorMessage.value = err.response?.data?.error || err.message || 'Failed to update status'
  } finally {
    isUpdating.value = false
  }
}

const handleTakedownPost = async () => {
  await handleStatusUpdate('TAKENDOWN')
}

const handlePayShipping = () => {
  router.push(`/checkout?requestId=${props.request.id}`)
}

const handleCancelRequest = () => {
  emit('cancel-request', props.request.id)
}

const handleReportIssue = () => {
  emit('report-issue', props.request.id)
}
</script>

<template>
  <div
    class="book-container flex w-full max-w-3xl flex-col items-center gap-6 rounded-xl border border-[#ccc] bg-colors p-4 dark:border-[#2C3233]"
    :class="flexReverse"
  >
    <!-- Book Display -->
    <div class="w-min">
      <BookPostCard :book="request.book" :loggedInUserId="authStore.user?.id" />
      
    </div>

    <!-- Request Details -->
    <div class="flex flex-col items-center w-full">
      <div class="w-full">
        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-100 rounded">
          {{ errorMessage }}
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-100 rounded">
          {{ successMessage }}
        </div>

        <!-- Title and Description -->
        <h2 class="mb-2 text-2xl font-bold text-colors">{{ statusTitle }}</h2>
        <p class="mb-4 text-[#555] dark:text-gray-400">{{ statusDescription }}</p>

        <!-- Progress Bar -->
        <div class="request-progress-bar">
          <div class="flex flex-row items-center justify-between gap-5">
            <template v-if="request.status !== 'TAKENDOWN'">
              <div v-for="(step, idx) in statusSteps" :key="idx" :class="stepStyles[idx]">
                <div class="step-number">
                  <img :src="stepImages[idx]" :alt="step" />
                </div>
                <div class="step-label whitespace-nowrap text-xs">
                  {{ step.replace(/([A-Z])/g, ' $1').trim() }}
                </div>
              </div>
            </template>

            <template v-else>
              <div class="step incomplete flex flex-col items-center gap-2 text-[#888] dark:text-gray-500">
                <div class="step-number">
                  <img src="/Assets/Cross.svg" alt="Taken Down" />
                </div>
                <div class="step-label">Taken Down</div>
              </div>
            </template>
          </div>

          <!-- Buttons -->
          <div class="buttons flex flex-wrap gap-3 border-t border-[#ccc] pt-2 dark:border-[#2C3233] mt-2">
            <!-- PENDING Status -->
            <template v-if="request.status === 'PENDING'">
              <template v-if="isOwner">
                <button
                  type="button"
                  :disabled="isUpdating"
                  class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleTakedownPost"
                >
                  {{ isUpdating ? 'Updating...' : 'Take down post' }}
                </button>
              </template>
              <template v-else>
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handlePayShipping"
                >
                  Pay for Shipping
                </button>
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleCancelRequest"
                >
                  Cancel request
                </button>
              </template>
            </template>

            <!-- SHIPPINGPAID Status -->
            <template v-else-if="request.status === 'SHIPPINGPAID'">
              <template v-if="isOwner">
                <a
                  :href="`/shipping-label/${request.id}`"
                  class="inline-flex items-center justify-center rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                >
                  Download Label
                </a>
                <button
                  type="button"
                  :disabled="isUpdating"
                  class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleStatusUpdate('SHIPPED')"
                >
                  {{ isUpdating ? 'Updating...' : 'Mark as shipped' }}
                </button>
              </template>
              <template v-else>
                <span
                  class="inline-flex items-center rounded-full border border-blue-500/40 bg-blue-500/20 px-4 py-2 text-sm font-semibold text-blue-300"
                >
                  Waiting for shipment...
                </span>
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleCancelRequest"
                >
                  Cancel request
                </button>
              </template>
            </template>

            <!-- SHIPPED Status -->
            <template v-else-if="request.status === 'SHIPPED'">
              <template v-if="isOwner">
                <span
                  class="inline-flex items-center rounded-full border border-blue-400 bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700 dark:border-blue-500/40 dark:bg-blue-500/20 dark:text-blue-300"
                >
                  In transit...
                </span>
              </template>
              <template v-else>
                <button
                  type="button"
                  :disabled="isUpdating"
                  class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleStatusUpdate('DELIVERED')"
                >
                  {{ isUpdating ? 'Updating...' : 'Confirm delivery' }}
                </button>
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-lg border border-red-500/60 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleReportIssue"
                >
                  Report issue
                </button>
              </template>
            </template>

            <!-- DELIVERED Status -->
            <template v-else-if="request.status === 'DELIVERED'">
              <template v-if="isOwner">
                <span
                  class="inline-flex items-center rounded-full border border-emerald-400 bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/20 dark:text-emerald-300"
                >
                  Delivered successfully
                </span>
              </template>
              <template v-else>
                <button
                  type="button"
                  :disabled="isUpdating"
                  class="inline-flex items-center justify-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-1 focus:ring-offset-[#0F0F0F]"
                  @click="handleStatusUpdate('COMPLETED')"
                >
                  {{ isUpdating ? 'Updating...' : 'Mark as completed' }}
                </button>
              </template>
            </template>

            <!-- COMPLETED Status -->
            <template v-else-if="request.status === 'COMPLETED'">
              <span
                class="inline-flex items-center rounded-full border border-emerald-400 bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/20 dark:text-emerald-300"
              >
                ✓ Swap Completed
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>