<script setup>
import { RouterLink, useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth.js'

defineProps({
  basePath: {
    type: String,
    required: true,
  },
  filters: {
    type: Array,
    default: () => [
      { label: 'All', status: 'all' },
      { label: 'In Progress', status: 'inProgress' },
      { label: 'Completed', status: 'completed' },
    ],
  },
})

const { authState } = useAuth()
const route = useRoute()

const activeStyle = "bg-[#d5d5d5] dark:bg-[#0F0F0F] rounded-full";
const isActive = (status) => {
  return route.query.status === status
}
</script>

<template>
    <ul
            class="flex flex-row justify-center gap-2 bg-[#e5e5e5] dark:bg-[#222222] px-1 py-1 rounded-full whitespace-nowrap">
            <li v-for="filter in filters"
                :key="filter.status"
                :class="isActive(filter.status) ? activeStyle : ''" 
                class="px-4 py-2">
                <RouterLink :to="`${basePath}?id=${authState.user?.id}&status=${filter.status}`"
                    class="text-colors font-semibold hover:underline">{{ filter.label }}</RouterLink>
            </li>
        </ul>
</template>