<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  book: {
    type: Object,
    required: true,
  },
  requestId: {
    type: [Number, String],
    default: null,
  },
  loggedInUserId: {
    type: [Number, String],
    default: null,
  },
})

const emit = defineEmits(['quick-view', 'get-book', 'request-actions', 'flip'])

const route = useRoute()
const isFlipped = ref(false)

const fallbackImage = '/Assets/NoImagePlaceholder.png'

const coverImage = computed(() => {
  return props.book?.thumbnail_image_url || fallbackImage
})

const showGetBook = computed(() => {
  return route.path === '/' || route.query.genre !== undefined
})

const showRequestActions = computed(() => {
  return route.path.includes('/myRequests')
})

const conditionLabel = computed(() => {
  if (!props.book?.condition) return ''
  if (typeof props.book.condition === 'string') return props.book.condition
  if (props.book.condition.label) return props.book.condition.label
  return ''
})

const sharedByLabel = computed(() => {
  const first = props.book?.shared_by?.fname || ''
  const last = props.book?.shared_by?.lname || ''
  return `${first} ${last}`.trim()
})

const toggleFlip = () => {
  isFlipped.value = !isFlipped.value
  emit('flip', props.book)
}

const onQuickView = () => {
  toggleFlip()
  emit('quick-view', props.book)
}

const onGetBook = () => {
  emit('get-book', props.book)
}

const onRequestActions = () => {
  emit('request-actions', {
    userId: props.loggedInUserId,
    requestId: props.requestId,
    book: props.book,
  })
}
</script>

<template>
  <article class="flip-card shadow-md shadow-black/20 dark:shadow-white/20">
    <div class="card-flip-container">
      <div class="card-flip" :class="{ flip: isFlipped }">
        <div
          class="card-flip-front bookcard_body bg-cover bg-center"
          :style="{ backgroundImage: `url('${coverImage}')` }"
        >
          <div class="h-full flex flex-col justify-end">
            <div class="bg-[#ffffffcc] dark:bg-[#000000cc] w-full p-4 text-center rounded-bl-md rounded-br-md">
              <h4 class="text-md font-semibold text-colors">{{ book.title }}</h4>
              <div class="flex flex-row justify-end mt-2 gap-1">
                <button
                  class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors"
                  type="button"
                  @click="onQuickView"
                >
                  Quick View
                </button>

                <button
                  v-if="showGetBook"
                  class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors"
                  type="button"
                  @click="onGetBook"
                >
                  Get this book
                </button>

                <button
                  v-if="showRequestActions"
                  class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] hover:bg-[#d0d0d0] dark:hover:bg-[#2C3233] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors"
                  type="button"
                  @click="onRequestActions"
                >
                  Request Actions
                </button>
              </div>
            </div>
          </div>
        </div>

        <div
          class="card-flip-back bookcard_body bg-cover bg-center"
          :style="{ backgroundImage: `url('${coverImage}')` }"
        >
          <div class="bg-[#f2f0efe6] dark:bg-[#191b1de6] h-full flex flex-col justify-start rounded-md">
            <div class="w-full p-4 text-center rounded-bl-md rounded-br-md overflow-hidden h-full">
              <h4 class="text-md font-semibold text-colors">{{ book.title }}</h4>

              <div class="flex flex-row justify-between gap-1 mt-3">
                <span class="text-sm font-medium text-colors">{{ book.author }}</span>
                <span class="text-sm font-medium text-colors">{{ book.published_year }}</span>
                <span class="text-sm font-medium text-colors">{{ book.page_count }}</span>
              </div>

              <div class="flex flex-row justify-center mt-3">
                <span class="rounded-full bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors">
                  {{ book.genre }}
                </span>
              </div>

              <div class="flex flex-col justify-between">
                <div class="bookcard_description">
                  <p class="text-sm font-regular text-colors">{{ book.description }}</p>
                </div>

                <div class="flex flex-row justify-between items-center mt-4">
                  <span class="text-sm font-medium text-colors">{{ conditionLabel }}</span>
                  <span class="text-sm font-medium text-colors">{{ sharedByLabel }}</span>
                  <button
                    class="rounded-xl bg-[#e5e5e5] dark:bg-[#151819] border border-[#ccc] dark:border-[#2C3233] px-2 py-1 text-sm font-semibold text-colors"
                    type="button"
                    @click="toggleFlip"
                  >
                    Flip
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </article>
</template>
