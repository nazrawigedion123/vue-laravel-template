<template>
  <div class="layout-dashboard">
    <aside class="sidebar">
      <div class="sidebar-brand">
        Dashboard
      </div>
      <nav class="sidebar-nav">
        <NuxtLink to="/dashboard" class="nav-item">Overview</NuxtLink>
        <NuxtLink to="/dashboard/blogs" class="nav-item">Blog Management</NuxtLink>
        <NuxtLink to="/" class="nav-item">Back to Site</NuxtLink>
      </nav>
      <div class="sidebar-footer">
        <button @click="logout" class="logout-btn">Log Out</button>
      </div>
    </aside>
    
    <div class="dashboard-main">
      <header class="dashboard-header">
        <div class="header-actions">
          <div class="language-selector">
            <select :value="languageStore.currentLanguagePreference" @change="handleLanguageChange" class="dashboard-select">
              <option value="en">EN</option>
              <option value="am">AM</option>
            </select>
          </div>
          <button @click="themeStore.toggleTheme" class="dashboard-theme-toggle">
            {{ themeStore.currentTheme === 'dark' ? '☀️' : '🌙' }}
          </button>
          <div class="header-user">
            {{ user?.email }}
          </div>
        </div>
      </header>

      <main class="dashboard-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useThemeStore } from '~/store/themeStore';
import { useLanguageStore } from '~/store/languageStore';

const { user, logout } = useAuth()
const themeStore = useThemeStore()
const languageStore = useLanguageStore()

// Language loading logic (minimal for dashboard)
const handleLanguageChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;
  languageStore.setLanguagePreference(target.value, 'dashboard');
};
</script>


<style scoped>
.layout-dashboard {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  width: 260px;
  background-color: var(--color-surface);
  color: var(--color-on-surface);
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  border-right: 1px solid var(--color-outline);
}

.sidebar-brand {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: var(--color-primary);
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

.nav-item {
  color: var(--color-on-surface-muted);
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-item:hover, .router-link-active {
  background-color: var(--color-primary);
  color: var(--color-on-primary);
}

.sidebar-footer {
  padding-top: 1rem;
  border-top: 1px solid var(--color-outline);
}

.logout-btn {
  width: 100%;
  background-color: var(--color-error);
  color: var(--color-on-error);
  border: none;
  padding: 0.75rem;
  border-radius: 6px;
  cursor: pointer;
}

.dashboard-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  overflow-y: auto;
}

.dashboard-header {
  height: 64px;
  background-color: var(--color-background);
  border-bottom: 1px solid var(--color-outline);
  display: flex;
  align-items: center;
  padding: 0 2rem;
  justify-content: flex-end;
  transition: background-color 0.3s ease;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.dashboard-select {
  background-color: var(--color-surface);
  color: var(--color-on-surface);
  border: 1px solid var(--color-outline);
  border-radius: 4px;
  padding: 4px 8px;
  cursor: pointer;
}

.dashboard-theme-toggle {
  background: none;
  border: 1px solid var(--color-outline);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background-color: var(--color-surface);
  color: var(--color-on-surface);
  font-size: 1rem;
}

.header-user {
  color: var(--color-on-background-muted);
  font-size: 0.875rem;
  font-weight: 500;
}


</style>
