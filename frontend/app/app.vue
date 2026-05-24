<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
// Main entry point for the application
const { loadUser } = useAuth()

// Initialize auth state on mount
onMounted(async () => {
  await loadUser()
})
</script>

<style>
/* Global styles can be placed here or in assets/css/main.css */
body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: #1e293b;
  background-color: #f8fafc;
}

* {
  box-sizing: border-box;
}

/* Base button styles if reused */
.btn {
  font-family: inherit;
}
</style>
