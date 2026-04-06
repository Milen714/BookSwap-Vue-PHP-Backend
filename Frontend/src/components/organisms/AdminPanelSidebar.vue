<script setup>
import { computed } from 'vue'

const props = defineProps({
  activeSection: {
    type: String,
    default: 'overview',
  },
  summary: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['select'])

const sections = [
  {
    id: 'overview',
    icon: 'pi pi-chart-bar',
    label: 'Dashboard',
    description: 'Snapshot of users and swap activity',
  },
  {
    id: 'users',
    icon: 'pi pi-users',
    label: 'Users',
    description: 'Ban or unban accounts',
  },
  {
    id: 'swaps',
    icon: 'pi pi-sync',
    label: 'Swap analytics',
    description: 'Review swap trends and status',
  },
]

const healthStats = computed(() => [
  {
    label: 'Total users',
    value: props.summary.totalUsers ?? 0,
  },
  {
    label: 'Active users',
    value: props.summary.activeUsers ?? 0,
  },
  {
    label: 'Completed swaps',
    value: props.summary.completedSwaps ?? 0,
  },
])
</script>

<template>
  <aside class="admin-panel-surface w-full shrink-0 p-5 md:sticky md:top-24 md:w-80 lg:w-96">
    <header class="mb-6">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-colors-secondary">
          <i class="pi pi-shield text-xl text-colors"></i>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.3em] opacity-70">Admin console</p>
          <h2 class="text-2xl font-bold text-colors">Management Hub</h2>
        </div>
      </div>
      <p class="mt-4 text-sm leading-6 opacity-80">
        Manage users and monitor book swap activity from a single responsive dashboard.
      </p>
    </header>

    <nav class="space-y-2" aria-label="Admin dashboard sections">
      <button
        v-for="section in sections"
        :key="section.id"
        type="button"
        class="admin-nav-button"
        :class="activeSection === section.id ? 'admin-nav-button-active' : ''"
        @click="emit('select', section.id)"
      >
        <div class="flex items-start gap-3">
          <i :class="section.icon" class="mt-1 text-lg opacity-80"></i>
          <div>
            <div class="font-semibold text-colors">{{ section.label }}</div>
            <div class="text-sm opacity-75">{{ section.description }}</div>
          </div>
        </div>
      </button>
    </nav>

    <div class="mt-6 grid gap-3">
      <div v-for="stat in healthStats" :key="stat.label" class="admin-stat-card">
        <p class="text-sm opacity-75">{{ stat.label }}</p>
        <p class="mt-1 text-2xl font-bold text-colors">{{ stat.value }}</p>
      </div>
    </div>
  </aside>
</template>
