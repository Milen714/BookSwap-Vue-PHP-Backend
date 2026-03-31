<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
const authStore = useAuthStore()
const emit = defineEmits(['open-chat'])
const props = defineProps({
    book: {
        type: Object,
        required: true
    },
    isLoggedIn: {
        type: Boolean,
        required: true
    }
})

const openChat = () => {
  emit('open-chat', props.book.shared_by)
  console.log('from child component, open chat with owner', props.book.shared_by)
}
</script>

<template>
    <div class="flex flex-col justify-between gap-4 md:flex-row items-center">
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

              <div class="flex flex-col items-start gap-4">
                <RouterLink
                  :to="`/profile/${book?.shared_by?.id}`"
                  class="inline-flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900 transition duration-200 group"
                >
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="pi pi-user text-white text-sm"></i>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wide">Shared by</span>
                    <span class="text-md font-semibold text-blue-700 dark:text-blue-300 group-hover:text-blue-900 dark:group-hover:text-blue-200">
                      {{ book?.shared_by?.fname || '' }} {{ book?.shared_by?.lname || '' }}
                    </span>
                  </div>
                  <i class="pi pi-arrow-right text-blue-600 dark:text-blue-400 ml-2 group-hover:translate-x-1 transition-transform"></i>
                </RouterLink>
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
              <button 
              v-if="isLoggedIn && book?.shared_by.id !== authStore.user?.id"
              class="button_primary mt-2" 
              type="button"
              @click="openChat" 
              >Message Owner</button>
            </div>
          </div>
        </div>
</template>