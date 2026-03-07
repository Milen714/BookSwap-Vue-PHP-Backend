<script setup>
import { RouterLink } from 'vue-router'
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
              <button 
              v-if="isLoggedIn"
              class="button_primary mt-2" 
              type="button"
              @click="openChat" 
              >Message Owner</button>
            </div>
          </div>
        </div>
</template>