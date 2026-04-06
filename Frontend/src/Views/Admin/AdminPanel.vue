<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth.js'
import { useAdminStore } from '@/stores/admin.js'
import AdminPanelSidebar from '@/components/organisms/AdminPanelSidebar.vue'
import AdminPanelHeader from '@/components/organisms/AdminPanelHeader.vue'
import AdminOverviewSection from '@/components/organisms/AdminOverviewSection.vue'
import AdminUsersSection from '@/components/organisms/AdminUsersSection.vue'
import AdminSwapsSection from '@/components/organisms/AdminSwapsSection.vue'
import AdminDashboardErrorCard from '@/components/organisms/AdminDashboardErrorCard.vue'

const authStore = useAuthStore()
const adminStore = useAdminStore()

const activeSection = ref('overview')
const searchQuery = ref('')
const userStatusFilter = ref('all')

const statusFilters = [
    { label: 'All users', value: 'all' },
    { label: 'Active', value: 'active' },
    { label: 'Banned', value: 'banned' },
    { label: 'Verified', value: 'verified' },
]

const sectionLabel = computed(() => {
    if (activeSection.value === 'users') return 'User management'
    if (activeSection.value === 'swaps') return 'Swap analytics'
    return 'Dashboard overview'
})

const completionRate = computed(() => {
    const total = Number(adminStore.summary.totalSwaps || 0)
    const completed = Number(adminStore.summary.completedSwaps || 0)

    if (!total) return 0
    return Math.round((completed / total) * 100)
})

const filteredUsers = computed(() => {
    const query = searchQuery.value.trim().toLowerCase()

    return adminStore.users.filter((user) => {
        const fullName = `${user.fname || ''} ${user.lname || ''}`.trim().toLowerCase()
        const email = String(user.email || '').toLowerCase()
        const matchesQuery = !query || fullName.includes(query) || email.includes(query)

        const matchesStatus =
            userStatusFilter.value === 'all' ||
            (userStatusFilter.value === 'active' && Number(user.isActive) === 1) ||
            (userStatusFilter.value === 'banned' && Number(user.isActive) === 0) ||
            (userStatusFilter.value === 'verified' && Number(user.isVerified) === 1)

        return matchesQuery && matchesStatus
    })
})

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

const lastUpdatedLabel = computed(() => {
    return adminStore.lastUpdated ? formatDate(adminStore.lastUpdated) : 'Just now'
})

const setSection = (section) => {
    activeSection.value = section

    const element = document.getElementById(section)
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

const handleToggleUser = async (user) => {
    const nextState = Number(user.isActive) !== 1
    const fullName = `${user.fname || ''} ${user.lname || ''}`.trim()
    const confirmMessage = nextState
        ? `Unban ${fullName}?`
        : `Ban ${fullName}?`

    if (!window.confirm(confirmMessage)) {
        return
    }

    await adminStore.toggleUserStatus(user.id, nextState)
}

onMounted(async () => {
    await adminStore.fetchDashboardData()
})
</script>

<template>
    <section class="admin-shell mx-auto flex max-w-7xl flex-col gap-6 px-4 py-6 lg:flex-row lg:px-6">
        <AdminPanelSidebar
            :active-section="activeSection"
            :summary="adminStore.summary"
            @select="setSection"
        />

        <div class="flex-1 space-y-6">
            <AdminPanelHeader
                :section-label="sectionLabel"
                :user-name="authStore.user?.fname || ''"
                :completion-rate="completionRate"
                :last-updated-label="lastUpdatedLabel"
            />

            <AdminDashboardErrorCard
                v-if="adminStore.error"
                :message="adminStore.error"
                @retry="adminStore.fetchDashboardData()"
            />

            <div v-else-if="adminStore.loading" class="admin-panel-surface flex items-center justify-center p-12">
                <div class="flex items-center gap-3 opacity-80">
                    <div class="h-6 w-6 animate-spin rounded-full border-2 border-current border-t-transparent"></div>
                    <span class="text-sm font-medium">Loading admin dashboard…</span>
                </div>
            </div>

            <template v-else>
                <AdminOverviewSection :summary="adminStore.summary" @refresh="adminStore.fetchDashboardData()" />

                <AdminUsersSection
                    :users="filteredUsers"
                    :status-filters="statusFilters"
                    :search-query="searchQuery"
                    :user-status-filter="userStatusFilter"
                    :action-loading-id="adminStore.actionLoadingId"
                    @update:search-query="searchQuery = $event"
                    @update:user-status-filter="userStatusFilter = $event"
                    @toggle-user="handleToggleUser"
                />

                <AdminSwapsSection :summary="adminStore.summary" :analytics="adminStore.analytics" />
            </template>
        </div>
    </section>
</template>