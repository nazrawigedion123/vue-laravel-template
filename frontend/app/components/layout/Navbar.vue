<template>
  <header class="navbar">
    <div class="container">
      <NuxtLink to="/" class="brand">
        <span class="brand-text">VueTemplate</span>
      </NuxtLink>

      <nav class="nav-links">
        <NuxtLink to="/" class="nav-link">Home</NuxtLink>
        <NuxtLink v-if="hasDashboardAccess" to="/dashboard" class="nav-link dashboard-link">Dashboard</NuxtLink>
      </nav>

      <div class="actions">
        <template v-if="!isAuthenticated">
          <NuxtLink to="accounts/login" class="btn btn-ghost">Log In</NuxtLink>
          <NuxtLink to="accounts/register" class="btn btn-primary">Sign Up</NuxtLink>
        </template>
        <template v-else>
          <div class="user-profile">
            <span class="user-email">{{ user?.email }}</span>
            <button @click="logout" class="btn btn-outline btn-sm">Log Out</button>
          </div>
        </template>
        <div class="theme-toggle">
          <button @click="themeStore.toggleTheme" class="btn btn-ghost theme-btn" aria-label="Toggle Theme">
            <span v-if="themeStore.currentTheme === 'dark'">☀️ Light</span>
            <span v-else>🌙 Dark</span>
          </button>
        </div>

      </div>
    </div>
  </header>
</template>

<script setup>
import { useThemeStore } from '~/store/theme';

const { isAuthenticated, hasDashboardAccess, user, logout } = useAuth()
const themeStore = useThemeStore();
</script>

<style scoped>
.navbar {
  background-color: var(--navbar-bg);
  border-bottom: 1px solid var(--border-color);
  height: 72px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  padding: 0 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand {
  text-decoration: none;
  display: flex;
  align-items: center;
}

.brand-text {
  font-size: 1.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, var(--color-primary) 0%, #2563eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: var(--text-muted);
  font-weight: 500;
  transition: color 0.2s;
}

.nav-link:hover, .router-link-active {
  color: var(--color-primary);
}

.dashboard-link {
  font-weight: 600;
}

.actions {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.theme-toggle {
  display: flex;
  align-items: center;
}

.theme-btn {
  padding: 0.5rem;
  min-width: 80px;
  justify-content: center;
  font-size: 0.875rem;
  border: 1px solid var(--border-color);
  background-color: var(--bg-surface);
  color: var(--text-primary);
}

.user-profile {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.user-email {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
}

.btn-sm {
  padding: 0.25rem 0.75rem;
  font-size: 0.875rem;
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
  border: none;
}

.btn-primary:hover {
  filter: brightness(1.1);
}

.btn-ghost {
  color: var(--text-muted);
  background: transparent;
  border: none;
}

.btn-ghost:hover {
  background-color: var(--bg-surface);
  color: var(--text-primary);
}

.btn-outline {
  border: 1px solid var(--border-color);
  background: transparent;
  color: #ef4444;
}

.btn-outline:hover {
  background-color: #fef2f2;
}

</style>