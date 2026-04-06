<script setup>
import { computed } from 'vue'
import AdminSummaryCard from '@/components/molecules/AdminSummaryCard.vue'
import AdminStatusBadge from '@/components/molecules/AdminStatusBadge.vue'

const props = defineProps({
  summary: {
    type: Object,
    required: true,
  },
  analytics: {
    type: Object,
    required: true,
  },
})

const maxMonthlyTotal = computed(() => {
  const totals = (Array.isArray(props.analytics?.monthlyTrend) ? props.analytics.monthlyTrend : []).map((trend) => Number(trend.total || 0))
  return Math.max(...totals, 1)
})

const statusMeta = (status) => {
  const normalized = String(status || '').toUpperCase()

  if (normalized === 'COMPLETED') return { label: 'Completed', variant: 'success' }
  if (normalized === 'TAKENDOWN') return { label: 'Taken down', variant: 'danger' }
  if (normalized === 'PENDING') return { label: 'Pending', variant: 'warning' }
  if (normalized === 'SHIPPINGPAID' || normalized === 'SHIPPED' || normalized === 'DELIVERED') {
    return { label: normalized, variant: 'neutral' }
  }
  return { label: normalized || 'Unknown', variant: 'neutral' }
}

const formatMonth = (value) => {
  if (!value) return '—'

  const date = new Date(`${value}-01`)
  if (Number.isNaN(date.getTime())) return value

  return new Intl.DateTimeFormat(undefined, {
    month: 'short',
    year: 'numeric',
  }).format(date)
}
</script>

<template>
  <section id="swaps" class="admin-panel-surface space-y-5 p-6 md:p-8">
    <div>
      <h2 class="text-2xl font-bold text-colors">Swap analytics</h2>
      <p class="mt-1 text-sm opacity-75">
        Monitor the health of the exchange system and highlight the most active genres.
      </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <AdminSummaryCard label="Total swaps" :value="props.summary.totalSwaps ?? 0" />
      <AdminSummaryCard label="Pending" :value="props.summary.pendingSwaps ?? 0" />
      <AdminSummaryCard label="In progress" :value="(props.summary.shippingPaidSwaps ?? 0) + (props.summary.shippedSwaps ?? 0) + (props.summary.deliveredSwaps ?? 0)" />
      <AdminSummaryCard label="Completed" :value="props.summary.completedSwaps ?? 0" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-colors">Status breakdown</h3>
        <div v-if="props.analytics.statusBreakdown.length === 0" class="admin-empty-state">No swap data available yet.</div>
        <div v-else class="space-y-3">
          <div
            v-for="item in props.analytics.statusBreakdown"
            :key="item.status"
            class="rounded-2xl border border-black/10 bg-colors-secondary-light p-4 dark:border-white/10"
          >
            <div class="flex items-center justify-between gap-4">
              <AdminStatusBadge :variant="statusMeta(item.status).variant" :label="statusMeta(item.status).label" />
              <span class="text-sm font-semibold text-colors">{{ item.total }}</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-black/10 dark:bg-white/10">
              <div
                class="h-full rounded-full bg-current opacity-70"
                :style="{ width: `${Math.max(8, ((item.total / (props.summary.totalSwaps || 1)) * 100))}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-colors">Monthly swap trend</h3>
        <div v-if="props.analytics.monthlyTrend.length === 0" class="admin-empty-state">No monthly trend data available yet.</div>
        <div v-else class="space-y-3">
          <div
            v-for="item in props.analytics.monthlyTrend"
            :key="item.month"
            class="rounded-2xl border border-black/10 bg-colors-secondary-light p-4 dark:border-white/10"
          >
            <div class="flex items-center justify-between text-sm">
              <span class="opacity-75">{{ formatMonth(item.month) }}</span>
              <span class="font-semibold text-colors">{{ item.total }}</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-black/10 dark:bg-white/10">
              <div
                class="h-full rounded-full bg-current opacity-70"
                :style="{ width: `${Math.max(8, ((item.total / maxMonthlyTotal) * 100))}%` }"
              ></div>
            </div>
          </div>
        </div>

        <div>
          <h3 class="mb-4 text-lg font-semibold text-colors">Top genres by swaps</h3>
          <div v-if="props.analytics.genreBreakdown.length === 0" class="admin-empty-state">No genre analytics available yet.</div>
          <div v-else class="flex flex-wrap gap-2">
            <AdminStatusBadge
              v-for="item in props.analytics.genreBreakdown"
              :key="item.genre"
              variant="neutral"
              :label="`${item.genre} · ${item.total}`"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
