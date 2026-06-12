<template>
  <p v-if="message" class="dashboard-message" :class="`dashboard-message--${type}`">
    {{ message }}
  </p>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  message?: string | null
  type?: 'success' | 'error'
}>(), {
  message: null,
  type: 'success',
})
</script>

<style scoped>
.dashboard-message {
  border-radius: 8px;
  font-weight: 800;
  margin: 0;
  padding: 1rem;
}

.dashboard-message--error {
  background: var(--color-error-container);
  border: 1px solid var(--color-error);
  color: var(--color-on-error-container);
}

.dashboard-message--success {
  background: var(--color-success-container);
  border: 1px solid var(--color-success);
  color: var(--color-on-success-container);
}
</style>
