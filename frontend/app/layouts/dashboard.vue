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
        <div class="header-user">
          {{ user?.email }}
        </div>
      </header>
      <main class="dashboard-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
const { user, logout } = useAuth()
</script>

<style scoped>
.layout-dashboard {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.sidebar {
  width: 260px;
  background-color: #1e293b;
  color: white;
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
}

.sidebar-brand {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #38bdf8;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

.nav-item {
  color: #cbd5e1;
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  transition: all 0.2s;
}

.nav-item:hover, .router-link-active {
  background-color: #334155;
  color: white;
}

.sidebar-footer {
  padding-top: 1rem;
  border-top: 1px solid #334155;
}

.logout-btn {
  width: 100%;
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 6px;
  cursor: pointer;
}

.dashboard-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background-color: #f8fafc;
  overflow-y: auto;
}

.dashboard-header {
  height: 64px;
  background-color: white;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  padding: 0 2rem;
  justify-content: flex-end;
}

.dashboard-content {
  padding: 2rem;
}
</style>
