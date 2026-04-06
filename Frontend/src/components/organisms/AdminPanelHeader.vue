<script setup>
import AdminSummaryCard from '@/components/molecules/AdminSummaryCard.vue'

defineProps({
  sectionLabel: {
    type: String,
    required: true,
  },
  userName: {
    type: String,
    default: '',
  },
  completionRate: {
    type: Number,
    default: 0,
  },
  lastUpdatedLabel: {
    type: String,
    default: 'Just now',
  },
})
</script>

<template>
  <header class="admin-panel-surface p-6 md:p-8">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
      <div class="min-w-0">
        <p class="text-xs uppercase tracking-[0.35em] opacity-70">Admin dashboard</p>
        <h1 class="mt-2 break-words text-3xl font-bold text-colors md:text-4xl">
          {{ sectionLabel }}
        </h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 opacity-80 md:text-base">
          Welcome{{ userName ? `, ${userName}` : '' }}. Review account activity,
          ban or unban users, and track how book swaps are performing across the platform.
        </p>
      </div>

      <div class="grid w-full shrink-0 gap-3 sm:grid-cols-2 xl:w-auto xl:min-w-[360px]">
        <AdminSummaryCard label="Completion rate" :value="`${completionRate}%`" valueClass="text-2xl" />
        <AdminSummaryCard label="Last updated" :value="lastUpdatedLabel" valueClass="text-base" />
      </div>
    </div>
  </header>
</template>
