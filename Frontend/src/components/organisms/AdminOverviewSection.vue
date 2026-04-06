<script setup>
import AdminSummaryCard from '@/components/molecules/AdminSummaryCard.vue'

defineProps({
  summary: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['refresh'])
</script>

<template>
  <section id="overview" class="space-y-4">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-colors">Overview</h2>
        <p class="mt-1 text-sm opacity-75">High-level numbers for users, listings, and swaps.</p>
      </div>
      <button type="button" class="admin-action-button" @click="emit('refresh')">
        <i class="pi pi-refresh"></i>
        Refresh
      </button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <AdminSummaryCard label="Total users" :value="summary.totalUsers ?? 0" />
      <AdminSummaryCard label="Active users" :value="summary.activeUsers ?? 0" />
      <AdminSummaryCard label="Completed swaps" :value="summary.completedSwaps ?? 0" />
      <AdminSummaryCard label="Taken down swaps" :value="summary.takenDownSwaps ?? 0" />
    </div>
  </section>
</template>
