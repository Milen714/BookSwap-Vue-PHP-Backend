<script setup>
import AdminStatusBadge from '@/components/molecules/AdminStatusBadge.vue'

defineProps({
  users: {
    type: Array,
    required: true,
  },
  statusFilters: {
    type: Array,
    required: true,
  },
  searchQuery: {
    type: String,
    required: true,
  },
  userStatusFilter: {
    type: String,
    required: true,
  },
  actionLoadingId: {
    type: [Number, String, null],
    default: null,
  },
})

const emit = defineEmits(['update:search-query', 'update:user-status-filter', 'toggle-user'])

const formatDate = (value) => {
  if (!value) return '—'

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return '—'

  return new Intl.DateTimeFormat(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date)
}
</script>

<template>
  <section id="users" class="admin-panel-surface space-y-5 p-6 md:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <h2 class="text-2xl font-bold text-colors">User management</h2>
        <p class="mt-1 text-sm opacity-75">Search accounts and ban or unban users as needed.</p>
      </div>

      <div class="grid gap-3 md:min-w-[24rem]">
        <input
          :value="searchQuery"
          type="search"
          class="admin-search"
          placeholder="Search users by name or email"
          aria-label="Search users"
          @input="emit('update:search-query', $event.target.value)"
        />
        <div class="flex flex-wrap gap-2">
          <button
            v-for="filter in statusFilters"
            :key="filter.value"
            type="button"
            class="admin-filter-chip"
            :class="userStatusFilter === filter.value ? 'admin-filter-chip-active' : ''"
            @click="emit('update:user-status-filter', filter.value)"
          >
            {{ filter.label }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="users.length === 0" class="admin-empty-state">
      <p class="text-lg font-semibold text-colors">No users match the current filters.</p>
      <p class="mt-2 opacity-75">Try a different search term or show all users.</p>
    </div>

    <div v-else class="overflow-hidden rounded-2xl border border-black/10 dark:border-white/10">
      <div class="hidden overflow-x-auto xl:block">
        <table class="min-w-full divide-y divide-black/10 dark:divide-white/10">
          <thead class="bg-colors-secondary">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] opacity-70">User</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] opacity-70">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] opacity-70">Stats</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] opacity-70">Joined</th>
              <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.2em] opacity-70">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-black/10 bg-colors-secondary-light dark:divide-white/10">
            <tr
              v-for="user in users"
              :key="user.id"
              class="transition-colors hover:bg-black/5 dark:hover:bg-white/5"
            >
              <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-11 w-11 items-center justify-center rounded-full bg-colors-secondary text-sm font-bold uppercase text-colors">
                    {{ `${user.fname?.[0] || ''}${user.lname?.[0] || ''}` || 'U' }}
                  </div>
                  <div>
                    <p class="font-semibold text-colors">{{ user.fname }} {{ user.lname }}</p>
                    <p class="text-sm opacity-75">{{ user.email }}</p>
                    <p class="text-xs uppercase tracking-[0.18em] opacity-60">{{ user.role }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="flex flex-wrap gap-2">
                  <AdminStatusBadge :variant="Number(user.isActive) === 1 ? 'success' : 'danger'" :label="Number(user.isActive) === 1 ? 'Active' : 'Banned'" />
                  <AdminStatusBadge :variant="Number(user.isVerified) === 1 ? 'success' : 'neutral'" :label="Number(user.isVerified) === 1 ? 'Verified' : 'Unverified'" />
                </div>
              </td>
              <td class="px-4 py-4">
                <p class="text-sm font-medium text-colors">{{ user.swap_tokens ?? 0 }} swap tokens</p>
                <p class="text-sm opacity-75">{{ user.listed_books_count ?? 0 }} active listings</p>
                <p class="text-sm opacity-75">{{ user.swap_count ?? 0 }} swaps</p>
              </td>
              <td class="px-4 py-4 text-sm opacity-80">{{ formatDate(user.joined_at) }}</td>
              <td class="px-4 py-4 text-right">
                <button
                  type="button"
                  class="admin-action-button"
                  :class="Number(user.isActive) === 1 ? 'admin-action-button-danger' : 'admin-action-button-success'"
                  :disabled="actionLoadingId === user.id"
                  @click="emit('toggle-user', user)"
                >
                  <span v-if="actionLoadingId === user.id" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                  <i v-else :class="Number(user.isActive) === 1 ? 'pi pi-ban' : 'pi pi-check'"></i>
                  {{ Number(user.isActive) === 1 ? 'Ban' : 'Unban' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="grid gap-4 xl:hidden">
        <article
          v-for="user in users"
          :key="user.id"
          class="rounded-2xl border border-black/10 bg-colors-secondary-light p-4 shadow-sm dark:border-white/10"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="flex h-11 w-11 items-center justify-center rounded-full bg-colors-secondary text-sm font-bold uppercase text-colors">
                {{ `${user.fname?.[0] || ''}${user.lname?.[0] || ''}` || 'U' }}
              </div>
              <div>
                <h3 class="font-semibold text-colors">{{ user.fname }} {{ user.lname }}</h3>
                <p class="text-sm opacity-75">{{ user.email }}</p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <AdminStatusBadge :variant="Number(user.isActive) === 1 ? 'success' : 'danger'" :label="Number(user.isActive) === 1 ? 'Active' : 'Banned'" />
              <button
                type="button"
                class="admin-action-button"
                :class="Number(user.isActive) === 1 ? 'admin-action-button-danger' : 'admin-action-button-success'"
                :disabled="actionLoadingId === user.id"
                @click="emit('toggle-user', user)"
              >
                <span v-if="actionLoadingId === user.id" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                <i v-else :class="Number(user.isActive) === 1 ? 'pi pi-ban' : 'pi pi-check'"></i>
                {{ Number(user.isActive) === 1 ? 'Ban' : 'Unban' }}
              </button>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl bg-colors-secondary p-3">
              <p class="opacity-70">Tokens</p>
              <p class="mt-1 font-semibold">{{ user.swap_tokens ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-colors-secondary p-3">
              <p class="opacity-70">Listings</p>
              <p class="mt-1 font-semibold">{{ user.listed_books_count ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-colors-secondary p-3">
              <p class="opacity-70">Swaps</p>
              <p class="mt-1 font-semibold">{{ user.swap_count ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-colors-secondary p-3">
              <p class="opacity-70">Joined</p>
              <p class="mt-1 font-semibold">{{ formatDate(user.joined_at) }}</p>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
