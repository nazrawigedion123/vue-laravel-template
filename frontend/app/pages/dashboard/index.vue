<template>
  <div class="dashboard-overview">
    <div class="welcome-banner">
      <h1>Welcome back, {{ user?.first_name || 'Admin' }}!</h1>
      <p>Here's what's happening with your platform today.</p>
    </div>

    <div class="stats-grid">
      <div v-for="stat in stats" :key="stat.label" class="stat-card">
        <div class="stat-icon" :style="{ backgroundColor: stat.color + '20', color: stat.color }">
          <span class="icon">{{ stat.icon }}</span>
        </div>
        <div class="stat-info">
          <p class="stat-label">{{ stat.label }}</p>
          <p v-if="isLoading" class="stat-value loading-skeleton">...</p>
          <p v-else class="stat-value">{{ stat.value }}</p>
        </div>
        <div class="stat-trend" :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">
          {{ stat.trend > 0 ? '↑' : '↓' }} {{ Math.abs(stat.trend) }}%
        </div>
      </div>
    </div>

    <div class="dashboard-sections">
      <section class="section">
        <h3>Recent Activity</h3>
        <div class="activity-list">
          <div v-for="i in 3" :key="i" class="activity-item">
            <div class="activity-dot"></div>
            <div class="activity-content">
              <p><strong>System</strong> updated security protocols</p>
              <span>2 hours ago</span>
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <h3>Quick Actions</h3>
        <div class="actions-buttons">
          <NuxtLink to="/dashboard/blogs" class="action-btn">Manage Blogs</NuxtLink>
          <button class="action-btn secondary">View Analytics</button>
          <button class="action-btn secondary">System Settings</button>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useBlogStore } from '~/store/blogStore'
import { storeToRefs } from 'pinia'

definePageMeta({
  layout: 'dashboard',
  middleware: ['admin']
})

const { user } = useAuth()

// 1. Initialize the blog store
const blogStore = useBlogStore()
const { managementMeta, publicMeta, isLoading } = storeToRefs(blogStore)

// 2. Fetch the meta counters on component mount
onMounted(async () => {
  // Pass page=1, size=1 to hit the endpoints efficiently without downloading large collections
  await Promise.all([
    blogStore.getBlogs(1, 10),        // Updates managementMeta (All drafts + published)
    blogStore.getPublishedBlogs(1, 10) // Updates publicMeta (Only published items)
  ])
})

// 3. Keep stats computed so they react automatically as meta variables populate
const stats = computed(() => [
  { 
    label: 'Total Blogs (Your Posts)', 
    value: managementMeta.value.totalItems.toString(), 
    icon: '📝', 
    color: '#3b82f6', 
    trend: 12 
  },
  { 
    label: 'Live Published Blogs', 
    value: publicMeta.value.totalItems.toString(), 
    icon: '🌐', 
    color: '#10b981', 
    trend: 5 
  },
  { 
    label: 'Comments', 
    value: '142', // Statically mocked until comment statistics endpoints exist
    icon: '💬', 
    color: '#8b5cf6', 
    trend: -2 
  },
  { 
    label: 'System Health', 
    value: '100%', 
    icon: '🛡️', 
    color: '#f59e0b', 
    trend: 0 
  },
])
</script>

<style scoped>
/* Optional styling enhancement for loading state */
.loading-skeleton {
  animation: pulse 1.5s infinite ease-in-out;
  color: #9ca3af !important;
}

@keyframes pulse {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}
</style>

<style scoped>
.dashboard-overview {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.welcome-banner h1 {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.welcome-banner p {
  color: var(--text-muted);
  font-size: 1.125rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: var(--card-bg);
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 1.25rem;
  position: relative;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
}

.stat-trend {
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 40px;
}

.trend-up {
  background-color: #f0fdf4;
  color: #166534;
}

.trend-down {
  background-color: #fef2f2;
  color: #991b1b;
}

.dashboard-sections {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.section {
  background: var(--card-bg);
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid var(--border-color);
}

.section h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.activity-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--color-primary);
  margin-top: 0.5rem;
}

.activity-content p {
  font-size: 0.9375rem;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.activity-content span {
  font-size: 0.8125rem;
  color: var(--text-muted);
}

.actions-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.action-btn {
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:not(.secondary) {
  background-color: var(--color-primary);
  color: white;
  border: none;
}

.action-btn.secondary {
  background-color: var(--bg-surface);
  color: var(--text-muted);
  border: 1px solid var(--border-color);
}

.action-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
</style>
