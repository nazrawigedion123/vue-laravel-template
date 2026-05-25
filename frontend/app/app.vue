<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
import { useThemeStore } from '~/store/themeStore';

const { loadUser } = useAuth()
const themeStore = useThemeStore()

// Initialize auth and theme on mount
onMounted(async () => {
  await loadUser()
  themeStore.applyThemeVariables(themeStore.currentTheme)
})
</script>

<style>
/* Global styles can be placed here or in assets/css/main.css */
body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: var(--text-primary);
  background-color: var(--bg-primary);
  transition: background-color 0.3s ease, color 0.3s ease;
}


* {
  box-sizing: border-box;
}

/* Base button styles if reused */
.btn {
  font-family: inherit;
}
</style>
