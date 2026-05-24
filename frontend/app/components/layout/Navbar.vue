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
          <NuxtLink to="/login" class="btn btn-ghost">Log In</NuxtLink>
          <NuxtLink to="/register" class="btn btn-primary">Sign Up</NuxtLink>
        </template>
        <template v-else>
          <div class="user-profile">
            <span class="user-email">{{ user?.email }}</span>
            <button @click="logout" class="btn btn-outline btn-sm">Log Out</button>
          </div>
        </template>
      </div>
    </div>
  </header>
</template>

<script setup>
const { isAuthenticated, hasDashboardAccess, user, logout } = useAuth()
</script>

<style scoped>
.navbar {
  background-color: white;
  border-bottom: 1px solid #e2e8f0;
  height: 72px;
  display: flex;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
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
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #64748b;
  font-weight: 500;
  transition: color 0.2s;
}

.nav-link:hover, .router-link-active {
  color: #2563eb;
}

.dashboard-link {
  font-weight: 600;
}

.actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.user-profile {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.user-email {
  font-size: 0.875rem;
  color: #64748b;
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
  background-color: #2563eb;
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-ghost {
  color: #64748b;
  background: transparent;
  border: none;
}

.btn-ghost:hover {
  background-color: #f1f5f9;
  color: #1e293b;
}

.btn-outline {
  border: 1px solid #e2e8f0;
  background: transparent;
  color: #ef4444;
}

.btn-outline:hover {
  background-color: #fef2f2;
  border-color: #fecaca;
}
</style>